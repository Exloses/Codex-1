<?php

namespace App\Filament\Widgets;

use App\Models\AffiliatePayout;
use App\Models\Order;
use App\Models\ReturnRequest;
use App\Models\SupportTicket;
use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $todayRevenue = Order::query()
            ->whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total_usd');

        $monthlyOrders = Order::query()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        return [
            Stat::make('Revenue hari ini', '$'.number_format((float) $todayRevenue, 2))
                ->description('Paid orders in USD')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Order bulan ini', number_format($monthlyOrders))
                ->description('Total orders created')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            Stat::make('Vendor aktif', number_format(Vendor::query()->where('is_approved', true)->count()))
                ->description('Approved suppliers')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('info'),
            Stat::make('Pending payouts affiliate', number_format(AffiliatePayout::query()->where('status', 'pending')->count()))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning'),
            Stat::make('Open support tickets', number_format(SupportTicket::query()->where('status', 'open')->count()))
                ->description('Customer support queue')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('danger'),
            Stat::make('Pending return requests', number_format(ReturnRequest::query()->where('status', 'pending')->count()))
                ->description('Needs admin review')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color('warning'),
        ];
    }
}
