<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $unitPrice = Product::factory()->create()->price;
        $quantity = rand(1, 5);
        $total = $unitPrice * $quantity;

        return [
            'order_id' => Orders::factory()->create()->id,
            'product_id' => Product::factory()->create()->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
        ];
    }
}
