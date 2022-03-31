<?php

namespace Database\Factories;

use App\Models\MeasureDimension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeasureIndicator>
 */
class MeasureIndicatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'measure_dimension_id' => MeasureDimension::factory(),
            'code' => $this->faker->unique()->numerify('##.##'),
            'description' => $this->faker->paragraph()
        ];
    }
}
