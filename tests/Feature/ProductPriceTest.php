<?php

namespace Tests\Feature;

use App\Exceptions\PriceListAmbiguityException;
use App\Models\Product;
use App\Models\PriceList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Carbon\Carbon;

class ProductPriceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->product = Product::factory()->create([
            'base_price' => 100.00
        ]);
    }

    #[Test]
    public function it_returns_base_price_if_no_price_list_exists()
    {
        $response = $this->getJson("/api/products/{$this->product->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.id', $this->product->id)
            ->assertJsonPath('data.price', 100);
    }

    #[Test]
    public function it_applies_price_list_for_specific_country_and_currency()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 90.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 90);
    }

    #[Test]
    public function it_falls_back_to_general_price_if_country_specific_price_is_not_available()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => null,
            'currency_code' => 'USD',
            'price' => 95.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 2,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 95);
    }

    #[Test]
    public function it_falls_back_to_base_price_if_no_valid_price_list_exists()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'CA',
            'currency_code' => 'CAD',
            'price' => 85.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 100);
    }

    #[Test]
    public function it_applies_price_list_with_the_lowest_priority_if_multiple_exist()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 95.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 2,
        ]);

        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 90.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 90);
    }

    #[Test]
    public function it_applies_price_only_if_current_date_is_within_valid_range()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 80.00,
            'starts_at' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'ends_at' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 100);
    }

    #[Test]
    public function it_applies_general_price_if_no_country_or_currency_provided()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => null,
            'currency_code' => null,
            'price' => 88.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}");
        $response->assertStatus(200)->assertJsonPath('data.price', 88);
    }

    #[Test]
    public function it_throws_exception_if_multiple_price_lists_have_same_priority_and_criteria()
    {
        $this->expectException(PriceListAmbiguityException::class);

        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 90.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 85.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
    }

    #[Test]
    public function it_ignores_expired_price_lists()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 75.00,
            'starts_at' => '2020-01-01',
            'ends_at' => Carbon::now()->subDay()->format('Y-m-d'),
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 100);
    }

    #[Test]
    public function it_applies_specific_price_over_general_price_with_same_priority()
    {
        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => null,
            'currency_code' => 'USD',
            'price' => 95.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        PriceList::factory()->create([
            'product_id' => $this->product->id,
            'country_code' => 'US',
            'currency_code' => 'USD',
            'price' => 85.00,
            'starts_at' => '2020-01-01',
            'ends_at' => '2099-12-31',
            'priority' => 1,
        ]);

        $response = $this->getJson("/api/products/{$this->product->id}?country_code=US&currency_code=USD");
        $response->assertStatus(200)->assertJsonPath('data.price', 85);
    }

}
