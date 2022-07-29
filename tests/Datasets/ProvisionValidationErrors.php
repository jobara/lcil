<?php

use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;

dataset('provisionValidationErrors', function () {
    return [
        'missing section' => [
            [
                'body' => 'body text',
            ],
            ['section' => 'The Section or Subsection (section) is required.'],
        ],
        'empty section name' => [
            [
                'section' => '',
                'body' => 'body text',
            ],
            ['section' => 'The Section or Subsection (section) is required.'],
        ],
        'missing body' => [
            [
                'section' => '123',
            ],
            ['body' => 'The Provision Text (body) is required.'],
            ['body' => 'body-editable'],
            true,
        ],
        'empty body name' => [
            [
                'section' => '123',
                'body' => '',
            ],
            ['body' => 'The Provision Text (body) is required.'],
            ['body' => 'body-editable'],
            true,
        ],
        'reference not a valid URL' => [
            [
                'section' => '123',
                'body' => 'body text',
                'reference' => 'not an URL',
            ],
            ['reference' => 'The Reference / Link (reference) format must be in a form like https://example.com or http://example.com.'],
        ],
        'decision type without court_challenge' => [
            [
                'section' => '123',
                'body' => 'body text',
                'decision_type' => [ProvisionDecisionTypes::Financial->value],
            ],
            ['decision_type' => 'The Type of Decision (decision_type) requires the Court Challenge (court_challenge) indicate that a challenge has been initiated; the current value is: empty'],
            ['decision_type' => 'decision-type-financial-property'],
        ],
        'decision type not related court_challenge' => [
            [
                'section' => '123',
                'body' => 'body text',
                'court_challenge' => ProvisionCourtChallenges::NotRelated->value,
                'decision_type' => [ProvisionDecisionTypes::Financial->value],
            ],
            ['decision_type' => 'The Type of Decision (decision_type) requires the Court Challenge (court_challenge) indicate that a challenge has been initiated; the current value is: not_related'],
            ['decision_type' => 'decision-type-financial-property'],
        ],
        'legal capacity approach invalid value' => [
            [
                'section' => '123',
                'body' => 'body text',
                'legal_capacity_approach' => 'invalid',
            ],
            ['legal_capacity_approach' => 'The Approach to Legal Capacity (legal_capacity_approach) must be one of the following: '.implode(', ', LegalCapacityApproaches::values()).'.'],
        ],
        'court challenge invalid value' => [
            [
                'section' => '123',
                'body' => 'body text',
                'court_challenge' => 'invalid',
            ],
            ['court_challenge' => 'The Court Challenge (court_challenge) must be one of the following: '.implode(', ', ProvisionCourtChallenges::values()).'.'],
            ['court_challenge' => 'court-challenge-not-related'],
        ],
        'decision citation without court_challenge' => [
            [
                'section' => '123',
                'body' => 'body text',
                'decision_citation' => 'decision citation text',
            ],
            ['decision_citation' => 'The Decision Citation (decision_citation) requires the Court Challenge (court_challenge) indicate that a challenge has been initiated; the current value is: empty'],
        ],
        'decision citation not related court_challenge' => [
            [
                'section' => '123',
                'body' => 'body text',
                'court_challenge' => ProvisionCourtChallenges::NotRelated->value,
                'decision_citation' => 'decision citation text',
            ],
            ['decision_citation' => 'The Decision Citation (decision_citation) requires the Court Challenge (court_challenge) indicate that a challenge has been initiated; the current value is: not_related'],
        ],
    ];
});
