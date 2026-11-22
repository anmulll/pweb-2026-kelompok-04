<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class CartService
{
    /**
     * Get or create the buyer's cart.
     *
     * @throws AuthorizationException
     */
    public function getCart(User $buyer): Cart
    {
        if (!$buyer->isBuyer()) {
            throw new AuthorizationException(
                'Only buyers can access a shopping cart.'
            );
        }

        return $buyer->cart()->firstOrCreate();
    }
}
