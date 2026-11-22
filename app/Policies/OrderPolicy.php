<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     * Buyer can only view their own orders; Seller can view all orders.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isSeller()) {
            return true;
        }

        return $user->isBuyer() && $user->id === $order->user_id;
    }

    /**
     * Buyer can cancel only their own order if it's pending_payment or paid.
     * Seller can cancel before shipped.
     */
    public function cancel(User $user, Order $order): bool
    {
        if ($user->isSeller()) {
            return $order->canBeCancelled();
        }

        return $user->isBuyer() && $user->id === $order->user_id && $order->canBeCancelled();
    }

    /**
     * Only Buyer who owns the order can confirm receipt (completed).
     */
    public function complete(User $user, Order $order): bool
    {
        return $user->isBuyer() && $user->id === $order->user_id && $order->status === \App\Enums\OrderStatus::SHIPPED;
    }
}
