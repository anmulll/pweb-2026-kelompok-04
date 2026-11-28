<?php

namespace Tests\Feature\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\Doku\DokuSignatureService;
use App\Services\PaymentGatewayResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokuPaymentAndWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $otherBuyer;
    protected User $seller;
    protected Category $category;
    protected Product $product;
    protected Order $order;
    protected Payment $payment;
    protected string $sharedKey = 'SHARED_KEY_SANDBOX_TEST';
    protected string $clientId = 'MALLID_SANDBOX_TEST';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'doku.client_id' => $this->clientId,
            'doku.shared_key' => $this->sharedKey,
            'doku.notification_path' => '/api/payments/doku/notification',
            'doku.is_production' => false,
        ]);

        $this->buyer = User::factory()->buyer()->create();
        $this->otherBuyer = User::factory()->buyer()->create();
        $this->seller = User::factory()->seller()->create();

        $this->category = Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 15 Pro 128GB',
            'model' => 'iPhone 15 Pro',
            'sku' => 'IP15P-128-TEST',
            'condition' => 'new',
            'price' => 19999000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'ORD-TEST-001',
            'status' => OrderStatus::PENDING_PAYMENT,
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 45',
            'subtotal' => 19999000,
            'shipping_cost' => 0,
            'total_amount' => 19999000,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'product_name_snapshot' => $this->product->name,
            'sku_snapshot' => $this->product->sku,
            'condition_snapshot' => 'new',
            'unit_price_snapshot' => 19999000,
            'quantity' => 1,
            'line_total' => 19999000,
        ]);

        $this->payment = Payment::create([
            'order_id' => $this->order->id,
            'gateway_order_id' => $this->order->order_code,
            'provider' => 'doku_sandbox',
            'status' => PaymentStatus::PENDING,
            'amount' => 19999000,
        ]);
    }

    public function test_valid_doku_webhook_updates_payment_and_order_to_paid(): void
    {
        $payload = [
            'order' => [
                'invoice_number' => $this->order->order_code,
                'amount' => 19999000,
            ],
            'transaction' => [
                'status' => 'SUCCESS',
                'date' => now()->toIso8601String(),
            ],
        ];

        $jsonBody = json_encode($payload);
        $requestId = 'REQ-NOTIF-001';
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $signatureService = app(DokuSignatureService::class);
        $signature = $signatureService->generateSignature(
            $this->clientId,
            $requestId,
            $timestamp,
            '/api/payments/doku/notification',
            $jsonBody,
            $this->sharedKey
        );

        $response = $this->withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson('/api/payments/doku/notification', $payload);

        $response->assertStatus(200);

        $this->assertEquals(PaymentStatus::PAID, $this->payment->fresh()->status);
        $this->assertEquals(OrderStatus::PAID, $this->order->fresh()->status);
        $this->assertNotNull($this->payment->fresh()->paid_at);
        $this->assertEquals($requestId, $this->payment->fresh()->last_request_id);
    }

    public function test_duplicate_webhook_processed_idempotently(): void
    {
        $this->payment->update(['last_request_id' => 'REQ-NOTIF-DUP']);

        $payload = [
            'order' => [
                'invoice_number' => $this->order->order_code,
                'amount' => 19999000,
            ],
            'transaction' => ['status' => 'SUCCESS'],
        ];

        $jsonBody = json_encode($payload);
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $signature = app(DokuSignatureService::class)->generateSignature(
            $this->clientId,
            'REQ-NOTIF-DUP',
            $timestamp,
            '/api/payments/doku/notification',
            $jsonBody,
            $this->sharedKey
        );

        $response = $this->withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => 'REQ-NOTIF-DUP',
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
        ])->postJson('/api/payments/doku/notification', $payload);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Duplicate notification processed idempotently.']);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        $payload = [
            'order' => [
                'invoice_number' => $this->order->order_code,
                'amount' => 19999000,
            ],
            'transaction' => ['status' => 'SUCCESS'],
        ];

        $response = $this->withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => 'REQ-FAKE',
            'Request-Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
            'Signature' => 'HMACSHA256=invalidSignature123',
        ])->postJson('/api/payments/doku/notification', $payload);

        $response->assertStatus(401);
        $this->assertEquals(PaymentStatus::PENDING, $this->payment->fresh()->status);
    }

    public function test_webhook_with_amount_mismatch_is_rejected(): void
    {
        $payload = [
            'order' => [
                'invoice_number' => $this->order->order_code,
                'amount' => 5000000, // Should be 19999000
            ],
            'transaction' => ['status' => 'SUCCESS'],
        ];

        $jsonBody = json_encode($payload);
        $requestId = 'REQ-MISMATCH';
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $signature = app(DokuSignatureService::class)->generateSignature(
            $this->clientId,
            $requestId,
            $timestamp,
            '/api/payments/doku/notification',
            $jsonBody,
            $this->sharedKey
        );

        $response = $this->withHeaders([
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
        ])->postJson('/api/payments/doku/notification', $payload);

        $response->assertStatus(422);
        $this->assertEquals(PaymentStatus::PENDING, $this->payment->fresh()->status);
    }

    public function test_historical_midtrans_record_is_read_only_and_cannot_be_synced_or_retried(): void
    {
        $this->payment->update(['provider' => 'midtrans_sandbox']);

        $response = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.payment.sync', $this->order->id));

        $response->assertStatus(422)
            ->assertJson(['message' => 'Transaksi historis bersifat read-only.']);

        $retryResponse = $this->actingAs($this->buyer)
            ->postJson(route('buyer.orders.payment.retry', $this->order->id));

        $retryResponse->assertStatus(422);
    }

    public function test_status_polling_endpoint_is_rate_limited_and_owner_protected(): void
    {
        // Protected from other buyers
        $forbiddenResponse = $this->actingAs($this->otherBuyer)
            ->getJson(route('buyer.orders.payment.status', $this->order->id));

        $forbiddenResponse->assertStatus(403);

        // Owner can access status
        $successResponse = $this->actingAs($this->buyer)
            ->getJson(route('buyer.orders.payment.status', $this->order->id));

        $successResponse->assertStatus(200)
            ->assertJsonStructure(['order_code', 'order_status', 'payment_status', 'synced']);
    }
}
