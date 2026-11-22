<?php

namespace App\Policies;

use App\Models\Promotion;
use App\Models\User;

class PromotionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller();
    }

    public function view(User $user, Promotion $promotion): bool
    {
        return $user->isSeller();
    }

    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    public function update(User $user, Promotion $promotion): bool
    {
        return $user->isSeller();
    }

    public function delete(User $user, Promotion $promotion): bool
    {
        return $user->isSeller();
    }

    public function toggle(User $user, Promotion $promotion): bool
    {
        return $user->isSeller();
    }
}
