<?php

use App\Enums\RegimeAssessmentStatuses;

dataset('regimeAssessmentStatusValidationErrors', function () {
    return [
        'status not in RegimeAssessmentStatuses enum' => [
            [
                'country' => 'CA',
                'status' => 'not a status',
            ],
            ['status' => 'The Regime Assessment Status (status) must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.'],
        ],
        'missing status' => [
            [
                'country' => 'CA',
            ],
            ['status' => 'The Regime Assessment Status (status) must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.'],
        ],
    ];
});
