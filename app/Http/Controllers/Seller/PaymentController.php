<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentStatusSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentStatusSyncService $syncService
    ) {}

    /**
     * Read-only payments record index (FR-SEL-03).
     */
    public function index(Request $request): View|JsonResponse
    {
        $payments = Payment::with(['order.user'])
            ->latest()
            ->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($payments);
        }

        return view('seller.payments.index', compact('payments'));
    }

    /**
     * Polling endpoint for seller order view.
     */
    public function status(Request $request, Order $order): JsonResponse
    {
        Gate::authorize('view', $order);

        $result = $this->syncService->syncStatus($order);

        return response()->json([
            'order_code' => $order->order_code,
            'order_status' => $result['order_status'],
            'payment_status' => $result['payment_status'],
            'synced' => $result['synced'],
        ]);
    }

    /**
     * Trigger status sync from seller side.
     */
    public function sync(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $order);

        $payment = $order->payment;
        if ($payment && $payment->isHistorical()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Transaksi historis bersifat read-only.'], 422);
            }
            return redirect()->route('seller.orders.show', $order->id)
                ->with('error', 'Transaksi historis bersifat read-only.');
        }

        $result = $this->syncService->syncStatus($order, force: true);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('seller.orders.show', $order->id)
            ->with('success', 'Status pembayaran berhasil disinkronkan.');
    }
}
