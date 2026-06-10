<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function update(User $user, Order $order): bool
    {
        if (($user->hasRole('Admin'))) {
            return true;
        }
        if ($user->hasRole('Customer')) {
            return $user->id === $order->user_id;
        }
        if ($user->hasRole('Hotel Owner')) {
            return $user->id === $order->hotel->user_id;
        }

        if ($user->hasRole('Delivery Person')) {
            return $user->id === $order->delivery_person_id;
        }
        return false;
    }

    public function pick(User $user, Order $order): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        if ($user->hasRole('Delivery Person')) {
            return is_null($order->delivery_person_id);
        }

        return false;
    }
}
