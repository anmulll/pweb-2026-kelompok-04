<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Services\PaymentGatewayResolver;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public const FIXED_SHIPPING_COST = 0.00;

    public function __construct(
        protected CartService $cartService,
        protected PaymentGatewayResolver $gatewayResolver
    ) {}

    /**
     * Perform Two-Phase Checkout:
     * Phase 1: In a single DB transaction:
     *   - Lock cart and product rows (lockForUpdate)
     *   - Re-validate SD-01, stock, and price (expected_total)
     *   - Create order, snapshot order_items, create payment (provider = doku_sandbox, status = pending)
     *   - Atomically decrement product stock
     *   - Clear cart items
     * Phase 2: Outside DB transaction:
     *   - Call DOKU Checkout Sandbox to get payment URL & token
     *   - Update payment record with checkout url, token, expired_at
     *   - Gateway failure keeps Phase 1 order and stock intact for safe retry
     *
     * @param array{recipient_name: string, recipient_phone: string, shipping_address: string, expected_total?: float|null} $data
     * @throws ValidationException
     */
    public function checkout(User $buyer, array $data): Order
    {
        $order = DB::transaction(function () use ($buyer, $data) {
            $cart = Cart::where('user_id', $buyer->id)->lockForUpdate()->first();

            if (! $cart) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja tidak ditemukan.',
                ]);
            }

            $cartItems = CartItem::where('cart_id', $cart->id)->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Keranjang belanja Anda masih kosong.',
                ]);
            }

            // Lock products for update and validate
            $productIds = $cartItems->pluck('product_id')->all();
            $products = Product::with('category')
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0.0;
            $itemsToCreate = [];

            foreach ($cartItems as $item) {
                /** @var Product|null $product */
                $product = $products->get($item->product_id);

                if (! $product) {
                    throw ValidationException::withMessages([
                        'cart' => 'Salah satu produk di keranjang tidak ditemukan.',
                    ]);
                }

                // SD-01 rule: products.is_active = 1 AND categories.is_active = 1
                if (! $product->is_active || ! ($product->category?->is_active ?? false)) {
                    throw ValidationException::withMessages([
                        'cart' => "Produk '{$product->name}' sedang tidak aktif atau kategori dinonaktifkan.",
                    ]);
                }

                // Second hand unit check: qty <= 1
                if ($product->isUsed() && $item->quantity > 1) {
                    throw ValidationException::withMessages([
                        'cart' => "Produk unit second '{$product->name}' hanya dapat dibeli 1 unit.",
                    ]);
                }

                // Stock check
                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Stok produk '{$product->name}' tidak mencukupi (tersedia: {$product->stock}).",
                    ]);
                }

                $lineTotal = ((float) $product->price) * $item->quantity;
                $subtotal += $lineTotal;

                $itemsToCreate[] = [
                    'product' => $product,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $product->price,
                    'line_total' => $lineTotal,
                ];
            }

            $shippingCost = self::FIXED_SHIPPING_COST;
            $totalAmount = $subtotal + $shippingCost;

            // Re-validate expected_total if supplied
            if (isset($data['expected_total']) && $data['expected_total'] !== null) {
                $expected = (float) $data['expected_total'];
                if (abs($expected - $totalAmount) > 0.01) {
                    throw ValidationException::withMessages([
                        'expected_total' => 'Terjadi perubahan harga produk atau total pembayaran. Silakan periksa kembali keranjang Anda.',
                    ]);
                }
            }

            // Create Order
            $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'user_id' => $buyer->id,
                'order_code' => $orderCode,
                'status' => OrderStatus::PENDING_PAYMENT,
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'shipping_address' => $data['shipping_address'],
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
            ]);

            // Create OrderItems Snapshot and decrement stock
            foreach ($itemsToCreate as $itemData) {
                /** @var Product $product */
                $product = $itemData['product'];
                $qty = $itemData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'sku_snapshot' => $product->sku,
                    'condition_snapshot' => is_object($product->condition) ? $product->condition->value : $product->condition,
                    'condition_grade_snapshot' => is_object($product->condition_grade) ? $product->condition_grade?->value : $product->condition_grade,
                    'battery_health_snapshot' => $product->battery_health,
                    'functional_status_snapshot' => $product->functional_status,
                    'defect_notes_snapshot' => $product->defect_notes,
                    'included_accessories_snapshot' => $product->included_accessories,
                    'warranty_info_snapshot' => $product->warranty_info,
                    'unit_price_snapshot' => $itemData['unit_price'],
                    'quantity' => $qty,
                    'line_total' => $itemData['line_total'],
                ]);

                // Atomically decrement stock
                $product->decrement('stock', $qty);
            }

            // Create initial Payment row
            Payment::create([
                'order_id' => $order->id,
                'gateway_order_id' => $order->order_code,
                'provider' => 'doku_sandbox',
                'status' => PaymentStatus::PENDING,
                'amount' => $totalAmount,
            ]);

            // Clear Cart Items
            $cart->items()->delete();

            return $order;
        });

        // Phase 2: Outside DB transaction, initiate Payment Gateway call
        try {
            $gateway = $this->gatewayResolver->resolve('doku_sandbox');
            $gatewayResult = $gateway->createPayment($order);

            if (! empty($gatewayResult['gateway_checkout_url'])) {
                $order->payment()->update([
                    'gateway_checkout_url' => $gatewayResult['gateway_checkout_url'],
                    'gateway_token' => $gatewayResult['gateway_token'] ?? null,
                    'expired_at' => $gatewayResult['expired_at'] ?? now()->addMinutes(60),
                    'raw_response' => $gatewayResult['raw_response'] ?? null,
                ]);
            }
        } catch (Exception $e) {
            // Gateway failure does NOT roll back Phase 1 order or stock;
            // it retains the pending order and payment so the buyer can safely retry.
        }

        return $order->fresh(['items', 'payment']);
    }
}
