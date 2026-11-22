<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
        $lowStockProducts = $this->reportService->getLowStockProducts(1);
        $recentOrders = Order::with(['items', 'payment', 'user'])->latest()->take(5)->get();

        if ($request->wantsJson()) {
            return response()->json([
                'total_revenue' => $totalRevenue,
                'low_stock_count' => $lowStockProducts->count(),
                'recent_orders' => $recentOrders,
            ]);
        }

        if (view()->exists('seller.dashboard')) {
            return view('seller.dashboard', compact('totalRevenue', 'lowStockProducts', 'recentOrders'));
        }

        return response()->json([
            'total_revenue' => $totalRevenue,
            'low_stock_count' => $lowStockProducts->count(),
            'recent_orders' => $recentOrders,
        ]);
    }
}
