<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    /**
     * Mark order as processing (Seller action).
     * Only allowed from 'paid'.
     *
     * @throws ValidationException
     */
    public function markAsProcessing(Order $order): Order
    {
        if ($order->status !== OrderStatus::PAID) {
            throw ValidationException::withMessages([
                'order' => 'Hanya pesanan yang sudah dibayar (paid) yang dapat diproses.',
            ]);
        }

        $order->update([
            'status' => OrderStatus::PROCESSING,
        ]);

        return $order->fresh();
    }

    /**
     * Ship order with courier & tracking number (Seller action).
     * Only allowed from 'processing'.
     *
     * @throws ValidationException
     */
    public function shipOrder(Order $order, string $courierName, string $trackingNumber): Order
    {
        if ($order->status !== OrderStatus::PROCESSING) {
            throw ValidationException::withMessages([
                'order' => 'Hanya pesanan yang sedang diproses yang dapat dikirim.',
            ]);
        }

        if (trim($courierName) === '' || trim($trackingNumber) === '') {
            throw ValidationException::withMessages([
                'shipping' => 'Nama kurir dan nomor resi wajib diisi saat pengiriman.',
            ]);
        }

        $order->update([
            'status' => OrderStatus::SHIPPED,
            'courier_name' => $courierName,
            'tracking_number' => $trackingNumber,
        ]);

        return $order->fresh();
    }

    /**
     * Confirm receipt of order by buyer (Buyer action).
     * Only allowed from 'shipped' by the owning buyer.
     *
     * @throws ValidationException
     */
    public function confirmReceipt(Order $order, User $buyer): Order
    {
        if ($order->user_id !== $buyer->id) {
            throw ValidationException::withMessages([
                'order' => 'Hanya pembeli pemilik pesanan yang dapat mengonfirmasi penerimaan.',
            ]);
        }

        if ($order->status !== OrderStatus::SHIPPED) {
            throw ValidationException::withMessages([
                'order' => 'Hanya pesanan berstatus dikirim yang dapat dikonfirmasi selesai.',
            ]);
        }

        $order->update([
            'status' => OrderStatus::COMPLETED,
        ]);

        return $order->fresh();
    }

    /**
     * Cancel order and perform idempotent restock.
     *
     * @throws ValidationException
     */
    public function cancelOrder(Order $order, string $reason = 'cancelled_by_user'): Order
    {
        if (! $order->canBeCancelled()) {
            throw ValidationException::withMessages([
                'order' => 'Pesanan dengan status ini tidak dapat dibatalkan.',
            ]);
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => OrderStatus::CANCELLED,
            ]);

            if ($order->payment && ! $order->payment->isTerminal()) {
                $order->payment->update([
                    'status' => PaymentStatus::CANCELLED,
                ]);
            }

            // Restore product stock idempotently
            $this->restoreStock($order);
        });

        return $order->fresh();
    }

    /**
     * Idempotently restore stock for an order using stock_restored_at guard (ADR-03 / BR-04).
     */
    public function restoreStock(Order $order): bool
    {
        if ($order->stock_restored_at !== null) {
            // Already restored, prevent duplicate restock
            return false;
        }

        $order->loadMissing('items.product');

        foreach ($order->items as $item) {
            if ($item->product_id) {
                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
            }
        }

        $order->update([
            'stock_restored_at' => now(),
        ]);

        return true;
    }
}
