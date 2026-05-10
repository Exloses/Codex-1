<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

class VendorPolicy
{
    public function manage(User $user, Vendor $vendor): bool
    {
        return $user->isAdmin() || $vendor->user_id === $user->id;
    }
}
