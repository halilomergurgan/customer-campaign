<?php

namespace Tests\Feature\OrderItem;

use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Orders;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderItemTest extends TestCase
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
    public function user_can_store_a_order_item()
    {
        $customer = Customer::factory()->create();

        $order = Orders::factory()->create(['customer_id' => $customer->id]);

        $newOrderItemData = OrderItem::factory()->raw(['order_id' => $order->id]);

        $response = $this->json('POST', $this->uri . '/' . $customer->id . '/add-item', $newOrderItemData);

        $newOrderItemData['id'] = $response->json('data')['id'];
        $newOrderItemData['total'] = $response->json('data')['total'];
        $newOrderItemData['unit_price'] = $response->json('data')['unit_price'];

        $response->assertOk()
            ->assertJsonFragment($newOrderItemData);

        $this->assertDatabaseHas('order_items', $newOrderItemData);
    }

    /** @test */
    public function user_can_delete_a_order_item()
    {
        $order = Orders::factory()->create();

        $orderItem = OrderItem::factory()->create(['order_id' => $order->id]);

        $response = $this->json('DELETE', $this->uri . '/' . $order->id . '/delete-item/' . $orderItem->id);

        $response->assertOk();

        $this->assertSoftDeleted('order_items', ['id' => $orderItem->id]);
    }
}
