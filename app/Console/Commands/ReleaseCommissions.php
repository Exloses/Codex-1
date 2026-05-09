<?php

namespace App\Console\Commands;

use App\Models\AffiliateCommission;
use Illuminate\Console\Command;

class ReleaseCommissions extends Command
{
    protected $signature = 'affiliate:release-commissions';

    protected $description = 'Release pending affiliate commissions after the seven-day hold period.';

    public function handle(): int
    {
        $released = AffiliateCommission::query()
            ->where('status', 'pending')
            ->where('available_at', '<=', now())
            ->update(['status' => 'available']);

        $this->info("Released {$released} affiliate commission(s).");

        return self::SUCCESS;
    }
}
