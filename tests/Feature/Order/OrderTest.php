<?php

namespace Tests\Feature\Order;

use App\Models\Orders;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use DatabaseTransactions;

    const BASE_URI = '/api';
    protected string $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uri = self::BASE_URI . "/orders";
    }

    /** @test */
    public function user_can_list_all_orders()
    {
        $orders = Orders::factory()->count(rand(1, 10))->create();

        $response = $this->json('GET', $this->uri);

        $response->assertOk()
            ->assertSee('data');

        foreach ($orders as $order) {
            $order['total'] = number_format($order['total'], 2, '.', '');

            $response->assertJsonFragment($order->toArray());
        }
    }

    /** @test */
    public function user_can_store_a_order()
    {
        $newOrderData = Orders::factory()->raw();

        $response = $this->json('POST', $this->uri, $newOrderData);

        $newOrderData['id'] = $response->json('data')['id'];

        $response->assertOk()
            ->assertJsonFragment($newOrderData);

        $this->assertDatabaseHas('orders', $newOrderData);
    }

    /** @test */
    public function user_can_delete_a_order()
    {
        $order = Orders::factory()->create();

        $response = $this->json('DELETE', $this->uri . '/' . $order->id);

        $response->assertOk();

        $this->assertSoftDeleted('orders', ['id' => $order->id]);
    }
}
