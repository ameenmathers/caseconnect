<?php

use App\Services\SentimentAnalysisService;

beforeEach(function () {
    $this->service = new SentimentAnalysisService();
});

describe('analyze', function () {
    it('returns positive sentiment for positive text', function () {
        $result = $this->service->analyze('This is great, wonderful service, very helpful and professional!');

        expect($result['sentiment'])->toBe('positive');
        expect($result['score'])->toBeGreaterThan(0);
    });

    it('returns negative sentiment for negative text', function () {
        $result = $this->service->analyze('This is terrible, awful experience, very frustrated and disappointed!');

        expect($result['sentiment'])->toBe('negative');
        expect($result['score'])->toBeLessThan(0);
    });

    it('returns neutral sentiment for neutral text', function () {
        $result = $this->service->analyze('The meeting is scheduled for tomorrow at 3pm in the conference room.');

        expect($result['sentiment'])->toBe('neutral');
    });

    it('identifies positive indicators', function () {
        $result = $this->service->analyze('This is excellent and wonderful service!');

        expect($result['positive_indicators'])
            ->toBeArray()
            ->toContain('excellent')
            ->toContain('wonderful');
    });

    it('identifies negative indicators', function () {
        $result = $this->service->analyze('This is terrible and awful service!');

        expect($result['negative_indicators'])
            ->toBeArray()
            ->toContain('terrible')
            ->toContain('awful');
    });

    it('handles empty text gracefully', function () {
        $result = $this->service->analyze('');

        expect($result)
            ->toHaveKey('sentiment')
            ->toHaveKey('score')
            ->toHaveKey('confidence');
    });

    it('returns confidence score between 0 and 100', function () {
        $result = $this->service->analyze('Very good excellent service, very helpful!');

        expect($result['confidence'])
            ->toBeGreaterThanOrEqual(0)
            ->toBeLessThanOrEqual(100);
    });

    it('increases weight with intensifiers', function () {
        $normalText = 'This is good service';
        $intensifiedText = 'This is very good service';

        $normalResult = $this->service->analyze($normalText);
        $intensifiedResult = $this->service->analyze($intensifiedText);

        expect($intensifiedResult['score'])->toBeGreaterThanOrEqual($normalResult['score']);
    });
});

describe('getEmotionalTone', function () {
    it('returns enthusiastic for highly positive text', function () {
        $tone = $this->service->getEmotionalTone('Amazing! Fantastic! Excellent! Wonderful! This is the best!');

        expect($tone)->toBe('enthusiastic');
    });

    it('returns frustrated for highly negative text', function () {
        $tone = $this->service->getEmotionalTone('Terrible! Awful! Horrible! The worst experience ever! So frustrated!');

        expect($tone)->toBe('frustrated');
    });

    it('returns neutral for low confidence analysis', function () {
        $tone = $this->service->getEmotionalTone('The package arrived.');

        expect($tone)->toBe('neutral');
    });
});

