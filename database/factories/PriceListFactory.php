<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PriceList>
 */
class PriceListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Random date range for promotions or seasonal pricing
        $startDate = Carbon::now()->subDays(rand(0, 30));
        $endDate = Carbon::now()->addDays(rand(1, 60));

        return [
            'product_id'    => Product::factory(),
            'country_code'  => $this->faker->randomElement([null, 'US', 'CA', 'DE', 'SA']),
            'country_id'    => Country::factory(),
            'currency_code' => $this->faker->randomElement([null, 'USD', 'CAD', 'EUR', 'SAR']),
            'currency_id'    => Currency::factory(),
            'price'         => $this->faker->randomFloat(2, 10, 500),
            'starts_at'    => $startDate,
            'ends_at'      => $endDate,
            'priority'      => $this->faker->numberBetween(1, 5),
        ];
    }
}
