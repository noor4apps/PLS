<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([null, 'United States', 'Canada', 'Germany', 'Saudi Arabia']),
            'code' => $this->faker->randomElement([null, 'US', 'CA', 'DE', 'SA'])
        ];
    }
}
