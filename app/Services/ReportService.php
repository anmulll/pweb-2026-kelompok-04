<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    /**
     * Get verified paid revenue across DOKU and historical archive without double-counting.
     */
    public function getVerifiedRevenue(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = Payment::where('status', PaymentStatus::PAID);

        if ($startDate) {
            $query->whereDate('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('paid_at', '<=', $endDate);
        }

        return (float) $query->sum('amount');
    }

    /**
     * Get products with low stock (<= 1).
     */
    public function getLowStockProducts(int $threshold = 1): Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get sales report data filtered by date range.
     */
    public function getSalesReport(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Order::with(['items', 'payment', 'user'])->latest();

        if (! empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if (! empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $orders = $query->get();
        $paidOrders = $orders->filter(fn ($order) => $order->payment?->status === PaymentStatus::PAID);
        $totalRevenue = (float) $paidOrders->sum('total_amount');
        $totalOrders = $orders->count();
        $paidOrdersCount = $paidOrders->count();

        return [
            'orders' => $orders,
            'total_revenue' => $totalRevenue,
            'verifiedRevenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'paidOrdersCount' => $paidOrdersCount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }
}
