<?php

namespace Tests\Feature\Buyer;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buyer = User::factory()->buyer()->create([
            'name' => 'Original Buyer Name',
            'email' => 'buyer@istore.test',
            'phone' => '081234567890',
            'default_shipping_address' => 'Jl. Awal No. 1, Jakarta',
        ]);
    }

    public function test_buyer_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->buyer)->get(route('buyer.profile'));

        $response->assertStatus(200);
    }

    public function test_buyer_can_update_name_phone_and_default_shipping_address(): void
    {
        $response = $this->actingAs($this->buyer)->put(route('buyer.profile.update'), [
            'name' => 'Updated Buyer Name',
            'phone' => '089988776655',
            'default_shipping_address' => 'Jl. Baru No. 99, Makassar',
        ]);

        $response->assertRedirect(route('buyer.profile'));

        $this->assertDatabaseHas('users', [
            'id' => $this->buyer->id,
            'name' => 'Updated Buyer Name',
            'phone' => '089988776655',
            'default_shipping_address' => 'Jl. Baru No. 99, Makassar',
        ]);
    }

    public function test_buyer_cannot_update_email_via_profile(): void
    {
        $response = $this->actingAs($this->buyer)->put(route('buyer.profile.update'), [
            'name' => 'Buyer Attempt Email Change',
            'email' => 'hacked@email.com',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect(route('buyer.profile'));

        $this->assertDatabaseHas('users', [
            'id' => $this->buyer->id,
            'email' => 'buyer@istore.test', // email remains intact
        ]);
    }

    public function test_buyer_cannot_escalate_role_via_profile(): void
    {
        $response = $this->actingAs($this->buyer)->put(route('buyer.profile.update'), [
            'name' => 'Buyer Attempt Role Escalation',
            'role' => 'seller',
        ]);

        $response->assertRedirect(route('buyer.profile'));

        $this->buyer->refresh();
        $this->assertSame(UserRole::Buyer, $this->buyer->role);
    }

    public function test_seller_is_forbidden_from_buyer_profile(): void
    {
        $seller = User::factory()->seller()->create();

        $response = $this->actingAs($seller)->get(route('buyer.profile'));

        $response->assertStatus(403);
    }
}
