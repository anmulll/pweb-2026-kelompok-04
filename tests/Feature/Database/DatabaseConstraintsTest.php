<?php

namespace Tests\Feature\Database;

use App\Enums\ConditionGrade;
use App\Enums\FunctionalStatus;
use App\Enums\ProductCondition;
use App\Enums\UserRole;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConstraintsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'duplicate@istore.test']);

        $this->expectException(QueryException::class);
        User::factory()->create(['email' => 'duplicate@istore.test']);
    }

    public function test_category_slug_must_be_unique(): void
    {
        Category::factory()->create(['slug' => 'iphone']);

        $this->expectException(QueryException::class);
        Category::factory()->create(['slug' => 'iphone']);
    }

    public function test_product_sku_must_be_unique(): void
    {
        Product::factory()->create(['sku' => 'SKU-UNIQUE-01']);

        $this->expectException(QueryException::class);
        Product::factory()->create(['sku' => 'SKU-UNIQUE-01']);
    }

    public function test_single_cart_per_user_constraint(): void
    {
        $user = User::factory()->buyer()->create();
        Cart::factory()->create(['user_id' => $user->id]);

        $this->expectException(QueryException::class);
        Cart::factory()->create(['user_id' => $user->id]);
    }

    public function test_cart_item_unique_cart_and_product_combination(): void
    {
        $cart = Cart::factory()->create();
        $product = Product::factory()->create();

        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->expectException(QueryException::class);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_order_code_must_be_unique(): void
    {
        Order::factory()->create(['order_code' => 'ORD-TEST-001']);

        $this->expectException(QueryException::class);
        Order::factory()->create(['order_code' => 'ORD-TEST-001']);
    }

    public function test_payment_order_id_must_be_unique(): void
    {
        $order = Order::factory()->create();
        Payment::factory()->create(['order_id' => $order->id, 'gateway_order_id' => 'GW-001']);

        $this->expectException(QueryException::class);
        Payment::factory()->create(['order_id' => $order->id, 'gateway_order_id' => 'GW-002']);
    }

    public function test_payment_gateway_order_id_must_be_unique(): void
    {
        Payment::factory()->create(['gateway_order_id' => 'GW-UNIQUE-001']);

        $this->expectException(QueryException::class);
        Payment::factory()->create(['gateway_order_id' => 'GW-UNIQUE-001']);
    }

    public function test_cannot_delete_category_with_associated_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->expectException(QueryException::class);
        $category->delete();
    }

    public function test_cannot_delete_product_referenced_in_order_items(): void
    {
        $orderItem = OrderItem::factory()->create();
        $product = $orderItem->product;

        $this->expectException(QueryException::class);
        $product->delete();
    }

    public function test_deleting_cart_cascades_to_cart_items(): void
    {
        $cart = Cart::factory()->create();
        $item = CartItem::factory()->create(['cart_id' => $cart->id]);

        $cart->delete();

        $this->assertDatabaseMissing('cart_items', ['id' => $item->id]);
    }

    public function test_deleting_order_cascades_to_order_items_and_payment(): void
    {
        $order = Order::factory()->create();
        $item = OrderItem::factory()->create(['order_id' => $order->id]);
        $payment = Payment::factory()->create(['order_id' => $order->id]);

        $order->delete();

        $this->assertDatabaseMissing('order_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }
}
