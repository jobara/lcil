<?php

namespace Database\Factories;

use App\Enums\LegalChallengeTypeEnum;
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
    public function definition()
    {
        $challengeTypes = array_column(LegalChallengeTypeEnum::cases(), 'value');

        $isSubToChallenge = $this->faker->boolean(50) ? $this->faker->boolean() : null;
        $isResOfChallenge = $this->faker->boolean(50) ? $this->faker->boolean() : null;

        $isChallenged = $isSubToChallenge || $isResOfChallenge;

        return [
            'law_policy_source_id' => LawPolicySource::factory(),
            'section' => $this->faker->unique()->regexify('[a-zA-Z0-9]{1,2} [a-zA-Z0-9]{0,2}'),
            'type_of_decision' => $this->faker->boolean(50) ? $this->faker->randomElement($challengeTypes) : null,
            'body' => $this->faker->paragraph(3),
            'reference' => $this->faker->boolean(80) ? $this->faker->unique()->url() : null,
            'is_subject_to_challenge' => $isSubToChallenge,
            'is_result_of_challenge' => $isResOfChallenge,
            'decision_citation' => $this->faker->boolean(50) && $isChallenged ? $this->faker->paragraph() : null,
        ];
    }
}
