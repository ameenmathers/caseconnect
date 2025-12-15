<?php

namespace Database\Factories;

use App\Models\Call;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        return [
            'filename' => Str::uuid() . '.mp3',
            'original_filename' => fake()->sentence(3) . '.mp3',
            'file_path' => 'calls/' . Str::uuid() . '.mp3',
            'mime_type' => 'audio/mpeg',
            'file_size' => fake()->numberBetween(100000, 5000000),
            'duration_seconds' => fake()->numberBetween(30, 600),
            'status' => 'pending',
            'eligibility' => 'pending',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'transcript' => fake()->paragraphs(3, true),
            'summary' => fake()->paragraph(),
            'lead_score' => fake()->numberBetween(0, 100),
            'eligibility' => fake()->randomElement(['yes', 'no']),
            'next_actions' => [
                'Schedule follow-up call',
                'Send information packet',
                'Transfer to senior agent',
            ],
            'keywords_detected' => ['car accident', 'injury', 'insurance denied'],
            'sentiment' => fake()->randomElement(['positive', 'negative', 'neutral']),
            'sentiment_score' => fake()->randomFloat(2, -1, 1),
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processing',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => 'Transcription service unavailable',
        ]);
    }

    public function highValue(): static
    {
        return $this->completed()->state(fn (array $attributes) => [
            'lead_score' => fake()->numberBetween(80, 100),
            'eligibility' => 'yes',
        ]);
    }

    public function lowValue(): static
    {
        return $this->completed()->state(fn (array $attributes) => [
            'lead_score' => fake()->numberBetween(0, 30),
            'eligibility' => 'no',
        ]);
    }
}
