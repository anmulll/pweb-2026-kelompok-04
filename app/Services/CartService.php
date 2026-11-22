<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Get or create the buyer's persistent cart (lazy creation).
     *
     * @throws AuthorizationException
     */
    public function getCart(User $buyer): Cart
    {
        if (! $buyer->isBuyer()) {
            throw new AuthorizationException('Only buyers can access a shopping cart.');
        }

        return $buyer->cart()->firstOrCreate();
    }

    /**
     * Retrieve cart with loaded product & category relations.
     */
    public function getCartWithItems(User $buyer): Cart
    {
        $cart = $this->getCart($buyer);

        $cart->load(['items.product.category']);

        return $cart;
    }

    /**
     * Add product to cart. If already present, merge quantity.
     * Enforce SD-01 visibility, stock limit, and second-hand single-unit rule.
     *
     * @throws ValidationException|AuthorizationException
     */
    public function addItem(User $buyer, int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getCart($buyer);

        $product = Product::with('category')->find($productId);

        if (! $product) {
            throw ValidationException::withMessages([
                'product_id' => 'Produk tidak ditemukan.',
            ]);
        }

        // BR-01 (SD-01) rule check
        if (! $product->is_active || ! ($product->category?->is_active ?? false)) {
            throw ValidationException::withMessages([
                'product_id' => 'Produk ini sedang tidak tersedia.',
            ]);
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Kuantitas minimal adalah 1.',
            ]);
        }

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();

        $newQuantity = $existingItem ? ($existingItem->quantity + $quantity) : $quantity;

        // BR-02: Second-hand unit quantity must be at most 1
        if ($product->isUsed() && $newQuantity > 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk unit second hanya dapat dibeli 1 unit.',
            ]);
        }

        // Stock check
        if ($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Kuantitas melebihi stok yang tersedia ({$product->stock}).",
            ]);
        }

        if ($existingItem) {
            $existingItem->update(['quantity' => $newQuantity]);
            return $existingItem->fresh();
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => $newQuantity,
        ]);
    }

    /**
     * Update quantity of a cart item.
     *
     * @throws ValidationException|AuthorizationException
     */
    public function updateItem(User $buyer, int $cartItemId, int $quantity): CartItem
    {
        $cart = $this->getCart($buyer);

        $item = CartItem::with(['product.category'])
            ->where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->first();

        if (! $item) {
            throw ValidationException::withMessages([
                'cart_item' => 'Item keranjang tidak ditemukan.',
            ]);
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Kuantitas minimal adalah 1.',
            ]);
        }

        $product = $item->product;

        if (! $product || ! $product->is_active || ! ($product->category?->is_active ?? false)) {
            throw ValidationException::withMessages([
                'product' => 'Produk ini sedang tidak aktif atau tidak tersedia.',
            ]);
        }

        if ($product->isUsed() && $quantity > 1) {
            throw ValidationException::withMessages([
                'quantity' => 'Produk unit second hanya dapat dibeli 1 unit.',
            ]);
        }

        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => "Kuantitas melebihi stok yang tersedia ({$product->stock}).",
            ]);
        }

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    /**
     * Remove an item from the cart.
     *
     * @throws AuthorizationException
     */
    public function removeItem(User $buyer, int $cartItemId): bool
    {
        $cart = $this->getCart($buyer);

        $deleted = CartItem::where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->delete();

        return (bool) $deleted;
    }

    /**
     * Clear all items in the cart while keeping the cart record.
     *
     * @throws AuthorizationException
     */
    public function clearCart(User $buyer): void
    {
        $cart = $this->getCart($buyer);

        $cart->items()->delete();
    }

    /**
     * Calculate live subtotal of active/valid items.
     */
    public function getSubtotal(Cart $cart): float
    {
        $cart->loadMissing(['items.product.category']);

        $subtotal = 0.0;

        foreach ($cart->items as $item) {
            if ($item->product && ! $item->isStale()) {
                $subtotal += ((float) $item->product->price) * $item->quantity;
            }
        }

        return $subtotal;
    }

    /**
     * Detect stale items in the cart (non-active product/category, or insufficient stock).
     */
    public function getStaleItems(Cart $cart): Collection
    {
        $cart->loadMissing(['items.product.category']);

        return $cart->items->filter(fn (CartItem $item) => $item->isStale());
    }

    /**
     * Check if the cart is ready for checkout.
     * Must have at least 1 item and ZERO stale items.
     */
    public function canCheckout(Cart $cart): bool
    {
        $cart->loadMissing(['items.product.category']);

        if ($cart->items->isEmpty()) {
            return false;
        }

        return $this->getStaleItems($cart)->isEmpty();
    }
}
