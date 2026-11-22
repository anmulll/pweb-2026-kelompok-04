<?php

namespace Tests\Feature\Payment;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\Doku\DokuSignatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokuPaymentAndWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;
    protected User $seller;
    protected DokuSignatureService $signatureService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = User::factory()->buyer()->create();
        $this->seller = User::factory()->seller()->create();
        $this->signatureService = new DokuSignatureService();
        config(['doku.shared_key' => 'test-secret-key']);
    }

    public function test_doku_webhook_verifies_signature_amount_and_updates_status_to_paid(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'total_amount' => 15000000,
            'status' => OrderStatus::PENDING_PAYMENT,
        ]);

        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'amount' => 15000000,
            'status' => PaymentStatus::PENDING,
            'provider' => 'doku_sandbox',
        ]);

        $payload = [
            'order' => [
                'invoice_number' => $order->order_code,
                'amount' => 15000000,
            ],
            'transaction' => [
                'status' => 'SUCCESS',
                'date' => now()->toIso8601String(),
            ],
        ];

        $rawBody = json_encode($payload);
        $clientId = 'CLIENT-TEST-001';
        $requestId = 'REQ-' . uniqid();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $targetPath = '/api/payments/doku/notification';

        $signature = $this->signatureService->generateSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            'test-secret-key'
        );

        $response = $this->withHeaders([
            'Client-Id' => $clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ])->postJson($targetPath, $payload);

        $response->assertStatus(200);

        $this->assertSame(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertSame(OrderStatus::PAID, $order->fresh()->status);
    }

    public function test_doku_webhook_rejects_invalid_signature(): void
    {
        $targetPath = '/api/payments/doku/notification';

        $response = $this->withHeaders([
            'Client-Id' => 'CLIENT-TEST-001',
            'Request-Id' => 'REQ-FAKE',
            'Request-Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
            'Signature' => 'HMACSHA256=invalidSignature123',
            'Content-Type' => 'application/json',
        ])->postJson($targetPath, [
            'order' => ['invoice_number' => 'ORD-FAKE', 'amount' => 1000],
        ]);

        $response->assertStatus(401);
    }

    public function test_buyer_and_seller_can_poll_payment_status_endpoint(): void
    {
        $order = Order::factory()->create([
            'user_id' => $this->buyer->id,
            'status' => OrderStatus::PENDING_PAYMENT,
        ]);

        Payment::factory()->create([
            'order_id' => $order->id,
            'status' => PaymentStatus::PENDING,
            'provider' => 'doku_sandbox',
        ]);

        // Buyer polls status
        $buyerResponse = $this->actingAs($this->buyer)->getJson(route('buyer.orders.payment.status', $order));
        $buyerResponse->assertStatus(200)
            ->assertJsonStructure(['order_id', 'order_code', 'order_status', 'payment_status']);

        // Seller polls status
        $sellerResponse = $this->actingAs($this->seller)->getJson(route('seller.orders.payment.status', $order));
        $sellerResponse->assertStatus(200)
            ->assertJsonStructure(['order_id', 'order_code', 'order_status', 'payment_status']);
    }
}
