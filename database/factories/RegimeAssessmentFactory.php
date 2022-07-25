<?php

namespace Database\Factories;

use App\Enums\RegimeAssessmentStatuses;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegimeAssessment>
 */
class RegimeAssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subdivisionRepository = new SubdivisionRepository();

        $country = $this->faker->boolean(50) ?
            $this->faker->randomElement(['CA', 'US']) :
            $this->faker->randomElement(array_keys(get_countries()));

        $province = $this->faker->boolean(70) ?
            $this->faker->randomElement($subdivisionRepository->getAll([$country])) :
            null;

        $jurisdiction = $province ?
            $province->getIsoCode() ?? $country :
            $country;

        return [
            'jurisdiction' => $jurisdiction,
            'municipality' => $province && $this->faker->boolean(20) ?
                $this->faker->word() :
                null,
            'description' => $this->faker->boolean(50) ?
                $this->faker->paragraph() :
                null,
            'year_in_effect' => $this->faker->boolean(50) ?
                $this->faker->numberBetween(config('settings.year.min'), config('settings.year.max')) :
                null,
            'status' => $this->faker->randomElement(RegimeAssessmentStatuses::values()),
        ];
    }
}
