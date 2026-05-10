<?php

namespace App\Policies;

use App\Models\ReturnRequest;
use App\Models\User;

class ReturnRequestPolicy
{
    public function view(User $user, ReturnRequest $returnRequest): bool
    {
        return $returnRequest->user_id === $user->id || $user->isAdmin();
    }
}
