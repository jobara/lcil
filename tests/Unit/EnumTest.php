<?php

use App\Enums\ApproachToLegalCapacityEnum;
use App\Enums\DecisionMakingCapabilityEnum;
use App\Enums\LawPolicyTypeEnum;
use App\Enums\ProvisionDecisionTypeEnum;

test('Values trait in ApproachToLegalCapacityEnum returns case values', function () {
    $values = ApproachToLegalCapacityEnum::values();
    expect($values)->toBe(array_column(ApproachToLegalCapacityEnum::cases(), 'value'));
});

test('Values trait in DecisionMakingCapabilityEnum returns case values', function () {
    $values = DecisionMakingCapabilityEnum::values();
    expect($values)->toBe(array_column(DecisionMakingCapabilityEnum::cases(), 'value'));
});

test('Values trait in LawPolicyTypeEnum returns case values', function () {
    $values = LawPolicyTypeEnum::values();
    expect($values)->toBe(array_column(LawPolicyTypeEnum::cases(), 'value'));
});

test('Values trait in ProvisionDecisionTypeEnum returns case values', function () {
    $values = ProvisionDecisionTypeEnum::values();
    expect($values)->toBe(array_column(ProvisionDecisionTypeEnum::cases(), 'value'));
});
