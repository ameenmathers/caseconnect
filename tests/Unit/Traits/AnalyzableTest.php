<?php

use App\Services\LeadScoringService;

beforeEach(function () {
    $this->analyzer = new LeadScoringService();
});

describe('detectKeywords', function () {
    it('detects single keyword', function () {
        $keywords = $this->analyzer->detectKeywords('I was in a car accident last week');

        expect($keywords)->toContain('car accident');
    });

    it('detects multiple keywords', function () {
        $keywords = $this->analyzer->detectKeywords('After the car accident, I suffered an injury and my insurance denied my claim');

        expect($keywords)
            ->toContain('car accident')
            ->toContain('injury')
            ->toContain('insurance denied');
    });

    it('is case insensitive', function () {
        $keywords = $this->analyzer->detectKeywords('CAR ACCIDENT and PERSONAL INJURY case');

        expect($keywords)
            ->toContain('car accident')
            ->toContain('personal injury');
    });

    it('returns empty array when no keywords found', function () {
        $keywords = $this->analyzer->detectKeywords('Hello, I would like to schedule an appointment');

        expect($keywords)->toBeEmpty();
    });

    it('returns unique keywords only', function () {
        $keywords = $this->analyzer->detectKeywords('car accident happened and another car accident occurred');

        $carAccidentCount = array_count_values($keywords)['car accident'] ?? 0;
        expect($carAccidentCount)->toBe(1);
    });
});

describe('extractPhoneNumbers', function () {
    it('extracts phone number with dashes', function () {
        $phones = $this->analyzer->extractPhoneNumbers('Call me at 555-123-4567');

        expect($phones)->toContain('555-123-4567');
    });

    it('extracts phone number with dots', function () {
        $phones = $this->analyzer->extractPhoneNumbers('My number is 555.123.4567');

        expect($phones)->toContain('555.123.4567');
    });

    it('extracts phone number without separators', function () {
        $phones = $this->analyzer->extractPhoneNumbers('Reach me at 5551234567');

        expect($phones)->toContain('5551234567');
    });

    it('extracts multiple phone numbers', function () {
        $phones = $this->analyzer->extractPhoneNumbers('Home: 555-111-2222, Work: 555-333-4444');

        expect($phones)
            ->toHaveCount(2)
            ->toContain('555-111-2222')
            ->toContain('555-333-4444');
    });

    it('returns empty array when no phone numbers found', function () {
        $phones = $this->analyzer->extractPhoneNumbers('No contact information provided');

        expect($phones)->toBeEmpty();
    });
});

describe('hasUrgentKeywords', function () {
    it('returns true for urgent text', function () {
        $result = $this->analyzer->hasUrgentKeywords('This is urgent, please call immediately');

        expect($result)->toBeTrue();
    });

    it('returns true for emergency text', function () {
        $result = $this->analyzer->hasUrgentKeywords('This is an emergency situation');

        expect($result)->toBeTrue();
    });

    it('returns false for non-urgent text', function () {
        $result = $this->analyzer->hasUrgentKeywords('I would like to schedule a consultation');

        expect($result)->toBeFalse();
    });
});

