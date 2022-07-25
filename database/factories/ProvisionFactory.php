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
        $capabilities = DecisionMakingCapabilities::values();
        $capabilitiesToSelect = $this->faker->numberBetween(1, count($capabilities));

        $body = '<p><strong><em>Example <u>Provision</u> Text</em></strong></p><ol><li><p>Some details</p></li>
                <li><p><p>Some more</p><ul><li><p>sub point</p></li><li><p><strike>sub point removed</strike></p>
                </li></ul></p></li></ol>';

        return [
            'law_policy_source_id' => LawPolicySource::factory(),
            'section' => $this->faker->unique()->regexify('[a-zA-Z0-9]{1,2} [a-zA-Z0-9]{0,2}'),
            'legal_capacity_approach' => $this->faker->boolean(50) ?
                $this->faker->randomElement(LegalCapacityApproaches::values()) :
                null,
            'decision_making_capability' => $this->faker->boolean(50) ?
                $this->faker->randomElements($capabilities, $capabilitiesToSelect) :
                null,
            'body' => $body,
            'reference' => $this->faker->boolean(80) ?
                $this->faker->unique()->url() :
                null,
            'court_challenge' => $this->faker->boolean(50) ?
                $this->faker->randomElement(ProvisionCourtChallenges::values()) :
                null,
            'decision_citation' => function (array $attributes) {
                $isChallenged = isset($attributes['court_challenge']) && ProvisionCourtChallenges::tryFrom($attributes['court_challenge']) !== ProvisionCourtChallenges::NotRelated;

                return $this->faker->boolean(50) && $isChallenged ?
                    $this->faker->paragraph() :
                    null;
            },
            'decision_type' => function (array $attributes) {
                $provisionDecisionTypes = ProvisionDecisionTypes::values();
                $numProvisionDecisionTypesToSelect = $this->faker->numberBetween(1, count($provisionDecisionTypes));
                $isChallenged = isset($attributes['court_challenge']) && ProvisionCourtChallenges::tryFrom($attributes['court_challenge']) !== ProvisionCourtChallenges::NotRelated;

                return $this->faker->boolean(50) && $isChallenged ?
                    $this->faker->randomElements($provisionDecisionTypes, $numProvisionDecisionTypesToSelect) :
                    null;
            },
        ];
    }
}
