<?php

namespace App\Console\Commands;

use App\Models\Affiliate;
use App\Services\AffiliateService;
use Illuminate\Console\Command;

class CheckAffiliateTiers extends Command
{
    protected $signature = 'affiliate:check-tiers';

    protected $description = 'Check active affiliates and upgrade tiers based on sales thresholds.';

    public function handle(AffiliateService $affiliateService): int
    {
        $checked = 0;
        $changed = 0;

        Affiliate::query()
            ->where('is_active', true)
            ->chunkById(100, function ($affiliates) use ($affiliateService, &$checked, &$changed) {
                foreach ($affiliates as $affiliate) {
                    $originalTier = $affiliate->tier;
                    $affiliateService->checkAndUpgradeTier($affiliate);

                    $checked++;
                    $changed += $affiliate->tier !== $originalTier ? 1 : 0;
                }
            });

        $this->info("Checked {$checked} affiliate(s); upgraded {$changed} tier(s).");

        return self::SUCCESS;
    }
}
