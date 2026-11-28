<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $orders = Order::with(['items.product', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        return view('buyer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View|JsonResponse
    {
        Gate::authorize('view', $order);

        $order->load(['items.product', 'payment', 'user']);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        return view('buyer.orders.show', compact('order'));
    }

    public function cancel(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('cancel', $order);

        $this->orderStatusService->cancelOrder($order, 'cancelled_by_buyer');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan.',
                'order' => $order->fresh(),
            ]);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function confirmReceipt(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('complete', $order);

        $this->orderStatusService->confirmReceipt($order, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan telah selesai. Terima kasih atas konfirmasi Anda.',
                'order' => $order->fresh(),
            ]);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Pesanan telah selesai. Terima kasih atas konfirmasi Anda.');
    }
}
