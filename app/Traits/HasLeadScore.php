<?php

namespace App\Traits;

trait HasLeadScore
{
    public function getScoreLabel(): string
    {
        $score = $this->lead_score;

        if ($score === null) {
            return 'Not Scored';
        }

        return match (true) {
            $score >= 80 => 'Hot Lead',
            $score >= 60 => 'Warm Lead',
            $score >= 40 => 'Lukewarm',
            $score >= 20 => 'Cold Lead',
            default => 'Very Cold',
        };
    }

    public function getScoreColor(): string
    {
        $score = $this->lead_score;

        if ($score === null) {
            return 'gray';
        }

        return match (true) {
            $score >= 80 => 'emerald',
            $score >= 60 => 'amber',
            $score >= 40 => 'orange',
            $score >= 20 => 'rose',
            default => 'slate',
        };
    }

    public function isHighValueLead(): bool
    {
        return $this->lead_score !== null && $this->lead_score >= 70;
    }

    public function isQualified(): bool
    {
        return $this->eligibility === 'yes' && $this->lead_score >= 50;
    }
}

