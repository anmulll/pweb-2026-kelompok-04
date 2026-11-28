<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    /**
     * Mark order as processing (Seller).
     */
    public function markAsProcessing(Order $order): Order
    {
        if ($order->status !== OrderStatus::PAID) {
            throw new \DomainException('Pesanan hanya dapat diproses jika sudah berstatus Dibayar.');
        }

        $order->update(['status' => OrderStatus::PROCESSING]);

        return $order->fresh();
    }

    /**
     * Mark order as shipped with courier and tracking number (Seller).
     */
    public function markAsShipped(Order $order, string $courierName, string $trackingNumber): Order
    {
        if ($order->status !== OrderStatus::PROCESSING) {
            throw new \DomainException('Pesanan hanya dapat dikirim jika sudah berstatus Diproses.');
        }

        if (trim($courierName) === '' || trim($trackingNumber) === '') {
            throw new \InvalidArgumentException('Nama kurir dan nomor resi wajib diisi.');
        }

        $order->update([
            'status' => OrderStatus::SHIPPED,
            'courier_name' => $courierName,
            'tracking_number' => $trackingNumber,
        ]);

        return $order->fresh();
    }

    /**
     * Mark order as completed (Buyer only).
     */
    public function markAsCompleted(Order $order): Order
    {
        if ($order->status !== OrderStatus::SHIPPED) {
            throw new \DomainException('Pesanan hanya dapat diselesaikan jika sudah berstatus Dikirim.');
        }

        $order->update(['status' => OrderStatus::COMPLETED]);

        return $order->fresh();
    }

    /**
     * Cancel order and restore stock idempotently via stock_restored_at guard (BR-04, ADR-03).
     */
    public function cancelOrder(Order $order, string $reason = ''): Order
    {
        if ($order->status === OrderStatus::SHIPPED || $order->status === OrderStatus::COMPLETED) {
            throw new \DomainException('Pesanan yang sudah dikirim atau selesai tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::CANCELLED]);

            // Idempotent Restock Guard
            if ($order->stock_restored_at === null) {
                $order->loadMissing('items.product');

                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }

                $order->update(['stock_restored_at' => now()]);
            }
        });

        return $order->fresh();
    }
}
