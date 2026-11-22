<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Buyer\AddToCartRequest;
use App\Http\Requests\Buyer\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();
        $cart = $this->cartService->getCartWithItems($user);
        $subtotal = $this->cartService->getSubtotal($cart);
        $staleItems = $this->cartService->getStaleItems($cart);
        $canCheckout = $this->cartService->canCheckout($cart);

        if ($request->wantsJson()) {
            return response()->json([
                'cart' => $cart,
                'subtotal' => $subtotal,
                'stale_count' => $staleItems->count(),
                'can_checkout' => $canCheckout,
            ]);
        }

        return view('buyer.cart.index', compact('cart', 'subtotal', 'staleItems', 'canCheckout'));
    }

    public function store(AddToCartRequest $request): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        $productId = (int) $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $cartItem = $this->cartService->addItem($user, $productId, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'item' => $cartItem,
            ], 201);
        }

        return redirect()->route('buyer.cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        Gate::authorize('update', $cartItem->cart);

        $quantity = (int) $request->input('quantity');
        $updatedItem = $this->cartService->updateItem($user, $cartItem->id, $quantity);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kuantitas berhasil diperbarui.',
                'item' => $updatedItem,
            ]);
        }

        return redirect()->route('buyer.cart.index')->with('success', 'Kuantitas keranjang berhasil diperbarui.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        Gate::authorize('delete', $cartItem->cart);

        $this->cartService->removeItem($user, $cartItem->id);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Item berhasil dihapus dari keranjang.',
            ]);
        }

        return redirect()->route('buyer.cart.index')->with('success', 'Item berhasil dihapus dari keranjang.');
    }
}
