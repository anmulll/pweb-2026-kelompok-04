<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\CheckoutRequest;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected CheckoutService $checkoutService
    ) {}

    public function index(Request $request): View|RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $cart = $this->cartService->getCartWithItems($user);

        if ($cart->items->isEmpty()) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang Anda masih kosong.',
                ]);
            }
            return redirect()->route('buyer.cart.index')->with('warning', 'Keranjang Anda masih kosong.');
        }

        if (! $this->cartService->canCheckout($cart)) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang Anda memiliki produk yang tidak tersedia atau melebihi stok.',
                ]);
            }
            return redirect()->route('buyer.cart.index')
                ->with('error', 'Keranjang Anda memiliki produk yang tidak tersedia atau melebihi stok.');
        }

        $subtotal = $this->cartService->getSubtotal($cart);
        $shippingCost = CheckoutService::FIXED_SHIPPING_COST;
        $totalAmount = $subtotal + $shippingCost;

        if ($request->wantsJson()) {
            return response()->json([
                'cart' => $cart,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
            ]);
        }

        return view('buyer.checkout.index', compact('cart', 'subtotal', 'shippingCost', 'totalAmount', 'user'));
    }

    public function store(CheckoutRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $cart = $this->cartService->getCartWithItems($user);

        if (! $this->cartService->canCheckout($cart)) {
            if ($request->wantsJson()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang Anda memiliki produk yang tidak tersedia atau melebihi stok.',
                ]);
            }

            return redirect()->route('buyer.cart.index')
                ->with('error', 'Keranjang Anda memiliki produk yang tidak tersedia atau melebihi stok.');
        }

        $order = $this->checkoutService->checkout($user, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pesanan berhasil dibuat.',
                'order' => $order,
                'checkout_url' => $order->payment?->gateway_checkout_url,
            ], 201);
        }

        if ($order->payment?->gateway_checkout_url) {
            return redirect()->away($order->payment->gateway_checkout_url);
        }

        return redirect()->route('buyer.orders.show', $order->id)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lanjutkan pembayaran.');
    }
}
