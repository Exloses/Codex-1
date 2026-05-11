<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Orders 14 Hari';

    protected static string $color = 'primary';

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 13; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();

            $labels[] = $day->format('d M');
            $data[] = Order::query()
                ->whereBetween('created_at', [$day->copy()->startOfDay(), $day->copy()->endOfDay()])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
