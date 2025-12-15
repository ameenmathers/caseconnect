<?php

namespace App\Traits;

trait Analyzable
{
    protected array $targetKeywords = [
        'car accident',
        'injury',
        'insurance denied',
        'medical bills',
        'personal injury',
        'slip and fall',
        'whiplash',
        'settlement',
        'liability',
        'negligence',
        'damages',
        'compensation',
    ];

    public function detectKeywords(string $text): array
    {
        $text = strtolower($text);
        $detected = [];

        foreach ($this->targetKeywords as $keyword) {
            if (str_contains($text, strtolower($keyword))) {
                $detected[] = $keyword;
            }
        }

        return array_unique($detected);
    }

    public function countKeywordOccurrences(string $text): array
    {
        $text = strtolower($text);
        $counts = [];

        foreach ($this->targetKeywords as $keyword) {
            $count = substr_count($text, strtolower($keyword));
            if ($count > 0) {
                $counts[$keyword] = $count;
            }
        }

        return $counts;
    }

    public function hasUrgentKeywords(string $text): bool
    {
        $urgentKeywords = ['emergency', 'urgent', 'immediately', 'asap', 'critical'];
        $text = strtolower($text);

        foreach ($urgentKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function extractPhoneNumbers(string $text): array
    {
        preg_match_all('/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/', $text, $matches);

        return $matches[0] ?? [];
    }

    public function calculateReadability(string $text): float
    {
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $words = str_word_count($text);
        $syllables = $this->countSyllables($text);

        if ($words === 0 || count($sentences) === 0) {
            return 0;
        }

        $avgWordsPerSentence = $words / count($sentences);
        $avgSyllablesPerWord = $syllables / $words;

        return 206.835 - (1.015 * $avgWordsPerSentence) - (84.6 * $avgSyllablesPerWord);
    }

    protected function countSyllables(string $text): int
    {
        $words = explode(' ', strtolower($text));
        $count = 0;

        foreach ($words as $word) {
            $word = preg_replace('/[^a-z]/', '', $word);
            $count += max(1, preg_match_all('/[aeiouy]+/', $word));
        }

        return $count;
    }
}

