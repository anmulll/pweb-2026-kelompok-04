<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentStatusSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentStatusSyncService $syncService
    ) {}

    public function index(Request $request)
    {
        $payments = Payment::with('order.user')->latest()->paginate(15);

        if ($request->wantsJson()) {
            return response()->json($payments);
        }

        if (view()->exists('seller.payments.index')) {
            return view('seller.payments.index', compact('payments'));
        }

        return response()->json($payments);
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        $result = $this->syncService->syncPaymentStatus($order);

        return response()->json([
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'order_status' => $result['order_status'],
            'payment_status' => $result['payment_status'],
        ]);
    }

    public function sync(Request $request, Order $order): JsonResponse
    {
        $payment = $order->payment;

        if ($payment && $payment->provider === 'midtrans_sandbox') {
            return response()->json(['message' => 'Transaksi arsip historis tidak dapat disinkronkan.'], 422);
        }

        $result = $this->syncService->syncPaymentStatus($order);

        return response()->json($result);
    }
}
