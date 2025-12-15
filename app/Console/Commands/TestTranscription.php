<?php

namespace App\Console\Commands;

use App\Services\TranscriptionService;
use Illuminate\Console\Command;

class TestTranscription extends Command
{
    protected $signature = 'test:transcription {url?}';
    protected $description = 'Test the AssemblyAI transcription service with a sample audio file';

    public function handle(TranscriptionService $service): int
    {
        $url = $this->argument('url') ?? 'https://assembly.ai/wildfires.mp3';

        $this->info('🎙️  Testing AssemblyAI Transcription Service');
        $this->line(str_repeat('=', 50));
        $this->newLine();

        if (!config('services.assemblyai.key')) {
            $this->error('❌ ASSEMBLYAI_API_KEY is not set in your .env file');
            return Command::FAILURE;
        }

        $this->info("📡 Testing with: {$url}");
        $this->newLine();
        $this->warn('⏳ Submitting transcription request...');
        $this->line('   (This may take 30-60 seconds for a short file)');
        $this->newLine();

        try {
            $result = $service->transcribeFromUrl($url);

            $this->info('✅ Transcription Complete!');
            $this->newLine();

            $this->line('📝 <fg=cyan>Text Preview:</>');
            $this->line(str_repeat('-', 50));
            $this->line(substr($result['text'], 0, 500) . '...');
            $this->newLine();

            $this->line('📊 <fg=cyan>Analysis Results:</>');
            $this->line(str_repeat('-', 50));

            $this->table(
                ['Metric', 'Value'],
                [
                    ['Duration', ($result['duration'] ?? 'N/A') . ' seconds'],
                    ['Sentiment', $result['sentiment'] ?? 'N/A'],
                    ['Sentiment Score', $result['sentiment_score'] ?? 'N/A'],
                    ['Word Count', str_word_count($result['text'])],
                ]
            );

            if (!empty($result['highlights'])) {
                $this->newLine();
                $this->line('🔑 <fg=cyan>Key Highlights:</>');
                foreach (array_slice($result['highlights'], 0, 5) as $highlight) {
                    $this->line("   • {$highlight['text']} <fg=gray>(mentioned {$highlight['count']}x)</>");
                }
            }

            $this->newLine();
            $this->info('✨ Test passed successfully!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

