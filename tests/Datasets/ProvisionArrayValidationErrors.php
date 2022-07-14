<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;

// Split these off form ProvisionValidationErrors dataset due to Hearth #149
// https://github.com/fluid-project/hearth/issues/149

dataset('provisionArrayValidationErrors', function () {
    return [
        'decision type invalid value' => [
            [
                'section' => '123',
                'body' => 'body text',
                'court_challenge' => ProvisionCourtChallenges::ResultOf->value,
                'decision_type' => ['invalid'],
            ],
            ['decision_type.0' => 'The Type of Decision (decision_type) must only include the following: ' . implode(', ', ProvisionDecisionTypes::values()) . '.'],
            ['decision_type' => 'decision_type-financial_property'],
        ],
        'decision making capability invalid value' => [
            [
                'section' => '123',
                'body' => 'body text',
                'decision_making_capability' => ['other'],
            ],
            ['decision_making_capability.0' => 'The Decision Making Capability (decision_making_capability) must only include the following: ' . implode(', ', DecisionMakingCapabilities::values()) . '.'],
            ['decision_making_capability' => 'decision_making_capability-independent'],
        ],
    ];
});
