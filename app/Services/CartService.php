<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class CartService
{
    /**
     * Get or create the authenticated buyer's cart.
     */
    public function getCart(User $buyer): Cart
    {
        return $buyer->cart()->firstOrCreate();
    }
}
