<?php

namespace App\Services;

use App\Models\Call;
use App\Traits\Analyzable;

class CallAnalysisService
{
    use Analyzable;

    public function __construct(
        protected TranscriptionService $transcriptionService,
        protected LeadScoringService $leadScoringService,
        protected SentimentAnalysisService $sentimentAnalysisService
    ) {}

    public function process(Call $call): Call
    {
        $call->markAsProcessing();

        try {
            $transcriptionResult = $this->transcriptionService->transcribe($call->file_path);

            $analysisData = [
                'transcript' => $transcriptionResult['text'],
                'duration' => $transcriptionResult['duration'],
                'sentiment_score' => $transcriptionResult['sentiment_score'],
                'utterances' => $transcriptionResult['utterances'],
            ];

            $localSentiment = $this->sentimentAnalysisService->analyze($transcriptionResult['text']);

            $combinedSentimentScore = ($transcriptionResult['sentiment_score'] + $localSentiment['score']) / 2;
            $analysisData['sentiment_score'] = $combinedSentimentScore;

            $leadScore = $this->leadScoringService->calculateScore($analysisData);
            $eligibility = $this->leadScoringService->determineEligibility($leadScore, $analysisData);
            $nextActions = $this->leadScoringService->generateNextActions($leadScore, $eligibility, $analysisData);

            $keywords = $this->detectKeywords($transcriptionResult['text']);

            $call->update([
                'transcript' => $transcriptionResult['text'],
                'summary' => $this->generateSummary($transcriptionResult['text'], $keywords),
                'lead_score' => $leadScore,
                'eligibility' => $eligibility,
                'next_actions' => $nextActions,
                'keywords_detected' => $keywords,
                'sentiment' => $transcriptionResult['sentiment'],
                'sentiment_score' => $combinedSentimentScore,
                'duration_seconds' => $transcriptionResult['duration'],
            ]);

            $call->markAsCompleted();
        } catch (\Exception $e) {
            $call->markAsFailed($e->getMessage());
            throw $e;
        }

        return $call->fresh();
    }

    protected function generateSummary(string $transcript, array $keywords): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $transcript, -1, PREG_SPLIT_NO_EMPTY);

        if (count($sentences) <= 3) {
            return $transcript;
        }

        $scoredSentences = [];

        foreach ($sentences as $index => $sentence) {
            $score = 0;

            if ($index < 2) {
                $score += 2;
            }

            foreach ($keywords as $keyword) {
                if (stripos($sentence, $keyword) !== false) {
                    $score += 3;
                }
            }

            $wordCount = str_word_count($sentence);
            if ($wordCount >= 5 && $wordCount <= 30) {
                $score += 1;
            }

            $scoredSentences[] = [
                'sentence' => $sentence,
                'score' => $score,
                'index' => $index,
            ];
        }

        usort($scoredSentences, fn ($a, $b) => $b['score'] <=> $a['score']);

        $topSentences = array_slice($scoredSentences, 0, 4);

        usort($topSentences, fn ($a, $b) => $a['index'] <=> $b['index']);

        $summary = implode(' ', array_column($topSentences, 'sentence'));

        return trim($summary);
    }

    public function reanalyze(Call $call): Call
    {
        if (!$call->transcript) {
            throw new \RuntimeException('Cannot reanalyze call without existing transcript');
        }

        $analysisData = [
            'transcript' => $call->transcript,
            'duration' => $call->duration_seconds,
            'sentiment_score' => $call->sentiment_score ?? 0,
            'utterances' => [],
        ];

        $sentiment = $this->sentimentAnalysisService->analyze($call->transcript);
        $leadScore = $this->leadScoringService->calculateScore($analysisData);
        $eligibility = $this->leadScoringService->determineEligibility($leadScore, $analysisData);
        $nextActions = $this->leadScoringService->generateNextActions($leadScore, $eligibility, $analysisData);
        $keywords = $this->detectKeywords($call->transcript);

        $call->update([
            'summary' => $this->generateSummary($call->transcript, $keywords),
            'lead_score' => $leadScore,
            'eligibility' => $eligibility,
            'next_actions' => $nextActions,
            'keywords_detected' => $keywords,
            'sentiment' => $sentiment['sentiment'],
            'sentiment_score' => $sentiment['score'],
        ]);

        return $call->fresh();
    }
}

