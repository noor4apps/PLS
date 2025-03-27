<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductIndexTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->product1 = Product::factory()->create(['base_price' => 50.00]);
        $this->product2 = Product::factory()->create(['base_price' => 100.00]);
        $this->product3 = Product::factory()->create(['base_price' => 75.00]);
    }

    #[Test]
    public function it_returns_all_products()
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['id' => $this->product1->id])
            ->assertJsonFragment(['id' => $this->product2->id])
            ->assertJsonFragment(['id' => $this->product3->id]);
    }

    #[Test]
    public function it_sorts_products_by_lowest_to_highest_price()
    {
        $response = $this->getJson('/api/products?order=lowest-to-highest');

        $response->assertStatus(200);
        $products = $response->json('data');

        $this->assertEquals($this->product1->id, $products[0]['id']);
        $this->assertEquals($this->product3->id, $products[1]['id']);
        $this->assertEquals($this->product2->id, $products[2]['id']);
    }

    #[Test]
    public function it_sorts_products_by_highest_to_lowest_price()
    {
        $response = $this->getJson('/api/products?order=highest-to-lowest');

        $response->assertStatus(200);
        $products = $response->json('data');

        $this->assertEquals($this->product2->id, $products[0]['id']);
        $this->assertEquals($this->product3->id, $products[1]['id']);
        $this->assertEquals($this->product1->id, $products[2]['id']);
    }
}
