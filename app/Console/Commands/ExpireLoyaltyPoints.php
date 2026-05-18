<?php

namespace App\Console\Commands;

use App\Services\LoyaltyService;
use Illuminate\Console\Command;

class ExpireLoyaltyPoints extends Command
{
    protected $signature = 'loyalty:expire-points';

    protected $description = 'Expire loyalty points past their expiration date.';

    public function handle(LoyaltyService $loyaltyService): int
    {
        $expiredPoints = $loyaltyService->expirePoints();

        $this->info("Expired {$expiredPoints} loyalty points.");

        return self::SUCCESS;
    }
}
