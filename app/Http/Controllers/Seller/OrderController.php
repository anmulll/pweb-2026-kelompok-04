<?php

namespace App\Http\Controllers\Seller;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ShipOrderRequest;
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
        $status = $request->query('status');

        $query = Order::with(['user', 'items.product', 'payment'])->latest();

        if ($status && in_array($status, array_column(OrderStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        return view('seller.orders.index', compact('orders', 'status'));
    }

    public function show(Request $request, Order $order): View|JsonResponse
    {
        Gate::authorize('view', $order);

        $order->load(['user', 'items.product', 'payment']);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        return view('seller.orders.show', compact('order'));
    }

    public function process(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $order);

        $this->orderStatusService->markAsProcessing($order);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan ditandai sedang diproses.',
                'order' => $order->fresh(),
            ]);
        }

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil ditandai sedang diproses.');
    }

    public function ship(ShipOrderRequest $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $order);

        $this->orderStatusService->shipOrder(
            $order,
            $request->validated('courier_name'),
            $request->validated('tracking_number')
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dikirim.',
                'order' => $order->fresh(),
            ]);
        }

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dikirim dengan nomor resi.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('cancel', $order);

        $this->orderStatusService->cancelOrder($order, 'cancelled_by_seller');

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan dan stok dikembalikan.',
                'order' => $order->fresh(),
            ]);
        }

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
    }
}
