<?php

use App\Enums\LawPolicyTypes;

dataset('lawPolicySourceValidationErrors', function () {
    return [
        'missing name' => [
            ['country' => 'CA'],
            ['name' => 'The Law or Policy Name (name) is required.'],
        ],
        'empty name' => [
            [
                'name' => '',
                'country' => 'CA',
            ],
            ['name' => 'The Law or Policy Name (name) is required.'],
        ],
        'missing country' => [
            [
                'name' => 'test',
            ],
            ['country' => 'A Country (country), specified using an ISO 3166-1 alpha-2 country code, is required.'],
        ],
        'country code too short' => [
            [
                'name' => 'test',
                'country' => 'C',
            ],
            ['country' => 'A Country (country), specified using an ISO 3166-1 alpha-2 country code, is required.'],
        ],
        'subdivision code too short' => [
            [
                'name' => 'test',
                'country' => 'CA',
                'subdivision' => 'O',
            ],
            ['subdivision' => 'The Province / Territory (subdivision) must be specified using the subdivision portion of an ISO 3166-2 code.'],
        ],
        'municipality without subdivision' => [
            [
                'name' => 'test',
                'country' => 'CA',
                'municipality' => 'Toronto',
            ],
            ['subdivision' => 'The Province / Territory (subdivision) cannot be empty if the Municipality (municipality) is specified.'],
        ],
        'year_in_effect not an integer' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'name' => 'test',
                'country' => 'CA',
                'year_in_effect' => 20.22,
            ],
            fn () => ['year_in_effect' => 'The Year in Effect (year_in_effect) must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.'],
        ],
        'year_in_effect below min' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'name' => 'test',
                'country' => 'CA',
                'year_in_effect' => config('settings.year.min') - 1,
            ],
            fn () => ['year_in_effect' => 'The Year in Effect (year_in_effect) must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.'],
        ],
        'year_in_effect above max' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'name' => 'test',
                'country' => 'CA',
                'year_in_effect' => config('settings.year.max') + 1,
            ],
            fn () => ['year_in_effect' => 'The Year in Effect (year_in_effect) must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.'],
        ],
        'year_in_effect not a number' => [
            // Uses Bound Datasets to resolve the dataset after the setup/before functions so that the `config` global
            // function is available. See: https://pestphp.com/docs/datasets#bound-datasets
            fn () => [
                'name' => 'test',
                'country' => 'CA',
                'year_in_effect' => false,
            ],
            fn () => ['year_in_effect' => 'The Year in Effect (year_in_effect) must be within ' . config('settings.year.min') . ' and ' . config('settings.year.max') . '.'],
        ],
        'reference not a valid URL' => [
            [
                'name' => 'test',
                'country' => 'CA',
                'reference' => 'not an URL',
            ],
            ['reference' => 'The Reference / Link (reference) format must be in a form like https://example.com or http://example.com.'],
        ],
        'type not in LawPolicyTypes enum' => [
            [
                'name' => 'test',
                'country' => 'CA',
                'type' => 'not a policy',
            ],
            ['type' => 'The Type (type) must be one of the following: ' . implode(', ', LawPolicyTypes::values()) . '.'],
        ],
        'is_core not a boolean' => [
            [
                'name' => 'test',
                'country' => 'CA',
                'is_core' => 'not a boolean value',
            ],
            ['is_core' => 'The Effect on Legal Capacity (is_core) must be true or false.'],
            ['is_core' => 'is_core-1']
        ],
    ];
});
