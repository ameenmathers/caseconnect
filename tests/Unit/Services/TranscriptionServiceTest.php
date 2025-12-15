<?php

use App\Services\TranscriptionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['services.assemblyai.key' => 'test-api-key']);
    $this->service = new TranscriptionService();
});

describe('transcribe', function () {
    it('uploads file and returns transcription result', function () {
        Storage::fake('local');
        Storage::put('calls/test.mp3', 'fake audio content');

        Http::fake([
            'api.assemblyai.com/v2/upload' => Http::response([
                'upload_url' => 'https://cdn.assemblyai.com/upload/test-file',
            ]),
            'api.assemblyai.com/v2/transcript' => Http::response([
                'id' => 'transcript-123',
                'status' => 'queued',
            ]),
            'api.assemblyai.com/v2/transcript/transcript-123' => Http::sequence()
                ->push(['status' => 'processing'])
                ->push([
                    'status' => 'completed',
                    'text' => 'Hello, I was in a car accident.',
                    'audio_duration' => 120,
                    'words' => [],
                    'utterances' => [],
                    'sentiment_analysis_results' => [
                        ['sentiment' => 'NEUTRAL'],
                    ],
                ]),
        ]);

        $result = $this->service->transcribe('calls/test.mp3');

        expect($result)
            ->toHaveKey('text')
            ->toHaveKey('duration')
            ->toHaveKey('sentiment')
            ->and($result['text'])->toBe('Hello, I was in a car accident.');
    });

    it('throws exception when file cannot be read', function () {
        Storage::fake('local');

        expect(fn () => $this->service->transcribe('nonexistent.mp3'))
            ->toThrow(RuntimeException::class, 'Unable to read file');
    });
});

describe('transcribeFromUrl', function () {
    it('transcribes audio from URL directly', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response([
                'id' => 'transcript-456',
            ]),
            'api.assemblyai.com/v2/transcript/transcript-456' => Http::response([
                'status' => 'completed',
                'text' => 'This is a test transcription.',
                'audio_duration' => 60,
            ]),
        ]);

        $result = $this->service->transcribeFromUrl('https://example.com/audio.mp3');

        expect($result['text'])->toBe('This is a test transcription.');
        expect($result['duration'])->toBe(60);
    });
});

describe('error handling', function () {
    it('throws exception when transcription fails', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response([
                'id' => 'transcript-error',
            ]),
            'api.assemblyai.com/v2/transcript/transcript-error' => Http::response([
                'status' => 'error',
                'error' => 'Audio file could not be processed',
            ]),
        ]);

        expect(fn () => $this->service->transcribeFromUrl('https://example.com/bad.mp3'))
            ->toThrow(RuntimeException::class, 'Audio file could not be processed');
    });

    it('throws exception when upload fails', function () {
        Storage::fake('local');
        Storage::put('calls/test.mp3', 'fake audio content');

        Http::fake([
            'api.assemblyai.com/v2/upload' => Http::response(['error' => 'Unauthorized'], 401),
        ]);

        expect(fn () => $this->service->transcribe('calls/test.mp3'))
            ->toThrow(RuntimeException::class, 'Failed to upload file');
    });

    it('throws exception when submit fails', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response(['error' => 'Bad request'], 400),
        ]);

        expect(fn () => $this->service->transcribeFromUrl('https://example.com/audio.mp3'))
            ->toThrow(RuntimeException::class, 'Failed to submit transcription');
    });
});

describe('sentiment extraction', function () {
    it('calculates positive sentiment correctly', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response(['id' => 'test']),
            'api.assemblyai.com/v2/transcript/test' => Http::response([
                'status' => 'completed',
                'text' => 'Great service!',
                'sentiment_analysis_results' => [
                    ['sentiment' => 'POSITIVE'],
                    ['sentiment' => 'POSITIVE'],
                    ['sentiment' => 'POSITIVE'],
                    ['sentiment' => 'NEUTRAL'],
                ],
            ]),
        ]);

        $result = $this->service->transcribeFromUrl('https://example.com/audio.mp3');

        expect($result['sentiment'])->toBe('positive');
        expect($result['sentiment_score'])->toBeGreaterThan(0);
    });

    it('calculates negative sentiment correctly', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response(['id' => 'test']),
            'api.assemblyai.com/v2/transcript/test' => Http::response([
                'status' => 'completed',
                'text' => 'Terrible experience!',
                'sentiment_analysis_results' => [
                    ['sentiment' => 'NEGATIVE'],
                    ['sentiment' => 'NEGATIVE'],
                    ['sentiment' => 'NEGATIVE'],
                    ['sentiment' => 'NEUTRAL'],
                ],
            ]),
        ]);

        $result = $this->service->transcribeFromUrl('https://example.com/audio.mp3');

        expect($result['sentiment'])->toBe('negative');
        expect($result['sentiment_score'])->toBeLessThan(0);
    });

    it('returns neutral when no sentiment data', function () {
        Http::fake([
            'api.assemblyai.com/v2/transcript' => Http::response(['id' => 'test']),
            'api.assemblyai.com/v2/transcript/test' => Http::response([
                'status' => 'completed',
                'text' => 'Hello',
                'sentiment_analysis_results' => [],
            ]),
        ]);

        $result = $this->service->transcribeFromUrl('https://example.com/audio.mp3');

        expect($result['sentiment'])->toBe('neutral');
        expect($result['sentiment_score'])->toBe(0.0);
    });
});

