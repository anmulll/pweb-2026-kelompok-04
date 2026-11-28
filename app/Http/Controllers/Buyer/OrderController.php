<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
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

        $orders = Order::where('user_id', $request->user()->id)
            ->when(! empty($status) && $status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($orders);
        }

        if (view()->exists('buyer.orders.index')) {
            return view('buyer.orders.index', compact('orders'));
        }

        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $order->loadMissing(['items.product', 'payment', 'user']);

        if ($request->wantsJson()) {
            return response()->json($order);
        }

        if (view()->exists('buyer.orders.show')) {
            return view('buyer.orders.show', compact('order'));
        }

        return response()->json($order);
    }

    public function cancel(Request $request, Order $order)
    {
        $this->authorize('cancel', $order);

        $this->orderStatusService->cancelOrder($order, 'Dibatalkan oleh Pembeli');

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function confirmReceipt(Request $request, Order $order)
    {
        $this->authorize('complete', $order);

        $this->orderStatusService->markAsCompleted($order);

        return redirect()->route('buyer.orders.show', $order)->with('success', 'Pesanan telah selesai. Terima kasih!');
    }
}
