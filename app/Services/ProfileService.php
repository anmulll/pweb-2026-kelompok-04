<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    /**
     * Update Buyer profile.
     * Enforces FR-PROF-01: Only name, phone, and default_shipping_address can be updated.
     * Email and Role are strictly immutable.
     */
    public function updateProfile(User $user, array $data): User
    {
        $filtered = [
            'name' => $data['name'] ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
            'default_shipping_address' => $data['default_shipping_address'] ?? $data['address'] ?? $user->default_shipping_address,
        ];

        $user->update($filtered);

        return $user->fresh();
    }
}
