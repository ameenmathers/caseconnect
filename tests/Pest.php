<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Unit/Models');

pest()->extend(Tests\TestCase::class)
    ->in('Unit/Services/TranscriptionServiceTest.php');

expect()->extend('toBeValidScore', function () {
    return $this->toBeInt()
        ->toBeGreaterThanOrEqual(0)
        ->toBeLessThanOrEqual(100);
});

expect()->extend('toBeEligibility', function () {
    return $this->toBeIn(['yes', 'no', 'pending']);
});
