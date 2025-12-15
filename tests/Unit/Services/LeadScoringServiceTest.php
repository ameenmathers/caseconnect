<?php

use App\Services\LeadScoringService;

beforeEach(function () {
    $this->service = new LeadScoringService();
});

describe('calculateScore', function () {
    it('returns base sentiment score for empty data', function () {
        $score = $this->service->calculateScore([]);

        expect($score)->toBeLessThanOrEqual(20);
    });

    it('increases score when high-value keywords are present', function () {
        $data = ['transcript' => 'I was in a car accident and suffered an injury'];

        $score = $this->service->calculateScore($data);

        expect($score)->toBeGreaterThan(0);
    });

    it('caps score at 100', function () {
        $data = [
            'transcript' => 'car accident injury insurance denied medical bills personal injury settlement liability negligence damages compensation urgent immediately help today',
            'duration' => 600,
            'sentiment_score' => 1.0,
            'utterances' => array_fill(0, 10, ['text' => 'test']),
        ];

        $score = $this->service->calculateScore($data);

        expect($score)->toBeLessThanOrEqual(100);
    });

    it('considers call duration in scoring', function () {
        $shortCall = ['transcript' => 'test', 'duration' => 20];
        $longCall = ['transcript' => 'test', 'duration' => 300];

        $shortScore = $this->service->calculateScore($shortCall);
        $longScore = $this->service->calculateScore($longCall);

        expect($longScore)->toBeGreaterThanOrEqual($shortScore);
    });

    it('considers sentiment in scoring', function () {
        $negativeCall = ['transcript' => 'test', 'sentiment_score' => -1.0];
        $positiveCall = ['transcript' => 'test', 'sentiment_score' => 1.0];

        $negativeScore = $this->service->calculateScore($negativeCall);
        $positiveScore = $this->service->calculateScore($positiveCall);

        expect($positiveScore)->toBeGreaterThan($negativeScore);
    });

    it('detects urgency keywords', function () {
        $urgentCall = ['transcript' => 'I need help urgently, this is an emergency'];
        $normalCall = ['transcript' => 'I would like to discuss my case'];

        $urgentScore = $this->service->calculateScore($urgentCall);
        $normalScore = $this->service->calculateScore($normalCall);

        expect($urgentScore)->toBeGreaterThan($normalScore);
    });
});

describe('determineEligibility', function () {
    it('returns yes for high score with injury keywords', function () {
        $eligibility = $this->service->determineEligibility(60, [
            'transcript' => 'I was injured in a car accident',
        ]);

        expect($eligibility)->toBe('yes');
    });

    it('returns no for very low scores', function () {
        $eligibility = $this->service->determineEligibility(20, [
            'transcript' => 'general inquiry',
        ]);

        expect($eligibility)->toBe('no');
    });

    it('returns pending for moderate scores without injury keywords', function () {
        $eligibility = $this->service->determineEligibility(45, [
            'transcript' => 'general legal matter',
        ]);

        expect($eligibility)->toBe('pending');
    });
});

describe('generateNextActions', function () {
    it('returns priority actions for high-value eligible leads', function () {
        $actions = $this->service->generateNextActions(80, 'yes', [
            'transcript' => 'car accident with insurance issues',
        ]);

        expect($actions)
            ->toBeArray()
            ->toContain('Priority: Schedule immediate consultation');
    });

    it('includes insurance-related actions when insurance is mentioned', function () {
        $actions = $this->service->generateNextActions(60, 'yes', [
            'transcript' => 'my insurance company denied my claim',
        ]);

        expect($actions)->toContain('Collect insurance policy information');
    });

    it('includes medical documentation for medical-related calls', function () {
        $actions = $this->service->generateNextActions(60, 'yes', [
            'transcript' => 'I have been seeing a doctor for my injuries',
        ]);

        expect($actions)->toContain('Document medical treatment timeline');
    });

    it('limits actions to maximum of 5', function () {
        $actions = $this->service->generateNextActions(80, 'yes', [
            'transcript' => 'insurance denied doctor medical bills injury accident',
        ]);

        expect($actions)->toHaveCount(5);
    });
});

