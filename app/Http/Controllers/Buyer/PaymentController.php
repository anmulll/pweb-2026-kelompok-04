<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentGatewayResolver;
use App\Services\PaymentStatusSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentGatewayResolver $gatewayResolver,
        protected PaymentStatusSyncService $syncService
    ) {}

    /**
     * Polling endpoint for payment & order status (FR-PAY-04 & ADR-04).
     * Rate limited by throttle:30,1 on the route.
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
     * Manual sync trigger by user.
     */
    public function sync(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $order);

        $payment = $order->payment;
        if ($payment && $payment->isHistorical()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Transaksi historis bersifat read-only.'], 422);
            }
            return redirect()->route('buyer.orders.show', $order->id)
                ->with('error', 'Transaksi historis bersifat read-only.');
        }

        $result = $this->syncService->syncStatus($order, force: true);

        if ($request->wantsJson()) {
            return response()->json($result);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    /**
     * Retry payment if session expired or failed.
     */
    public function retry(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        Gate::authorize('view', $order);

        $payment = $order->payment;
        if ($payment && $payment->isHistorical()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Transaksi historis bersifat read-only.'], 422);
            }
            return redirect()->route('buyer.orders.show', $order->id)
                ->with('error', 'Transaksi historis tidak dapat dibayar ulang.');
        }

        if ($payment?->gateway_checkout_url) {
            return redirect()->away($payment->gateway_checkout_url);
        }

        $gateway = $this->gatewayResolver->resolve('doku_sandbox');
        $gatewayResult = $gateway->createPayment($order);

        if (! empty($gatewayResult['gateway_checkout_url'])) {
            $payment->update([
                'gateway_checkout_url' => $gatewayResult['gateway_checkout_url'],
                'gateway_token' => $gatewayResult['gateway_token'] ?? null,
                'expired_at' => $gatewayResult['expired_at'] ?? now()->addMinutes(60),
                'raw_response' => $gatewayResult['raw_response'] ?? null,
            ]);

            return redirect()->away($gatewayResult['gateway_checkout_url']);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('error', 'Gagal membuat sesi pembayaran baru. Silakan coba lagi.');
    }
}
