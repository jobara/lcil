<?php

namespace Database\Factories;

use App\Models\MeasureIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Measure>
 */
class MeasureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'measure_indicator_id' => MeasureIndicator::factory(),
            'code' => $this->faker->unique()->numerify('##.##.##'),
            'description' => $this->faker->paragraph(),
            'title' => $this->faker->sentence(),
            'type' => $this->faker->sentence(),
        ];
    }
}
