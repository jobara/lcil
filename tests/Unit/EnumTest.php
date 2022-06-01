<?php

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LawPolicyTypes;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionDecisionTypes;

test('Values trait in LegalCapacityApproaches returns case values', function () {
    $values = LegalCapacityApproaches::values();
    expect($values)->toBe(array_column(LegalCapacityApproaches::cases(), 'value'));
});

test('Values trait in DecisionMakingCapabilities returns case values', function () {
    $values = DecisionMakingCapabilities::values();
    expect($values)->toBe(array_column(DecisionMakingCapabilities::cases(), 'value'));
});

test('Values trait in LawPolicyTypes returns case values', function () {
    $values = LawPolicyTypes::values();
    expect($values)->toBe(array_column(LawPolicyTypes::cases(), 'value'));
});

test('Values trait in ProvisionDecisionTypes returns case values', function () {
    $values = ProvisionDecisionTypes::values();
    expect($values)->toBe(array_column(ProvisionDecisionTypes::cases(), 'value'));
});
