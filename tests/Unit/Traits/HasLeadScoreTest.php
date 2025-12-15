<?php

use App\Models\Call;

describe('getScoreLabel', function () {
    it('returns Hot Lead for scores 80 and above', function () {
        $call = new Call(['lead_score' => 85]);

        expect($call->getScoreLabel())->toBe('Hot Lead');
    });

    it('returns Warm Lead for scores between 60 and 79', function () {
        $call = new Call(['lead_score' => 65]);

        expect($call->getScoreLabel())->toBe('Warm Lead');
    });

    it('returns Lukewarm for scores between 40 and 59', function () {
        $call = new Call(['lead_score' => 50]);

        expect($call->getScoreLabel())->toBe('Lukewarm');
    });

    it('returns Cold Lead for scores between 20 and 39', function () {
        $call = new Call(['lead_score' => 30]);

        expect($call->getScoreLabel())->toBe('Cold Lead');
    });

    it('returns Very Cold for scores below 20', function () {
        $call = new Call(['lead_score' => 10]);

        expect($call->getScoreLabel())->toBe('Very Cold');
    });

    it('returns Not Scored when score is null', function () {
        $call = new Call(['lead_score' => null]);

        expect($call->getScoreLabel())->toBe('Not Scored');
    });
});

describe('getScoreColor', function () {
    it('returns emerald for high scores', function () {
        $call = new Call(['lead_score' => 90]);

        expect($call->getScoreColor())->toBe('emerald');
    });

    it('returns amber for warm scores', function () {
        $call = new Call(['lead_score' => 70]);

        expect($call->getScoreColor())->toBe('amber');
    });

    it('returns gray when score is null', function () {
        $call = new Call(['lead_score' => null]);

        expect($call->getScoreColor())->toBe('gray');
    });
});

describe('isHighValueLead', function () {
    it('returns true for scores 70 and above', function () {
        $call = new Call(['lead_score' => 75]);

        expect($call->isHighValueLead())->toBeTrue();
    });

    it('returns false for scores below 70', function () {
        $call = new Call(['lead_score' => 60]);

        expect($call->isHighValueLead())->toBeFalse();
    });

    it('returns false when score is null', function () {
        $call = new Call(['lead_score' => null]);

        expect($call->isHighValueLead())->toBeFalse();
    });
});

describe('isQualified', function () {
    it('returns true when eligible and score is 50 or above', function () {
        $call = new Call(['lead_score' => 55, 'eligibility' => 'yes']);

        expect($call->isQualified())->toBeTrue();
    });

    it('returns false when not eligible even with high score', function () {
        $call = new Call(['lead_score' => 80, 'eligibility' => 'no']);

        expect($call->isQualified())->toBeFalse();
    });

    it('returns false when eligible but low score', function () {
        $call = new Call(['lead_score' => 40, 'eligibility' => 'yes']);

        expect($call->isQualified())->toBeFalse();
    });
});

