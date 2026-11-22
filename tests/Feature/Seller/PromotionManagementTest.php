<?php

namespace Tests\Feature\Seller;

use App\Enums\UserRole;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = User::factory()->create([
            'role' => UserRole::SELLER,
        ]);

        $this->buyer = User::factory()->create([
            'role' => UserRole::BUYER,
        ]);
    }

    public function test_guest_is_redirected_from_seller_promotions(): void
    {
        $response = $this->get(route('seller.promotions.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_buyer_is_forbidden_from_seller_promotions(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('seller.promotions.index'));

        $response->assertStatus(403);
    }

    public function test_seller_can_create_promotion(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.promotions.store'), [
            'title' => 'Diskon Awal Tahun Apple',
            'description' => 'Diskon hingga Rp 2.000.000 untuk pembelian MacBook',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('seller.promotions.index'));

        $this->assertDatabaseHas('promotions', [
            'title' => 'Diskon Awal Tahun Apple',
            'created_by' => $this->seller->id,
            'is_active' => true,
        ]);
    }

    public function test_promotion_end_date_cannot_be_before_start_date(): void
    {
        $response = $this->actingAs($this->seller)->post(route('seller.promotions.store'), [
            'title' => 'Promosi Tanggal Tidak Valid',
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->toDateString(), // End date before start date
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_seller_can_toggle_promotion_status(): void
    {
        $promo = Promotion::create([
            'created_by' => $this->seller->id,
            'title' => 'Flash Sale Apple Watch',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->seller)->post(route('seller.promotions.toggle', $promo));

        $response->assertRedirect();
        $this->assertDatabaseHas('promotions', [
            'id' => $promo->id,
            'is_active' => false,
        ]);
    }

    public function test_seller_can_delete_promotion(): void
    {
        $promo = Promotion::create([
            'created_by' => $this->seller->id,
            'title' => 'Promo Lama',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->subDays(2)->toDateString(),
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->seller)->delete(route('seller.promotions.destroy', $promo));

        $response->assertRedirect(route('seller.promotions.index'));
        $this->assertDatabaseMissing('promotions', ['id' => $promo->id]);
    }
}
