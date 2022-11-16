<?php

namespace Database\Factories;

use App\Enums\EvaluationAssessments;
use App\Models\Measure;
use App\Models\Provision;
use App\Models\RegimeAssessment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'regime_assessment_id' => RegimeAssessment::factory(),
            'measure_id' => Measure::factory(),
            'provision_id' => Provision::factory(),
            'assessment' => $this->faker->randomElement(EvaluationAssessments::values()),
            'comment' => $this->faker->boolean(50) ?
                $this->faker->paragraph() :
                null,
        ];
    }
}
