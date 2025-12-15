<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\TranscriptionService;

echo "🎙️  Testing AssemblyAI Transcription Service\n";
echo str_repeat("=", 50) . "\n\n";

$service = new TranscriptionService();

$testUrl = "https://assembly.ai/wildfires.mp3";

echo "📡 Testing with sample audio: {$testUrl}\n\n";

try {
    echo "⏳ Submitting transcription request...\n";
    echo "   (This may take 30-60 seconds for a short file)\n\n";
    
    $result = $service->transcribeFromUrl($testUrl);
    
    echo "✅ Transcription Complete!\n\n";
    
    echo "📝 Text Preview:\n";
    echo str_repeat("-", 50) . "\n";
    echo substr($result['text'], 0, 500) . "...\n\n";
    
    echo "📊 Analysis Results:\n";
    echo str_repeat("-", 50) . "\n";
    echo "Duration: " . ($result['duration'] ?? 'N/A') . " seconds\n";
    echo "Sentiment: " . ($result['sentiment'] ?? 'N/A') . "\n";
    echo "Sentiment Score: " . ($result['sentiment_score'] ?? 'N/A') . "\n";
    
    if (!empty($result['highlights'])) {
        echo "\n🔑 Key Highlights:\n";
        foreach (array_slice($result['highlights'], 0, 5) as $highlight) {
            echo "   - {$highlight['text']} (mentioned {$highlight['count']}x)\n";
        }
    }
    
    echo "\n✨ Test passed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nMake sure your ASSEMBLYAI_API_KEY is set in .env\n";
}

