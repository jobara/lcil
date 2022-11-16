<?php

use App\Enums\RegimeAssessmentStatuses;

dataset('regimeAssessmentValidationErrors', function () {
    return [
        'missing country' => [
            [
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            ['country' => 'A Country (country), specified using an ISO 3166-1 alpha-2 country code, is required.'],
        ],
        'country code too short' => [
            [
                'country' => 'C',
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            ['country' => 'A Country (country), specified using an ISO 3166-1 alpha-2 country code, is required.'],
        ],
        'subdivision code too short' => [
            [
                'country' => 'CA',
                'subdivision' => 'O',
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            ['subdivision' => 'The Province / Territory (subdivision) must be specified using the subdivision portion of an ISO 3166-2 code.'],
        ],
        'municipality without subdivision' => [
            [
                'country' => 'CA',
                'municipality' => 'Toronto',
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            ['subdivision' => 'The Province / Territory (subdivision) cannot be empty if the Municipality (municipality) is specified.'],
        ],
        'year_of_assessment not an integer' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'country' => 'CA',
                'year_of_assessment' => 20.22,
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            fn () => ['year_of_assessment' => 'The Year of Assessment (year_of_assessment) must be within '.config('settings.year.min').' and '.config('settings.year.max').'.'],
        ],
        'year_of_assessment below min' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'country' => 'CA',
                'year_of_assessment' => config('settings.year.min') - 1,
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            fn () => ['year_of_assessment' => 'The Year of Assessment (year_of_assessment) must be within '.config('settings.year.min').' and '.config('settings.year.max').'.'],
        ],
        'year_of_assessment above max' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'country' => 'CA',
                'year_of_assessment' => config('settings.year.max') + 1,
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            fn () => ['year_of_assessment' => 'The Year of Assessment (year_of_assessment) must be within '.config('settings.year.min').' and '.config('settings.year.max').'.'],
        ],
        'year_of_assessment not a number' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'country' => 'CA',
                'year_of_assessment' => false,
                'status' => RegimeAssessmentStatuses::Draft->value,
            ],
            fn () => ['year_of_assessment' => 'The Year of Assessment (year_of_assessment) must be within '.config('settings.year.min').' and '.config('settings.year.max').'.'],
        ],
        'status not in RegimeAssessmentStatuses enum' => [
            [
                'country' => 'CA',
                'status' => 'not a status',
            ],
            ['status' => 'The Regime Assessment Status (status) must be one of the following: '.implode(', ', RegimeAssessmentStatuses::values()).'.'],
        ],
    ];
});
