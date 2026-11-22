<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new public buyer account.
     *
     * @param  array{name: string, email: string, password: string, phone?: ?string, address?: ?string}  $data
     */
    public function registerBuyer(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => UserRole::Buyer,
        ]);

        Auth::login($user);

        return $user;
    }

    /**
     * Authenticate a user with email and password.
     *
     * @param  array{email: string, password: string}  $credentials
     *
     * @throws ValidationException
     */
    public function authenticate(array $credentials, bool $remember = false): User
    {
        $normalized = [
            'email' => strtolower(trim($credentials['email'])),
            'password' => $credentials['password'],
        ];

        if (! Auth::attempt($normalized, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }

    public function dashboardRouteFor(User $user): string
    {
        return $user->isSeller() ? 'seller.dashboard' : 'buyer.dashboard';
    }
}
