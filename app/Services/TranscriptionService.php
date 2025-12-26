<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TranscriptionService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.assemblyai.com/v2';

    public function __construct()
    {
        $this->apiKey = config('services.assemblyai.key');
        
        if (empty($this->apiKey)) {
            Log::error('AssemblyAI API key is not configured');
            throw new RuntimeException('AssemblyAI API key is not set. Please configure ASSEMBLYAI_API_KEY in your .env file.');
        }
        
        Log::info('TranscriptionService initialized', [
            'api_key_set' => !empty($this->apiKey),
            'api_key_length' => strlen($this->apiKey),
        ]);
    }

    public function transcribe(string $filePath): array
    {
        $uploadUrl = $this->uploadFile($filePath);

        $transcriptId = $this->submitTranscription($uploadUrl);

        return $this->pollForCompletion($transcriptId);
    }

    public function transcribeFromUrl(string $audioUrl): array
    {
        $transcriptId = $this->submitTranscription($audioUrl);

        return $this->pollForCompletion($transcriptId);
    }

    protected function uploadFile(string $filePath): string
    {
        Log::info('Uploading file to AssemblyAI', ['file_path' => $filePath]);
        
        $fileContent = Storage::get($filePath);

        if (!$fileContent) {
            Log::error('File not found or unreadable', ['file_path' => $filePath]);
            throw new RuntimeException("Unable to read file: {$filePath}");
        }

        $response = Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/octet-stream',
            ])
            ->withBody($fileContent, 'application/octet-stream')
            ->post('/upload');

        if (!$response->successful()) {
            Log::error('AssemblyAI upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Failed to upload file to AssemblyAI: ' . $response->body());
        }

        $uploadUrl = $response->json('upload_url');
        Log::info('File uploaded successfully', ['upload_url' => substr($uploadUrl, 0, 50) . '...']);
        
        return $uploadUrl;
    }

    protected function submitTranscription(string $audioUrl): string
    {
        $response = $this->client()->post('/transcript', [
            'audio_url' => $audioUrl,
            'sentiment_analysis' => true,
            'auto_highlights' => true,
            'speaker_labels' => true,
        ]);

        if (!$response->successful()) {
            Log::error('AssemblyAI transcription submission failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Failed to submit transcription: ' . $response->body());
        }

        $transcriptId = $response->json('id');
        Log::info('Transcription submitted', ['transcript_id' => $transcriptId]);
        
        return $transcriptId;
    }

    protected function pollForCompletion(string $transcriptId): array
    {
        $pollingEndpoint = "/transcript/{$transcriptId}";
        $maxAttempts = 120;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $response = $this->client()->get($pollingEndpoint);

            if (!$response->successful()) {
                throw new RuntimeException('Failed to check transcription status');
            }

            $result = $response->json();

            if ($result['status'] === 'completed') {
                return $this->formatTranscriptionResult($result);
            }

            if ($result['status'] === 'error') {
                throw new RuntimeException('Transcription failed: ' . ($result['error'] ?? 'Unknown error'));
            }

            sleep(3);
            $attempt++;
        }

        throw new RuntimeException('Transcription timed out after ' . ($maxAttempts * 3) . ' seconds');
    }

    protected function formatTranscriptionResult(array $data): array
    {
        $sentiments = $this->extractSentiments($data['sentiment_analysis_results'] ?? []);

        return [
            'text' => $data['text'] ?? '',
            'duration' => $data['audio_duration'] ?? null,
            'words' => $data['words'] ?? [],
            'utterances' => $data['utterances'] ?? [],
            'sentiment' => $sentiments['overall'],
            'sentiment_score' => $sentiments['score'],
            'highlights' => $this->extractHighlights($data['auto_highlights_result'] ?? []),
        ];
    }

    protected function extractSentiments(array $sentimentResults): array
    {
        if (empty($sentimentResults)) {
            return ['overall' => 'neutral', 'score' => 0.0];
        }

        $scores = [
            'POSITIVE' => 0,
            'NEGATIVE' => 0,
            'NEUTRAL' => 0,
        ];

        foreach ($sentimentResults as $result) {
            $sentiment = $result['sentiment'] ?? 'NEUTRAL';
            $scores[$sentiment]++;
        }

        $total = array_sum($scores);
        $positiveRatio = $scores['POSITIVE'] / $total;
        $negativeRatio = $scores['NEGATIVE'] / $total;

        $score = $positiveRatio - $negativeRatio;

        $overall = match (true) {
            $score > 0.2 => 'positive',
            $score < -0.2 => 'negative',
            default => 'neutral',
        };

        return ['overall' => $overall, 'score' => round($score, 2)];
    }

    protected function extractHighlights(array $highlightsResult): array
    {
        $highlights = $highlightsResult['results'] ?? [];

        return array_map(fn ($h) => [
            'text' => $h['text'] ?? '',
            'count' => $h['count'] ?? 0,
        ], $highlights);
    }

    protected function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'authorization' => $this->apiKey,
                'content-type' => 'application/json',
            ])
            ->timeout(30);
    }
}
