<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';

    protected $description = 'Cancel orders that remain unpaid for more than 24 hours.';

    public function handle(): int
    {
        $cancelled = Order::query()
            ->where('payment_status', 'unpaid')
            ->whereIn('status', ['pending', 'created'])
            ->where('created_at', '<=', now()->subDay())
            ->update(['status' => 'cancelled']);

        $this->info("Cancelled {$cancelled} unpaid order(s).");

        return self::SUCCESS;
    }
}
