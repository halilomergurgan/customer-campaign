<?php

namespace Tests\Feature\Product;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    const BASE_URI = '/api';
    protected string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = self::BASE_URI . "/products";
    }

    /** @test */
    public function user_can_list_all_products()
    {
        $category = Category::factory()->create();

        $products = Product::factory()->count(rand(1, 10))->create(['category' => $category->id]);

        $response = $this->json('GET', $this->uri);

        $response->assertOk()
            ->assertSee('data');

        foreach ($products as $product) {
            $product['price'] = number_format($product['price'], 2, '.', '');

            $response->assertJsonFragment($product->only('id', 'name', 'price', 'stock'));
        }
    }

    /** @test */
    public function user_can_store_a_products()
    {
        $newProductData = Product::factory()->raw();

        $response = $this->json('POST', $this->uri, $newProductData);

        $response->assertOk()
            ->assertJsonFragment($newProductData);

        $newProductData['id'] = $response->json('data')['id'];

        $this->assertDatabaseHas('products', $newProductData);
    }

    /** @test */
    public function user_can_update_product_stock_entry()
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create();

        $newProductData = Product::factory()->raw(['category' => $category->id]);

        $response = $this->json('PUT', $this->uri . '/' . $product->id, $newProductData);

        $response->assertStatus(200);

        $this->assertDatabaseHas(
            'products',
            array_merge(['id' => $product->id], $newProductData)
        );
    }

    /** @test */
    public function user_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->json('DELETE', $this->uri . '/' . $product->id);

        $response->assertOk();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
