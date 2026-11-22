<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentStatusSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentStatusSyncService $syncService
    ) {}

    /**
     * FR-PAY-04: Polling status endpoint with rate-limit and OrderPolicy.
     */
    public function status(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $result = $this->syncService->syncPaymentStatus($order);

        return response()->json([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'order_status' => $result['order_status'],
            'payment_status' => $result['payment_status'],
        ]);
    }

    public function pay(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $payment = $order->payment;

        if ($payment && $payment->provider === 'midtrans_sandbox') {
            abort(422, 'Transaksi arsip historis tidak dapat dibayar ulang.');
        }

        if ($payment && $payment->gateway_checkout_url) {
            return redirect()->away($payment->gateway_checkout_url);
        }

        return redirect()->route('buyer.orders.show', $order);
    }

    public function sync(Request $request, Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $payment = $order->payment;

        if ($payment && $payment->provider === 'midtrans_sandbox') {
            return response()->json(['message' => 'Transaksi arsip historis tidak dapat disinkronkan.'], 422);
        }

        $result = $this->syncService->syncPaymentStatus($order);

        return response()->json($result);
    }
}
