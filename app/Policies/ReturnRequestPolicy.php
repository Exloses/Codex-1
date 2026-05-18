<?php

namespace App\Policies;

use App\Enums\ReturnRequestStatus;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\User;
use App\Services\ReturnRefundService;

class ReturnRequestPolicy
{
    public function view(User $user, ReturnRequest $returnRequest): bool
    {
        return $returnRequest->user_id === $user->id || $user->isAdmin();
    }

    public function create(User $user, Order $order): bool
    {
        return app(ReturnRefundService::class)->canCreateForOrder($user, $order);
    }

    public function cancel(User $user, ReturnRequest $returnRequest): bool
    {
        return $returnRequest->user_id === $user->id
            && in_array($returnRequest->statusValue(), [
                ReturnRequestStatus::Pending->value,
                ReturnRequestStatus::UnderReview->value,
            ], true);
    }

    public function update(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->isAdmin();
    }

    public function processRefund(User $user, ReturnRequest $returnRequest): bool
    {
        return $user->isAdmin();
    }
}
