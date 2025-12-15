<?php

namespace App\Services;

class SentimentAnalysisService
{
    protected array $positiveWords = [
        'great', 'good', 'excellent', 'happy', 'satisfied', 'pleased', 'wonderful',
        'helpful', 'professional', 'thank', 'appreciate', 'perfect', 'amazing',
        'fantastic', 'friendly', 'responsive', 'efficient', 'thorough',
    ];

    protected array $negativeWords = [
        'bad', 'terrible', 'awful', 'frustrated', 'angry', 'disappointed', 'upset',
        'worst', 'horrible', 'rude', 'unprofessional', 'slow', 'incompetent',
        'annoying', 'waste', 'problem', 'issue', 'complaint', 'never', 'hate',
    ];

    protected array $intensifiers = [
        'very', 'extremely', 'really', 'absolutely', 'completely', 'totally',
    ];

    public function analyze(string $text): array
    {
        $text = strtolower($text);
        $words = str_word_count($text, 1);

        $positiveCount = $this->countMatches($words, $this->positiveWords);
        $negativeCount = $this->countMatches($words, $this->negativeWords);

        $score = $this->calculateScore($positiveCount, $negativeCount, count($words));
        $sentiment = $this->determineSentiment($score);
        $confidence = $this->calculateConfidence($positiveCount, $negativeCount, count($words));

        return [
            'sentiment' => $sentiment,
            'score' => $score,
            'confidence' => $confidence,
            'positive_indicators' => $this->findIndicators($words, $this->positiveWords),
            'negative_indicators' => $this->findIndicators($words, $this->negativeWords),
        ];
    }

    protected function countMatches(array $words, array $dictionary): int
    {
        $count = 0;
        $previousWord = '';

        foreach ($words as $word) {
            if (in_array($word, $dictionary)) {
                $multiplier = in_array($previousWord, $this->intensifiers) ? 2 : 1;
                $count += $multiplier;
            }
            $previousWord = $word;
        }

        return $count;
    }

    protected function calculateScore(int $positive, int $negative, int $totalWords): float
    {
        if ($totalWords === 0) {
            return 0.0;
        }

        $netSentiment = $positive - $negative;
        $normalizer = sqrt($totalWords);

        $rawScore = $netSentiment / $normalizer;

        return max(-1.0, min(1.0, $rawScore));
    }

    protected function determineSentiment(float $score): string
    {
        return match (true) {
            $score > 0.1 => 'positive',
            $score < -0.1 => 'negative',
            default => 'neutral',
        };
    }

    protected function calculateConfidence(int $positive, int $negative, int $totalWords): float
    {
        if ($totalWords === 0) {
            return 0.0;
        }

        $sentimentWordRatio = ($positive + $negative) / $totalWords;
        $dominance = abs($positive - $negative) / max(1, $positive + $negative);

        $confidence = ($sentimentWordRatio * 0.5 + $dominance * 0.5) * 100;

        return min(100, round($confidence, 1));
    }

    protected function findIndicators(array $words, array $dictionary): array
    {
        $found = array_intersect($words, $dictionary);

        return array_values(array_unique($found));
    }

    public function getEmotionalTone(string $text): string
    {
        $analysis = $this->analyze($text);
        $score = $analysis['score'];
        $confidence = $analysis['confidence'];

        if ($confidence < 30) {
            return 'neutral';
        }

        return match (true) {
            $score > 0.5 => 'enthusiastic',
            $score > 0.2 => 'positive',
            $score > 0 => 'slightly positive',
            $score < -0.5 => 'frustrated',
            $score < -0.2 => 'negative',
            $score < 0 => 'slightly negative',
            default => 'neutral',
        };
    }
}

