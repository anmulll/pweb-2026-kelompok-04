<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;

class CartPolicy
{
    /**
     * Determine whether the user can view or update the cart.
     */
    public function view(User $user, Cart $cart): bool
    {
        return $user->isBuyer() && $user->id === $cart->user_id;
    }

    public function update(User $user, Cart $cart): bool
    {
        return $user->isBuyer() && $user->id === $cart->user_id;
    }

    public function delete(User $user, Cart $cart): bool
    {
        return $user->isBuyer() && $user->id === $cart->user_id;
    }
}
