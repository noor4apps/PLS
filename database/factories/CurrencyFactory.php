<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([null, 'US Dollar', 'Canadian Dollar', 'Euro', 'Saudi Riyal']),
            'code' => $this->faker->randomElement([null, 'USD', 'CAD', 'EUR', 'SAR'])
        ];
    }
}
