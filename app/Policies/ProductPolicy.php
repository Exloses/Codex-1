<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function manage(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->vendor?->id === $product->vendor_id;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }
}
