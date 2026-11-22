<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSeller();
    }

    public function view(User $user, Category $category): bool
    {
        return $user->isSeller();
    }

    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    public function update(User $user, Category $category): bool
    {
        return $user->isSeller();
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->isSeller();
    }

    public function toggle(User $user, Category $category): bool
    {
        return $user->isSeller();
    }
}
