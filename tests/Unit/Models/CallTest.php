<?php

use App\Models\Call;

describe('scopes', function () {
    it('filters pending calls', function () {
        Call::factory()->create(['status' => 'pending']);
        Call::factory()->create(['status' => 'completed']);

        $pendingCalls = Call::pending()->get();

        expect($pendingCalls)->toHaveCount(1);
        expect($pendingCalls->first()->status)->toBe('pending');
    });

    it('filters completed calls', function () {
        Call::factory()->create(['status' => 'pending']);
        Call::factory()->create(['status' => 'completed']);
        Call::factory()->create(['status' => 'completed']);

        $completedCalls = Call::completed()->get();

        expect($completedCalls)->toHaveCount(2);
    });

    it('filters eligible calls', function () {
        Call::factory()->create(['eligibility' => 'yes']);
        Call::factory()->create(['eligibility' => 'no']);
        Call::factory()->create(['eligibility' => 'pending']);

        $eligibleCalls = Call::eligible()->get();

        expect($eligibleCalls)->toHaveCount(1);
    });

    it('filters high value leads', function () {
        Call::factory()->create(['lead_score' => 80]);
        Call::factory()->create(['lead_score' => 50]);
        Call::factory()->create(['lead_score' => 90]);

        $highValueLeads = Call::highValue()->get();

        expect($highValueLeads)->toHaveCount(2);
    });
});

describe('status methods', function () {
    it('marks call as processing', function () {
        $call = Call::factory()->create(['status' => 'pending']);

        $call->markAsProcessing();

        expect($call->fresh()->status)->toBe('processing');
    });

    it('marks call as completed with timestamp', function () {
        $call = Call::factory()->create(['status' => 'processing']);

        $call->markAsCompleted();

        $fresh = $call->fresh();
        expect($fresh->status)->toBe('completed');
        expect($fresh->processed_at)->not->toBeNull();
    });

    it('marks call as failed with error message', function () {
        $call = Call::factory()->create(['status' => 'processing']);

        $call->markAsFailed('API timeout');

        $fresh = $call->fresh();
        expect($fresh->status)->toBe('failed');
        expect($fresh->error_message)->toBe('API timeout');
    });
});

describe('accessors', function () {
    it('formats duration correctly', function () {
        $call = new Call(['duration_seconds' => 125]);

        expect($call->formatted_duration)->toBe('2:05');
    });

    it('returns N/A for null duration', function () {
        $call = new Call(['duration_seconds' => null]);

        expect($call->formatted_duration)->toBe('N/A');
    });

    it('formats file size in bytes', function () {
        $call = new Call(['file_size' => 500]);

        expect($call->formatted_file_size)->toBe('500 B');
    });

    it('formats file size in KB', function () {
        $call = new Call(['file_size' => 2048]);

        expect($call->formatted_file_size)->toBe('2 KB');
    });

    it('formats file size in MB', function () {
        $call = new Call(['file_size' => 2097152]);

        expect($call->formatted_file_size)->toBe('2 MB');
    });

    it('returns N/A for null file size', function () {
        $call = new Call(['file_size' => null]);

        expect($call->formatted_file_size)->toBe('N/A');
    });
});

describe('sentiment emoji', function () {
    it('returns happy emoji for positive sentiment', function () {
        $call = new Call(['sentiment' => 'positive']);

        expect($call->getSentimentEmoji())->toBe('😊');
    });

    it('returns sad emoji for negative sentiment', function () {
        $call = new Call(['sentiment' => 'negative']);

        expect($call->getSentimentEmoji())->toBe('😟');
    });

    it('returns neutral emoji for neutral sentiment', function () {
        $call = new Call(['sentiment' => 'neutral']);

        expect($call->getSentimentEmoji())->toBe('😐');
    });

    it('returns question emoji for unknown sentiment', function () {
        $call = new Call(['sentiment' => null]);

        expect($call->getSentimentEmoji())->toBe('❓');
    });
});

describe('casts', function () {
    it('casts next_actions to array', function () {
        $call = Call::factory()->create([
            'next_actions' => ['action1', 'action2'],
        ]);

        $fresh = $call->fresh();
        expect($fresh->next_actions)->toBeArray();
    });

    it('casts keywords_detected to array', function () {
        $call = Call::factory()->create([
            'keywords_detected' => ['keyword1', 'keyword2'],
        ]);

        $fresh = $call->fresh();
        expect($fresh->keywords_detected)->toBeArray();
    });

    it('casts lead_score to integer', function () {
        $call = Call::factory()->create(['lead_score' => '75']);

        expect($call->fresh()->lead_score)->toBeInt();
    });
});

