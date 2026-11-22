<?php

namespace Database\Factories;

use App\Enums\ProductCondition;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $price = fake()->numberBetween(1000000, 20000000);
        $quantity = 1;

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_name_snapshot' => fake()->words(3, true),
            'sku_snapshot' => 'SKU-' . strtoupper(fake()->bothify('??###??')),
            'condition_snapshot' => ProductCondition::NEW,
            'condition_grade_snapshot' => null,
            'battery_health_snapshot' => null,
            'functional_status_snapshot' => null,
            'defect_notes_snapshot' => null,
            'included_accessories_snapshot' => null,
            'warranty_info_snapshot' => null,
            'unit_price_snapshot' => $price,
            'quantity' => $quantity,
            'line_total' => $price * $quantity,
        ];
    }
}
