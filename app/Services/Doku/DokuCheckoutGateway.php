<?php

namespace App\Services\Doku;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DokuCheckoutGateway implements PaymentGatewayInterface
{
    protected string $clientId;
    protected string $sharedKey;
    protected string $baseUrl;
    protected bool $isProduction;
    protected DokuSignatureService $signatureService;

    public function __construct(DokuSignatureService $signatureService)
    {
        $this->clientId = (string) config('doku.client_id', env('DOKU_CLIENT_ID', ''));
        $this->sharedKey = (string) config('doku.shared_key', env('DOKU_SHARED_KEY', ''));
        $this->baseUrl = (string) config('doku.base_url', 'https://api-sandbox.doku.com');
        $this->isProduction = (bool) config('doku.is_production', false);
        $this->signatureService = $signatureService;

        // FR-PAY-01 & Architecture ADR-01: Runtime guard strictly rejects production mode
        if ($this->isProduction) {
            throw new \RuntimeException('DOKU Production mode is strictly disabled in this version. Use Sandbox only.');
        }
    }

    public function createPayment(Order $order): array
    {
        $order->loadMissing(['items', 'user']);

        $endpoint = '/checkout/v1/payment';
        $requestId = 'REQ-' . $order->order_code . '-' . time();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $body = [
            'order' => [
                'invoice_number' => $order->order_code,
                'amount' => (int) round($order->total_amount),
                'callback_url' => route('buyer.orders.show', $order->id),
                'auto_redirect' => true,
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 minutes expiry
            ],
            'customer' => [
                'name' => $order->recipient_name,
                'email' => $order->user->email ?? 'buyer@istore.test',
                'phone' => $order->recipient_phone,
                'address' => $order->shipping_address,
            ],
        ];

        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->signatureService->generateSignature(
            $this->clientId,
            $requestId,
            $timestamp,
            $endpoint,
            $jsonBody,
            $this->sharedKey
        );

        $headers = [
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(15)
                ->post($this->baseUrl . $endpoint, $body);

            $data = $response->json() ?? [];

            if ($response->successful() && ! empty($data['response']['payment']['url'])) {
                $checkoutUrl = $data['response']['payment']['url'];
                $token = $data['response']['payment']['token_id'] ?? null;
                $expiredAt = now()->addMinutes(60)->toDateTimeString();

                return [
                    'gateway_checkout_url' => $checkoutUrl,
                    'gateway_token' => $token,
                    'expired_at' => $expiredAt,
                    'raw_response' => $data,
                ];
            }

            Log::warning('DOKU createPayment failed response', [
                'order_code' => $order->order_code,
                'status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'gateway_checkout_url' => null,
                'gateway_token' => null,
                'expired_at' => null,
                'raw_response' => $data,
            ];
        } catch (Exception $e) {
            Log::error('DOKU createPayment exception: ' . $e->getMessage(), [
                'order_code' => $order->order_code,
            ]);

            return [
                'gateway_checkout_url' => null,
                'gateway_token' => null,
                'expired_at' => null,
                'raw_response' => ['error' => $e->getMessage()],
            ];
        }
    }

    public function checkStatus(Order $order): array
    {
        $endpoint = '/orders/v1/status/' . $order->order_code;
        $requestId = 'INQ-' . $order->order_code . '-' . time();
        $timestamp = gmdate("Y-m-d\TH:i:s\Z");

        $signature = $this->signatureService->generateSignature(
            $this->clientId,
            $requestId,
            $timestamp,
            $endpoint,
            '',
            $this->sharedKey
        );

        $headers = [
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $timestamp,
            'Signature' => $signature,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->get($this->baseUrl . $endpoint);

            return $response->json() ?? [];
        } catch (Exception $e) {
            Log::error('DOKU checkStatus exception: ' . $e->getMessage(), [
                'order_code' => $order->order_code,
            ]);

            return ['error' => $e->getMessage()];
        }
    }
}
