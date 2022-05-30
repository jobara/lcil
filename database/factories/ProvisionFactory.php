<?php

namespace Database\Factories;

use App\Enums\ApproachToLegalCapacityEnum;
use App\Enums\DecisionMakingCapabilityEnum;
use App\Enums\ProvisionDecisionTypeEnum;
use App\Models\LawPolicySource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provision>
 */
class ProvisionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $challengeTypes = ProvisionDecisionTypeEnum::values();
        $numChallengeTypesToSelect = $this->faker->numberBetween(1, count($challengeTypes));

        $isSubToChallenge = $this->faker->boolean(50) ? $this->faker->boolean() : null;
        $isResOfChallenge = $this->faker->boolean(50) ? $this->faker->boolean() : null;

        $isChallenged = $isSubToChallenge || $isResOfChallenge;

        return [
            'law_policy_source_id' => LawPolicySource::factory(),
            'section' => $this->faker->unique()->regexify('[a-zA-Z0-9]{1,2} [a-zA-Z0-9]{0,2}'),
            'decision_type' => $this->faker->boolean(50) ? $this->faker->randomElements($challengeTypes, $numChallengeTypesToSelect) : null,
            'legal_capacity_approach' => $this->faker->boolean(50) ? $this->faker->randomElement(ApproachToLegalCapacityEnum::values()) : null,
            'decision_making_capability' => $this->faker->boolean(50) ? $this->faker->randomElement(DecisionMakingCapabilityEnum::values()) : null,
            'body' => $this->faker->paragraph(3),
            'reference' => $this->faker->boolean(80) ? $this->faker->unique()->url() : null,
            'is_subject_to_challenge' => $isSubToChallenge,
            'is_result_of_challenge' => $isResOfChallenge,
            'decision_citation' => $this->faker->boolean(50) && $isChallenged ? $this->faker->paragraph() : null,
        ];
    }
}
