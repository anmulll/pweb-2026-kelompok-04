<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\ShipOrderRequest;
use App\Models\Order;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderStatusService $orderStatusService
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status');

        $orders = Order::with(['items.product', 'payment', 'user'])
            ->when(! empty($status) && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        if (view()->exists('seller.orders.index')) {
            return view('seller.orders.index', compact('orders'));
        }

        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        $order->loadMissing(['items.product', 'payment', 'user']);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        if (view()->exists('seller.orders.show')) {
            return view('seller.orders.show', compact('order'));
        }

        return response()->json($order);
    }

    public function process(Request $request, Order $order)
    {
        $this->orderStatusService->markAsProcessing($order);

        return redirect()->route('seller.orders.show', $order)->with('success', 'Pesanan berhasil ditandai Diproses.');
    }

    public function ship(ShipOrderRequest $request, Order $order)
    {
        $validated = $request->validated();

        $this->orderStatusService->markAsShipped($order, $validated['courier_name'], $validated['tracking_number']);

        return redirect()->route('seller.orders.show', $order)->with('success', 'Pesanan berhasil ditandai Dikirim.');
    }

    public function cancel(Request $request, Order $order)
    {
        $this->orderStatusService->cancelOrder($order, 'Dibatalkan oleh Penjual');

        return redirect()->route('seller.orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
