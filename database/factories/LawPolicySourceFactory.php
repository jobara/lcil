<?php

namespace Database\Factories;

use App\Enums\LawPolicyTypeEnum;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LawPolicySource>
 */
class LawPolicySourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $subdivisionRepository = new SubdivisionRepository();
        $country = $this->faker->boolean(50) ? $this->faker->randomElement(['CA', 'US']) : $this->faker->randomElement(array_keys(get_countries()));
        $province = $this->faker->boolean(70) ? $this->faker->randomElement($subdivisionRepository->getAll([$country])) : null;
        $jurisdiction = $province ? $province->getIsoCode() ?? $country : $country;

        return [
            'name' => $this->faker->unique()->realTextBetween($minNbChars = 15, $maxNbChars = 50, $indexSize = 2),
            'type' => $this->faker->boolean(50) ? $this->faker->randomElement(LawPolicyTypeEnum::values()) : null,
            'is_core' => $this->faker->boolean(50) ? $this->faker->boolean() : null,
            'reference' => $this->faker->boolean(80) ? $this->faker->unique()->url() : null,
            'jurisdiction' => $jurisdiction,
            'municipality' => $province && $this->faker->boolean(20) ? $this->faker->word() : null,
            'year_in_effect' => $this->faker->numberBetween(1800, 2030),
            'slug' => $this->faker->unique->slug(),
        ];
    }
}
