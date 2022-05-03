<?php

namespace Tests\Unit;

use App\Enums\ApproachToLegalCapacityEnum;
use App\Enums\DecisionMakingCapabilityEnum;
use App\Enums\LawPolicyTypeEnum;
use App\Enums\ProvisionDecisionTypeEnum;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    /**
     * Ensures the ApproachToLegalCapacityEnum contains the Values trait
     *
     * @return void
     */
    public function test_values_trait_in_ApproachToLegalCapacityEnum()
    {
        $expected = array_column(ApproachToLegalCapacityEnum::cases(), 'value');
        $this->assertEquals($expected, ApproachToLegalCapacityEnum::values());
    }

    /**
     * Ensures the DecisionMakingCapabilityEnum contains the Values trait
     *
     * @return void
     */
    public function test_values_trait_in_DecisionMakingCapabilityEnum()
    {
        $expected = array_column(DecisionMakingCapabilityEnum::cases(), 'value');
        $this->assertEquals($expected, DecisionMakingCapabilityEnum::values());
    }

    /**
     * Ensures the LawPolicyTypeEnum contains the Values trait
     *
     * @return void
     */
    public function test_values_trait_in_LawPolicyTypeEnum()
    {
        $expected = array_column(LawPolicyTypeEnum::cases(), 'value');
        $this->assertEquals($expected, LawPolicyTypeEnum::values());
    }

    /**
     * Ensures the ProvisionDecisionTypeEnum contains the Values trait
     *
     * @return void
     */
    public function test_values_trait_in_ProvisionDecisionTypeEnum()
    {
        $expected = array_column(ProvisionDecisionTypeEnum::cases(), 'value');
        $this->assertEquals($expected, ProvisionDecisionTypeEnum::values());
    }
}
