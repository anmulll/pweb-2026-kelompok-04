<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentStatusSyncService
{
    public function __construct(
        protected PaymentGatewayResolver $gatewayResolver,
        protected OrderStatusService $orderStatusService
    ) {}

    /**
     * Synchronize payment status with gateway for an order.
     * Uses cache throttling (15s per order per ADR-04) unless $force is true.
     *
     * @return array{order_status: string, payment_status: string, synced: bool}
     */
    public function syncStatus(Order $order, bool $force = false): array
    {
        $order->loadMissing('payment');
        /** @var Payment|null $payment */
        $payment = $order->payment;

        if (! $payment) {
            return [
                'order_status' => $order->status->value,
                'payment_status' => 'none',
                'synced' => false,
            ];
        }

        // FR-PAY-07 & BB-PAY-04: Historical midtrans records are read-only
        if ($payment->isHistorical()) {
            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
                'synced' => false,
            ];
        }

        // If order is already terminal or paid, return directly
        if ($payment->isPaid() || $order->isCompleted()) {
            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
                'synced' => false,
            ];
        }

        $throttleKey = "doku_auto_sync_{$order->id}";

        if (! $force && ! Cache::add($throttleKey, true, 15)) {
            // Throttled: return current status without calling remote gateway
            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
                'synced' => false,
            ];
        }

        try {
            $gateway = $this->gatewayResolver->resolve($payment);
            $inquiryResult = $gateway->checkStatus($order);

            $this->applyGatewayStatus($order, $inquiryResult);

            $order->refresh();
            $payment->refresh();

            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
                'synced' => true,
            ];
        } catch (\Exception $e) {
            Log::warning("Payment status sync failed for order {$order->order_code}: " . $e->getMessage());

            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
                'synced' => false,
            ];
        }
    }

    /**
     * Map gateway status to internal domain status and apply within a DB transaction.
     */
    public function applyGatewayStatus(Order $order, array $gatewayData): void
    {
        $order->loadMissing('payment');
        /** @var Payment|null $payment */
        $payment = $order->payment;

        if (! $payment || $payment->isHistorical()) {
            return;
        }

        // DOKU transaction status extraction
        $trxStatus = $gatewayData['transaction']['status']
            ?? $gatewayData['order']['status']
            ?? $gatewayData['status']
            ?? null;

        if (! $trxStatus) {
            return;
        }

        $trxStatusUpper = strtoupper((string) $trxStatus);

        DB::transaction(function () use ($order, $payment, $trxStatusUpper, $gatewayData) {
            if ($trxStatusUpper === 'SUCCESS') {
                if ($payment->status !== PaymentStatus::PAID) {
                    $payment->update([
                        'status' => PaymentStatus::PAID,
                        'paid_at' => now(),
                        'raw_response' => $gatewayData,
                    ]);

                    $order->update([
                        'status' => OrderStatus::PAID,
                    ]);
                }
            } elseif (in_array($trxStatusUpper, ['EXPIRED', 'EXPIRE'], true)) {
                if (! $payment->isTerminal()) {
                    $payment->update([
                        'status' => PaymentStatus::EXPIRED,
                        'raw_response' => $gatewayData,
                    ]);

                    $order->update([
                        'status' => OrderStatus::CANCELLED,
                    ]);

                    $this->orderStatusService->restoreStock($order);
                }
            } elseif (in_array($trxStatusUpper, ['CANCELLED', 'CANCELED'], true)) {
                if (! $payment->isTerminal()) {
                    $payment->update([
                        'status' => PaymentStatus::CANCELLED,
                        'raw_response' => $gatewayData,
                    ]);

                    $order->update([
                        'status' => OrderStatus::CANCELLED,
                    ]);

                    $this->orderStatusService->restoreStock($order);
                }
            }
            // Note: FAILED notifications/inquiries are ignored without state change per spec:
            // "notification FAILED diabaikan sesuai aturan reference; status inquiry dan expiry memiliki aturan tersendiri"
        });
    }
}
