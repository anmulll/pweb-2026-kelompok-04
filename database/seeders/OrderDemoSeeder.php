<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@istore.test')->first();

        if (! $buyer) {
            return;
        }

        $products = Product::all()->keyBy('sku');

        if ($products->isEmpty()) {
            return;
        }

        // Demo Order 1: Pending Payment (DOKU Sandbox)
        $this->seedOrder(
            orderCode: 'ORD-DEMO-PENDING',
            buyer: $buyer,
            status: OrderStatus::PENDING_PAYMENT,
            product: $products->first(),
            paymentProvider: 'doku_sandbox',
            paymentStatus: PaymentStatus::PENDING,
            courier: null,
            trackingNumber: null
        );

        // Demo Order 2: Paid & Processing (DOKU Sandbox)
        $this->seedOrder(
            orderCode: 'ORD-DEMO-PAID',
            buyer: $buyer,
            status: OrderStatus::PROCESSING,
            product: $products->first(),
            paymentProvider: 'doku_sandbox',
            paymentStatus: PaymentStatus::PAID,
            courier: null,
            trackingNumber: null
        );

        // Demo Order 3: Shipped (DOKU Sandbox)
        $this->seedOrder(
            orderCode: 'ORD-DEMO-SHIPPED',
            buyer: $buyer,
            status: OrderStatus::SHIPPED,
            product: $products->first(),
            paymentProvider: 'doku_sandbox',
            paymentStatus: PaymentStatus::PAID,
            courier: 'JNE Express',
            trackingNumber: 'JNE8899001122'
        );

        // Demo Order 4: Completed (DOKU Sandbox)
        $this->seedOrder(
            orderCode: 'ORD-DEMO-COMPLETED',
            buyer: $buyer,
            status: OrderStatus::COMPLETED,
            product: $products->first(),
            paymentProvider: 'doku_sandbox',
            paymentStatus: PaymentStatus::PAID,
            courier: 'SiCepat Reguler',
            trackingNumber: 'SCP9988776655'
        );

        // Demo Order 5: Historical Archive (Midtrans Sandbox - Read Only)
        $this->seedOrder(
            orderCode: 'ORD-HISTORICAL-MIDTRANS',
            buyer: $buyer,
            status: OrderStatus::COMPLETED,
            product: $products->first(),
            paymentProvider: 'midtrans_sandbox',
            paymentStatus: PaymentStatus::PAID,
            courier: 'JNE YES',
            trackingNumber: 'JNE7766554433'
        );
    }

    protected function seedOrder(
        string $orderCode,
        User $buyer,
        OrderStatus $status,
        Product $product,
        string $paymentProvider,
        PaymentStatus $paymentStatus,
        ?string $courier,
        ?string $trackingNumber
    ): void {
        // Idempotency: jika order_code sudah ada, jangan buat ulang & jangan kurangi stok lagi!
        if (Order::where('order_code', $orderCode)->exists()) {
            return;
        }

        $subtotal = $product->price;
        $shippingCost = 50000;
        $totalAmount = $subtotal + $shippingCost;

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_code' => $orderCode,
            'status' => $status,
            'recipient_name' => $buyer->name,
            'recipient_phone' => $buyer->phone ?? '089876543210',
            'shipping_address' => $buyer->default_shipping_address ?? 'Jl. Perintis Kemerdekaan KM 10, Makassar',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_amount' => $totalAmount,
            'courier_name' => $courier,
            'tracking_number' => $trackingNumber,
            'stock_restored_at' => null,
            'created_at' => now()->subDays(rand(1, 15)),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'sku_snapshot' => $product->sku,
            'condition_snapshot' => $product->condition,
            'condition_grade_snapshot' => $product->condition_grade,
            'battery_health_snapshot' => $product->battery_health,
            'functional_status_snapshot' => $product->functional_status,
            'defect_notes_snapshot' => $product->defect_notes,
            'included_accessories_snapshot' => $product->included_accessories,
            'warranty_info_snapshot' => $product->warranty_info,
            'unit_price_snapshot' => $product->price,
            'quantity' => 1,
            'line_total' => $subtotal,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway_order_id' => ($paymentProvider === 'midtrans_sandbox' ? 'MIDTRANS-' : 'DOKU-') . $orderCode,
            'provider' => $paymentProvider,
            'status' => $paymentStatus,
            'amount' => $totalAmount,
            'gateway_checkout_url' => 'https://sandbox.doku.com/checkout/' . $orderCode,
            'gateway_token' => 'tok_' . md5($orderCode),
            'last_request_id' => 'req_' . md5($orderCode),
            'expired_at' => now()->addHours(2),
            'paid_at' => $paymentStatus === PaymentStatus::PAID ? now()->subDays(1) : null,
            'raw_response' => ['seeded' => true, 'provider' => $paymentProvider],
        ]);
    }
}
