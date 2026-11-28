<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Services\Doku\DokuCheckoutGateway;
use Illuminate\Support\Facades\Cache;

class PaymentStatusSyncService
{
    public function __construct(
        protected DokuCheckoutGateway $dokuGateway,
        protected OrderStatusService $orderStatusService
    ) {}

    /**
     * Sync payment status with DOKU Checkout Sandbox (throttled 15s per order).
     */
    public function syncPaymentStatus(Order $order): array
    {
        $order->loadMissing('payment');
        $payment = $order->payment;

        if (! $payment) {
            return ['status' => 'not_found', 'order_status' => $order->status->value];
        }

        // FR-PAY-07 & ADR-01: midtrans_sandbox is read-only archive
        if ($payment->provider === 'midtrans_sandbox') {
            return [
                'error' => 'Transaksi historis Midtrans bersifat Read-Only dan tidak dapat disinkronisasi.',
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
            ];
        }

        // If already paid or terminal, no need to call gateway
        if ($payment->status === PaymentStatus::PAID) {
            return [
                'order_status' => $order->status->value,
                'payment_status' => $payment->status->value,
            ];
        }

        // ADR-04: Throttled server sync: max 1 request every 15 seconds per order
        $lockAcquired = Cache::add("doku_auto_sync_{$order->id}", true, 15);

        if ($lockAcquired) {
            $inquiry = $this->dokuGateway->checkStatus($order);

            if (! empty($inquiry['transaction']['status']) && strtoupper($inquiry['transaction']['status']) === 'SUCCESS') {
                $payment->update([
                    'status' => PaymentStatus::PAID,
                    'paid_at' => now(),
                    'raw_response' => $inquiry,
                ]);

                if ($order->status === OrderStatus::PENDING_PAYMENT) {
                    $order->update(['status' => OrderStatus::PAID]);
                }
            } elseif (! empty($inquiry['transaction']['status']) && in_array(strtoupper($inquiry['transaction']['status']), ['EXPIRED', 'FAILED', 'CANCELLED'])) {
                $payment->update([
                    'status' => PaymentStatus::EXPIRED,
                    'raw_response' => $inquiry,
                ]);

                $this->orderStatusService->cancelOrder($order, 'Kadaluarsa dari gateway DOKU');
            }
        }

        return [
            'order_status' => $order->fresh()->status->value,
            'payment_status' => $payment->fresh()->status->value,
        ];
    }
}
