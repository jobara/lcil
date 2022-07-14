<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LawPolicyTypes;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;

test('Values trait', function ($enum) {
    $values = $enum::values();
    expect($values)->toBe(array_column($enum::cases(), 'value'));
})->with([
    DecisionMakingCapabilities::class,
    LawPolicyTypes::class,
    LegalCapacityApproaches::class,
    ProvisionCourtChallenges::class,
    ProvisionDecisionTypes::class,
]);
