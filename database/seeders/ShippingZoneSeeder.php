<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Southeast Asia',
                'countries' => ['SG', 'MY', 'TH', 'PH', 'VN'],
                'rates' => [
                    ['name' => 'Standard', 'carrier' => 'GlobalDropship Standard', 'estimated_days' => '7-14 days', 'price_usd' => 8],
                    ['name' => 'Express', 'carrier' => 'GlobalDropship Express', 'estimated_days' => '3-5 days', 'price_usd' => 18],
                ],
            ],
            [
                'name' => 'East Asia & Australia',
                'countries' => ['JP', 'KR', 'AU', 'NZ', 'HK', 'TW'],
                'rates' => [
                    ['name' => 'Standard', 'carrier' => 'GlobalDropship Standard', 'estimated_days' => '10-18 days', 'price_usd' => 12],
                    ['name' => 'Express', 'carrier' => 'GlobalDropship Express', 'estimated_days' => '5-7 days', 'price_usd' => 25],
                ],
            ],
            [
                'name' => 'Americas & Europe',
                'countries' => ['US', 'CA', 'GB', 'DE', 'FR', 'NL', 'IT', 'ES'],
                'rates' => [
                    ['name' => 'Standard', 'carrier' => 'GlobalDropship Standard', 'estimated_days' => '14-21 days', 'price_usd' => 15],
                    ['name' => 'Express', 'carrier' => 'GlobalDropship Express', 'estimated_days' => '7-10 days', 'price_usd' => 35],
                ],
            ],
        ];

        foreach ($zones as $zoneData) {
            $zone = ShippingZone::updateOrCreate(
                ['name' => $zoneData['name']],
                ['countries' => $zoneData['countries'], 'is_active' => true],
            );

            foreach ($zoneData['rates'] as $rate) {
                $zone->rates()->updateOrCreate(
                    ['name' => $rate['name']],
                    $rate + ['min_weight' => 0, 'max_weight' => 99999],
                );
            }
        }
    }
}
