<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Doku\DokuSignatureService;
use App\Services\PaymentStatusSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuNotificationController extends Controller
{
    public function __construct(
        protected DokuSignatureService $signatureService,
        protected PaymentStatusSyncService $syncService
    ) {}

    /**
     * Handle incoming DOKU notification webhook.
     * Enforces HMAC-SHA256 signature verification, gross_amount validation,
     * and Request-Id idempotency per FR-PAY-02 & ADR-01.
     */
    public function handle(Request $request): JsonResponse
    {
        $clientId = (string) $request->header('Client-Id');
        $requestId = (string) $request->header('Request-Id');
        $requestTimestamp = (string) $request->header('Request-Timestamp');
        $signature = (string) $request->header('Signature');

        $configuredClientId = (string) config('doku.client_id');
        $configuredSharedKey = (string) config('doku.shared_key');
        $targetPath = (string) config('doku.notification_path', '/api/payments/doku/notification');

        $rawBody = $request->getContent();

        // 1. Signature Verification
        $isValidSignature = $this->signatureService->verifyWebhookSignature(
            $clientId,
            $requestId,
            $requestTimestamp,
            $targetPath,
            $rawBody,
            $configuredSharedKey,
            $signature
        );

        if (! $isValidSignature || $clientId !== $configuredClientId) {
            Log::warning('DOKU Webhook rejected: Invalid signature or client id', [
                'client_id' => $clientId,
                'request_id' => $requestId,
            ]);

            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $payload = $request->json()->all();
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;
        $amount = (float) ($payload['order']['amount'] ?? 0);

        if (! $invoiceNumber) {
            return response()->json(['message' => 'Missing invoice number.'], 400);
        }

        /** @var Order|null $order */
        $order = Order::with('payment')->where('order_code', $invoiceNumber)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        /** @var Payment|null $payment */
        $payment = $order->payment;

        if (! $payment) {
            return response()->json(['message' => 'Payment record not found.'], 404);
        }

        // FR-PAY-07: Historical midtrans orders cannot be notified via doku webhook
        if ($payment->isHistorical()) {
            return response()->json(['message' => 'Historical payment archive cannot be updated.'], 422);
        }

        // 2. Validate Amount
        if (abs(((float) $payment->amount) - $amount) > 0.01) {
            Log::warning('DOKU Webhook rejected: Amount mismatch', [
                'order_code' => $order->order_code,
                'expected' => $payment->amount,
                'received' => $amount,
            ]);

            return response()->json(['message' => 'Amount mismatch.'], 422);
        }

        // 3. Request-Id Idempotency Check
        if ($payment->last_request_id === $requestId) {
            return response()->json(['message' => 'Duplicate notification processed idempotently.']);
        }

        // Record last processed request_id
        $payment->update([
            'last_request_id' => $requestId,
        ]);

        // 4. Apply Status Changes
        $this->syncService->applyGatewayStatus($order, $payload);

        return response()->json(['message' => 'Notification processed successfully.']);
    }
}
