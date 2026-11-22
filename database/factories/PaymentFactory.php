<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'gateway_order_id' => 'DOKU-' . Str::uuid()->toString(),
            'provider' => 'doku_sandbox',
            'status' => PaymentStatus::PENDING,
            'amount' => 5000000,
            'gateway_checkout_url' => 'https://sandbox.doku.com/checkout/' . Str::random(20),
            'gateway_token' => Str::random(32),
            'last_request_id' => Str::uuid()->toString(),
            'expired_at' => now()->addHours(2),
            'paid_at' => null,
            'raw_response' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);
    }

    public function midtrans(): static
    {
        return $this->state(fn (array $attributes) => [
            'provider' => 'midtrans_sandbox',
            'status' => PaymentStatus::PAID,
            'paid_at' => now()->subMonths(1),
        ]);
    }
}
