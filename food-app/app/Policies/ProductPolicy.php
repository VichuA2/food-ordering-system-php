<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        return $user->id === $product->hotel->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        return $user->id === $product->hotel->user_id;
    }
}
