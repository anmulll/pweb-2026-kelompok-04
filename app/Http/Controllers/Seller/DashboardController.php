<?php

namespace App\Http\Controllers\Seller;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $totalRevenue = $this->reportService->getVerifiedRevenue();
        $verifiedRevenue = $totalRevenue;
        $totalOrders = Order::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = $this->reportService->getLowStockProducts(1);
        $recentOrders = Order::with(['items', 'payment', 'user'])->latest()->take(5)->get();

        // 7-day verified paid revenue trend for Chart.js
        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $chartLabels[] = $day->format('d M');
            $chartValues[] = (float) Payment::where('status', PaymentStatus::PAID)
                ->whereDate('paid_at', $day->toDateString())
                ->sum('amount');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'total_revenue' => $totalRevenue,
                'verified_revenue' => $verifiedRevenue,
                'total_orders' => $totalOrders,
                'active_products' => $activeProducts,
                'low_stock_count' => $lowStockProducts->count(),
                'recent_orders' => $recentOrders,
                'chart_labels' => $chartLabels,
                'chart_values' => $chartValues,
            ]);
        }

        if (view()->exists('seller.dashboard')) {
            return view('seller.dashboard', compact(
                'totalRevenue',
                'verifiedRevenue',
                'totalOrders',
                'activeProducts',
                'lowStockProducts',
                'recentOrders',
                'chartLabels',
                'chartValues'
            ));
        }

        return response()->json([
            'total_revenue' => $totalRevenue,
            'low_stock_count' => $lowStockProducts->count(),
            'recent_orders' => $recentOrders,
        ]);
    }
}
