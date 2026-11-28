<?php

namespace Tests\Feature\Integration;

use App\Enums\ConditionGrade;
use App\Enums\FunctionalStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProductCondition;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $seller;
    private User $buyer;
    private Category $category;
    private Product $usedProduct;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->seller()->create();
        $this->buyer = User::factory()->buyer()->create();
        $this->category = Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'is_active' => true,
        ]);

        $this->usedProduct = Product::create([
            'category_id' => $this->category->id,
            'name' => 'iPhone 14 Pro 128GB Deep Purple',
            'model' => 'iPhone 14 Pro',
            'sku' => 'IP14P-128-DP-01',
            'storage' => '128GB',
            'color' => 'Deep Purple',
            'condition' => ProductCondition::USED,
            'condition_grade' => ConditionGrade::A,
            'battery_health' => 91,
            'functional_status' => FunctionalStatus::NORMAL->value,
            'included_accessories' => 'Fullset Box',
            'warranty_info' => 'Garansi Toko 1 Bulan',
            'price' => 12850000,
            'stock' => 1,
            'is_active' => true,
        ]);
    }

    public function test_seller_orders_payments_reports_and_dashboard_render_real_data_without_enum_errors(): void
    {
        $order = Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'IST-2026-0001',
            'status' => OrderStatus::PAID,
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 1, Makassar',
            'subtotal' => 12850000,
            'shipping_cost' => 25000,
            'total_amount' => 12875000,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->usedProduct->id,
            'product_name_snapshot' => $this->usedProduct->name,
            'sku_snapshot' => $this->usedProduct->sku,
            'condition_snapshot' => ProductCondition::USED->value,
            'condition_grade_snapshot' => ConditionGrade::A->value,
            'battery_health_snapshot' => 91,
            'functional_status_snapshot' => FunctionalStatus::NORMAL->value,
            'unit_price_snapshot' => 12850000,
            'quantity' => 1,
            'line_total' => 12850000,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'gateway_order_id' => 'DOKU-IST-2026-0001',
            'provider' => 'doku_sandbox',
            'amount' => 12875000,
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
        ]);

        $this->actingAs($this->seller);

        $this->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('12.875.000');

        $this->get(route('seller.orders.index'))
            ->assertOk()
            ->assertSee('IST-2026-0001')
            ->assertSee('Budi Santoso');

        $this->get(route('seller.orders.show', $order))
            ->assertOk()
            ->assertSee('IST-2026-0001')
            ->assertSee('iPhone 14 Pro 128GB Deep Purple')
            ->assertSee('name="courier_name"', false);

        $this->get(route('seller.payments.index'))
            ->assertOk()
            ->assertSee('DOKU-IST-2026-0001')
            ->assertSee('12.875.000');

        $this->get(route('seller.reports.index'))
            ->assertOk()
            ->assertSee('12.875.000')
            ->assertSee('IST-2026-0001');

        $this->get(route('seller.reports.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_product_detail_add_to_cart_flow_for_guest_and_buyer(): void
    {
        $this->get(route('products.show', $this->usedProduct))
            ->assertOk()
            ->assertSee('Masuk untuk Membeli');

        $this->actingAs($this->buyer);

        $this->get(route('products.show', $this->usedProduct))
            ->assertOk()
            ->assertSee(route('buyer.cart.store'), false)
            ->assertSee('Tambah ke Keranjang');

        $this->post(route('buyer.cart.store'), [
            'product_id' => $this->usedProduct->id,
            'quantity' => 1,
        ])->assertRedirect(route('buyer.cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->usedProduct->id,
            'quantity' => 1,
        ]);
    }

    public function test_seller_forms_and_fulfillment_ship_use_matching_request_field_names(): void
    {
        $this->actingAs($this->seller);

        $this->get(route('seller.promotions.create'))
            ->assertOk()
            ->assertSee('name="title"', false);

        $this->post(route('seller.promotions.store'), [
            'title' => 'Promo Audit Integrasi',
            'description' => 'Diskon khusus',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'is_active' => '1',
        ])->assertRedirect(route('seller.promotions.index'));

        $this->assertDatabaseHas('promotions', ['title' => 'Promo Audit Integrasi']);

        $this->get(route('seller.products.edit', $this->usedProduct))
            ->assertOk()
            ->assertSee('name="included_accessories"', false)
            ->assertSee('name="storage"', false)
            ->assertSee('name="color"', false);

        $order = Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'IST-SHIP-0001',
            'status' => OrderStatus::PROCESSING,
            'recipient_name' => 'Ani Wijaya',
            'recipient_phone' => '081234567899',
            'shipping_address' => 'Jl. Sudirman No. 10, Jakarta',
            'subtotal' => 12850000,
            'shipping_cost' => 25000,
            'total_amount' => 12875000,
        ]);

        $this->patch(route('seller.orders.ship', $order), [
            'courier_name' => 'JNE YES',
            'tracking_number' => 'JNE99887766',
        ])->assertRedirect(route('seller.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => OrderStatus::SHIPPED->value,
            'courier_name' => 'JNE YES',
            'tracking_number' => 'JNE99887766',
        ]);
    }

    public function test_orders_index_filters_by_status_for_buyer_and_seller(): void
    {
        Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'IST-FILTER-PAID',
            'status' => OrderStatus::PAID,
            'recipient_name' => 'Pembeli Satu',
            'recipient_phone' => '0811111111',
            'shipping_address' => 'Alamat 1',
            'subtotal' => 1000000,
            'shipping_cost' => 25000,
            'total_amount' => 1025000,
        ]);

        Order::create([
            'user_id' => $this->buyer->id,
            'order_code' => 'IST-FILTER-CANCEL',
            'status' => OrderStatus::CANCELLED,
            'recipient_name' => 'Pembeli Satu',
            'recipient_phone' => '0811111111',
            'shipping_address' => 'Alamat 1',
            'subtotal' => 1000000,
            'shipping_cost' => 25000,
            'total_amount' => 1025000,
        ]);

        $this->actingAs($this->buyer)
            ->get(route('buyer.orders.index', ['status' => 'paid']))
            ->assertOk()
            ->assertSee('IST-FILTER-PAID')
            ->assertDontSee('IST-FILTER-CANCEL');

        $this->actingAs($this->seller)
            ->get(route('seller.orders.index', ['status' => 'cancelled']))
            ->assertOk()
            ->assertSee('IST-FILTER-CANCEL')
            ->assertDontSee('IST-FILTER-PAID');
    }

    public function test_doku_config_resolves_sandbox_defaults(): void
    {
        $this->assertFalse(config('doku.is_production'));
        $this->assertSame('https://api-sandbox.doku.com', config('doku.base_url'));
    }
}
