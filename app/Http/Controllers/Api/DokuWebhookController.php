<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Doku\DokuSignatureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    public function __construct(
        protected DokuSignatureService $signatureService
    ) {}

    /**
     * FR-PAY-02: DOKU notification receiver webhook.
     * Verifies signature, timestamp, amount, and Request-Id idempotency.
     */
    public function handle(Request $request): JsonResponse
    {
        $clientId = (string) $request->header('Client-Id', '');
        $requestId = (string) $request->header('Request-Id', '');
        $timestamp = (string) $request->header('Request-Timestamp', '');
        $signature = (string) $request->header('Signature', '');
        $targetPath = '/api/payments/doku/notification';
        $rawBody = $request->getContent();
        $secretKey = (string) config('doku.shared_key', env('DOKU_SHARED_KEY', ''));

        // 1. Verify HMAC-SHA256 signature
        $isSignatureValid = $this->signatureService->verifyWebhookSignature(
            $clientId,
            $requestId,
            $timestamp,
            $targetPath,
            $rawBody,
            $secretKey,
            $signature
        );

        if (! $isSignatureValid) {
            Log::warning('DOKU webhook invalid signature attempt', [
                'request_id' => $requestId,
                'signature' => $signature,
            ]);

            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->json()->all();
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;
        $amount = $payload['order']['amount'] ?? null;
        $status = strtoupper($payload['transaction']['status'] ?? '');

        if (! $invoiceNumber) {
            return response()->json(['message' => 'Invoice number missing.'], 400);
        }

        $order = Order::where('order_code', $invoiceNumber)->with('payment')->first();

        if (! $order || ! $order->payment) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $payment = $order->payment;

        // 2. Reject webhook on historical midtrans archive
        if ($payment->provider === 'midtrans_sandbox') {
            return response()->json(['message' => 'Historical archive cannot be updated.'], 422);
        }

        // 3. Verify nominal amount
        if ((int) round($order->total_amount) !== (int) $amount) {
            Log::warning('DOKU webhook amount mismatch', [
                'expected' => $order->total_amount,
                'received' => $amount,
            ]);

            return response()->json(['message' => 'Amount mismatch.'], 400);
        }

        // 4. Idempotency check via Request-Id
        if ($payment->last_request_id === $requestId && $payment->status === PaymentStatus::PAID) {
            return response()->json(['message' => 'Already processed idempotently.'], 200);
        }

        // 5. Update status
        if ($status === 'SUCCESS') {
            $payment->update([
                'status' => PaymentStatus::PAID,
                'paid_at' => now(),
                'last_request_id' => $requestId,
                'raw_response' => $payload,
            ]);

            if ($order->status === OrderStatus::PENDING_PAYMENT) {
                $order->update(['status' => OrderStatus::PAID]);
            }
        } elseif (in_array($status, ['FAILED', 'EXPIRED', 'CANCELLED'])) {
            $payment->update([
                'status' => PaymentStatus::EXPIRED,
                'last_request_id' => $requestId,
                'raw_response' => $payload,
            ]);

            app(\App\Services\OrderStatusService::class)->cancelOrder($order, 'Dibatalkan/Kadaluarsa dari webhook DOKU');
        }

        return response()->json(['message' => 'Webhook processed successfully.']);
    }
}
