<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            CategorySeeder::class,
            ShippingZoneSeeder::class,
            BannerSeeder::class,
            FaqSeeder::class,
            VendorSeeder::class,
            ProductSeeder::class,
        ]);

        $buyer = User::firstOrNew(['email' => 'buyer@demo.com']);

        $buyer->forceFill([
            'name' => 'Demo Buyer',
            'password' => Hash::make('Buyer123!'),
            'email_verified_at' => now(),
            'is_active' => true,
            'currency' => 'USD',
            'language' => 'en',
        ])->save();

        $buyer->assignRole('buyer');

        $testBuyer = User::firstOrNew(['email' => 'test@example.com']);

        $testBuyer->forceFill([
            'name' => 'Test Buyer',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'currency' => 'USD',
            'language' => 'en',
        ])->save();

        $testBuyer->assignRole('buyer');
    }
}
