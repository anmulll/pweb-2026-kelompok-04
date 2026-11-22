<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_register_and_login_pages(): void
    {
        $this->get(route('register'))->assertOk();
        $this->get(route('login'))->assertOk();
    }

    public function test_public_registration_always_creates_buyer_and_redirects_to_buyer_dashboard(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Budi Santoso',
            'email' => 'Budi@Example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '081234567890',
            'address' => 'Jl. Perintis Kemerdekaan No. 10, Makassar',
            'role' => 'seller', // Attempted privilege escalation must be ignored
        ]);

        $response->assertRedirect(route('buyer.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->firstOrFail();
        $this->assertSame(UserRole::Buyer, $user->role);
        $this->assertTrue($user->isBuyer());
        $this->assertFalse($user->isSeller());
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertSame('081234567890', $user->phone);
    }

    public function test_buyer_login_redirects_to_buyer_dashboard(): void
    {
        $buyer = User::factory()->buyer()->create([
            'password' => 'secret123',
        ]);

        $response = $this->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('buyer.dashboard'));
        $this->assertAuthenticatedAs($buyer);
    }

    public function test_seller_login_redirects_to_seller_dashboard(): void
    {
        $seller = User::factory()->seller()->create([
            'password' => 'secret123',
        ]);

        $response = $this->post(route('login'), [
            'email' => $seller->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('seller.dashboard'));
        $this->assertAuthenticatedAs($seller);
    }

    public function test_invalid_login_fails_without_leaking_account_existence(): void
    {
        $buyer = User::factory()->buyer()->create([
            'password' => 'secret123',
        ]);

        $wrongPassword = $this->from(route('login'))->post(route('login'), [
            'email' => $buyer->email,
            'password' => 'wrong-password',
        ]);

        $wrongPassword->assertRedirect(route('login'));
        $wrongPassword->assertSessionHasErrors('email');
        $this->assertGuest();

        $unknownEmail = $this->from(route('login'))->post(route('login'), [
            'email' => 'tidak-ada@example.com',
            'password' => 'wrong-password',
        ]);

        $unknownEmail->assertRedirect(route('login'));
        $unknownEmail->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout_and_is_redirected_home(): void
    {
        $buyer = User::factory()->buyer()->create();

        $response = $this->actingAs($buyer)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login_for_private_routes(): void
    {
        $this->get(route('buyer.dashboard'))->assertRedirect(route('login'));
        $this->get(route('seller.dashboard'))->assertRedirect(route('login'));
    }

    public function test_buyer_can_access_buyer_area_and_is_forbidden_from_seller_area(): void
    {
        $buyer = User::factory()->buyer()->create();

        $this->actingAs($buyer)->get(route('buyer.dashboard'))->assertOk();
        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertForbidden();
    }

    public function test_seller_can_access_seller_area_and_is_forbidden_from_buyer_area(): void
    {
        $seller = User::factory()->seller()->create();

        $this->actingAs($seller)->get(route('seller.dashboard'))->assertOk();
        $this->actingAs($seller)->get(route('buyer.dashboard'))->assertForbidden();
    }

    public function test_authenticated_users_visiting_guest_auth_pages_are_redirected_to_their_dashboard(): void
    {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();

        $this->actingAs($buyer)->get(route('login'))->assertRedirect(route('buyer.dashboard'));
        $this->actingAs($seller)->get(route('login'))->assertRedirect(route('seller.dashboard'));
    }

    public function test_login_endpoint_is_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post(route('login'), [
                'email' => 'rate-limit@example.com',
                'password' => 'wrong-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => 'rate-limit@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(429);
    }
}
