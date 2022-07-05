<?php

namespace Database\Factories;

use App\Enums\DecisionMakingCapabilities;
use App\Enums\LegalCapacityApproaches;
use App\Enums\ProvisionCourtChallenges;
use App\Enums\ProvisionDecisionTypes;
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
        $challengeTypes = ProvisionDecisionTypes::values();
        $numChallengeTypesToSelect = $this->faker->numberBetween(1, count($challengeTypes));
        $capabilities = DecisionMakingCapabilities::values();
        $capabilitiesToSelect = $this->faker->numberBetween(1, count($capabilities));

        $courtChallenge = $this->faker->boolean(50) ?
            $this->faker->randomElement(ProvisionCourtChallenges::values()) :
            null;

        $isChallenged = $courtChallenge === ProvisionCourtChallenges::SubjectTo || ProvisionCourtChallenges::ResultOf;

        return [
            'law_policy_source_id' => LawPolicySource::factory(),
            'section' => $this->faker->unique()->regexify('[a-zA-Z0-9]{1,2} [a-zA-Z0-9]{0,2}'),
            'decision_type' => $this->faker->boolean(50) ?
                $this->faker->randomElements($challengeTypes, $numChallengeTypesToSelect) :
                null,
            'legal_capacity_approach' => $this->faker->boolean(50) ?
                $this->faker->randomElement(LegalCapacityApproaches::values()) :
                null,
            'decision_making_capability' => $this->faker->boolean(50) ?
                $this->faker->randomElements($capabilities, $capabilitiesToSelect) :
                null,
            'body' => $this->faker->paragraph(3),
            'reference' => $this->faker->boolean(80) ?
                $this->faker->unique()->url() :
                null,
            'court_challenge' => $this->faker->boolean(50) ?
                $this->faker->randomElement(ProvisionCourtChallenges::values()) :
                null,
            'decision_citation' => $this->faker->boolean(50) && $isChallenged ?
                $this->faker->paragraph() :
                null,
        ];
    }
}
