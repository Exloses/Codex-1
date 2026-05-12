<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'user' => [
                    'name' => 'Batik Nusantara Vendor',
                    'email' => 'vendor1@demo.com',
                    'password' => 'Vendor123!',
                ],
                'store' => [
                    'store_name' => 'Batik Nusantara',
                    'slug' => 'batik-nusantara',
                    'description' => 'Solo-based fashion vendor specializing in batik apparel and lifestyle goods.',
                    'province' => 'Jawa Tengah',
                    'city' => 'Solo',
                    'commission_rate' => 15,
                ],
            ],
            [
                'user' => [
                    'name' => 'TechGadget ID Vendor',
                    'email' => 'vendor2@demo.com',
                    'password' => 'Vendor123!',
                ],
                'store' => [
                    'store_name' => 'TechGadget ID',
                    'slug' => 'techgadget-id',
                    'description' => 'Bandung gadget supplier offering practical electronics and travel-ready accessories.',
                    'province' => 'Jawa Barat',
                    'city' => 'Bandung',
                    'commission_rate' => 12,
                ],
            ],
        ];

        foreach ($vendors as $vendorData) {
            $user = User::firstOrNew(['email' => $vendorData['user']['email']]);

            $user->forceFill([
                'name' => $vendorData['user']['name'],
                'password' => Hash::make($vendorData['user']['password']),
                'email_verified_at' => now(),
                'is_active' => true,
                'currency' => 'USD',
                'language' => 'en',
            ])->save();

            $user->assignRole('vendor');

            Vendor::updateOrCreate(
                ['slug' => $vendorData['store']['slug']],
                $vendorData['store'] + [
                    'user_id' => $user->id,
                    'logo' => 'https://picsum.photos/300/300?random='.($vendorData['store']['slug'] === 'batik-nusantara' ? 11 : 12),
                    'banner' => 'https://picsum.photos/1200/400?random='.($vendorData['store']['slug'] === 'batik-nusantara' ? 21 : 22),
                    'is_approved' => true,
                    'balance_idr' => 0,
                ],
            );
        }
    }
}
