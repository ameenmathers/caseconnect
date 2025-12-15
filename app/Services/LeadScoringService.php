<?php

namespace App\Services;

use App\Traits\Analyzable;

class LeadScoringService
{
    use Analyzable;

    protected array $scoreWeights = [
        'keywords' => 30,
        'sentiment' => 20,
        'duration' => 15,
        'urgency' => 15,
        'contact_info' => 10,
        'engagement' => 10,
    ];

    protected array $highValueKeywords = [
        'car accident' => 10,
        'personal injury' => 10,
        'medical bills' => 8,
        'insurance denied' => 9,
        'settlement' => 7,
        'liability' => 6,
        'negligence' => 7,
        'damages' => 5,
        'compensation' => 6,
        'injury' => 5,
        'whiplash' => 6,
        'slip and fall' => 8,
    ];

    public function calculateScore(array $analysisData): int
    {
        $score = 0;

        $score += $this->scoreKeywords($analysisData['transcript'] ?? '');
        $score += $this->scoreSentiment($analysisData['sentiment_score'] ?? 0);
        $score += $this->scoreDuration($analysisData['duration'] ?? 0);
        $score += $this->scoreUrgency($analysisData['transcript'] ?? '');
        $score += $this->scoreContactInfo($analysisData['transcript'] ?? '');
        $score += $this->scoreEngagement($analysisData);

        return min(100, max(0, $score));
    }

    protected function scoreKeywords(string $transcript): int
    {
        $text = strtolower($transcript);
        $score = 0;
        $maxKeywordScore = $this->scoreWeights['keywords'];

        foreach ($this->highValueKeywords as $keyword => $weight) {
            if (str_contains($text, $keyword)) {
                $score += $weight;
            }
        }

        return min($maxKeywordScore, $score);
    }

    protected function scoreSentiment(float $sentimentScore): int
    {
        $maxScore = $this->scoreWeights['sentiment'];
        $normalized = ($sentimentScore + 1) / 2;

        return (int) round($normalized * $maxScore);
    }

    protected function scoreDuration(int $durationSeconds): int
    {
        $maxScore = $this->scoreWeights['duration'];
        $optimalDuration = 300;

        if ($durationSeconds < 30) {
            return 0;
        }

        if ($durationSeconds >= $optimalDuration) {
            return $maxScore;
        }

        return (int) round(($durationSeconds / $optimalDuration) * $maxScore);
    }

    protected function scoreUrgency(string $transcript): int
    {
        $maxScore = $this->scoreWeights['urgency'];
        $urgencyKeywords = [
            'urgent' => 5,
            'immediately' => 5,
            'emergency' => 5,
            'asap' => 4,
            'right away' => 4,
            'today' => 3,
            'soon' => 2,
            'help' => 2,
        ];

        $text = strtolower($transcript);
        $score = 0;

        foreach ($urgencyKeywords as $keyword => $weight) {
            if (str_contains($text, $keyword)) {
                $score += $weight;
            }
        }

        return min($maxScore, $score);
    }

    protected function scoreContactInfo(string $transcript): int
    {
        $maxScore = $this->scoreWeights['contact_info'];
        $score = 0;

        $phoneNumbers = $this->extractPhoneNumbers($transcript);
        if (!empty($phoneNumbers)) {
            $score += 5;
        }

        if (preg_match('/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b/', $transcript)) {
            $score += 5;
        }

        return min($maxScore, $score);
    }

    protected function scoreEngagement(array $data): int
    {
        $maxScore = $this->scoreWeights['engagement'];
        $score = 0;

        $wordCount = str_word_count($data['transcript'] ?? '');
        if ($wordCount > 100) {
            $score += 5;
        }
        if ($wordCount > 300) {
            $score += 3;
        }

        $utterances = $data['utterances'] ?? [];
        if (count($utterances) > 5) {
            $score += 2;
        }

        return min($maxScore, $score);
    }

    public function determineEligibility(int $score, array $analysisData): string
    {
        $transcript = strtolower($analysisData['transcript'] ?? '');

        $hasPersonalInjury = str_contains($transcript, 'personal injury')
            || str_contains($transcript, 'injury')
            || str_contains($transcript, 'accident');

        if ($score >= 50 && $hasPersonalInjury) {
            return 'yes';
        }

        if ($score < 30) {
            return 'no';
        }

        return 'pending';
    }

    public function generateNextActions(int $score, string $eligibility, array $analysisData): array
    {
        $actions = [];
        $transcript = strtolower($analysisData['transcript'] ?? '');

        if ($score >= 70 && $eligibility === 'yes') {
            $actions[] = 'Priority: Schedule immediate consultation';
            $actions[] = 'Assign to senior case manager';
        }

        if ($score >= 50) {
            $actions[] = 'Send intake questionnaire';
            $actions[] = 'Request medical records authorization';
        }

        if (str_contains($transcript, 'insurance')) {
            $actions[] = 'Collect insurance policy information';
        }

        if (str_contains($transcript, 'medical') || str_contains($transcript, 'doctor')) {
            $actions[] = 'Document medical treatment timeline';
        }

        if ($eligibility === 'pending') {
            $actions[] = 'Schedule follow-up call for additional information';
        }

        if ($eligibility === 'no') {
            $actions[] = 'Send referral resources';
            $actions[] = 'Archive lead';
        }

        if (empty($actions)) {
            $actions[] = 'Review transcript for case details';
        }

        return array_slice($actions, 0, 5);
    }
}

