<?php

use App\Models\Call;

it('displays the dashboard page', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});

it('shows correct statistics', function () {
    Call::factory()->count(3)->completed()->create();
    Call::factory()->count(2)->create(['status' => 'pending']);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Total Calls');
});

it('displays recent calls', function () {
    $call = Call::factory()->create([
        'original_filename' => 'test-recording.mp3',
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('test-recording.mp3');
});

it('displays high value leads', function () {
    $highValueLead = Call::factory()->highValue()->create([
        'original_filename' => 'high-value-call.mp3',
    ]);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('High Value Leads');
});

it('calculates sentiment breakdown correctly', function () {
    Call::factory()->completed()->create(['sentiment' => 'positive']);
    Call::factory()->completed()->create(['sentiment' => 'negative']);
    Call::factory()->completed()->create(['sentiment' => 'neutral']);

    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Sentiment Analysis');
});

