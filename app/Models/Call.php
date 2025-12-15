<?php

namespace App\Models;

use App\Traits\Analyzable;
use App\Traits\HasLeadScore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Call extends Model
{
    use HasFactory, HasLeadScore, Analyzable;

    protected $fillable = [
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
        'duration_seconds',
        'transcript',
        'summary',
        'lead_score',
        'eligibility',
        'next_actions',
        'keywords_detected',
        'sentiment',
        'sentiment_score',
        'status',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'next_actions' => 'array',
            'keywords_detected' => 'array',
            'lead_score' => 'integer',
            'file_size' => 'integer',
            'duration_seconds' => 'integer',
            'sentiment_score' => 'float',
            'processed_at' => 'datetime',
        ];
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeEligible(Builder $query): Builder
    {
        return $query->where('eligibility', 'yes');
    }

    public function scopeHighValue(Builder $query): Builder
    {
        return $query->where('lead_score', '>=', 70);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $message): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $message,
        ]);
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;

        return sprintf('%d:%02d', $minutes, $seconds);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    public function getSentimentEmoji(): string
    {
        return match ($this->sentiment) {
            'positive' => '😊',
            'negative' => '😟',
            'neutral' => '😐',
            default => '❓',
        };
    }
}

