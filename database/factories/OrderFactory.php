<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(1000000, 25000000);
        $shippingCost = 50000;

        return [
            'user_id' => User::factory()->buyer(),
            'order_code' => 'ORD-' . strtoupper(Str::random(10)),
            'status' => OrderStatus::PENDING_PAYMENT,
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->numerify('08##########'),
            'shipping_address' => fake()->address(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $subtotal + $shippingCost,
            'courier_name' => null,
            'tracking_number' => null,
            'stock_restored_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PAID,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PROCESSING,
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::SHIPPED,
            'courier_name' => 'JNE Express',
            'tracking_number' => 'JNE' . fake()->numerify('##########'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::COMPLETED,
            'courier_name' => 'JNE Express',
            'tracking_number' => 'JNE' . fake()->numerify('##########'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED,
            'stock_restored_at' => now(),
        ]);
    }
}
