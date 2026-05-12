<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@platform.com']);

        $admin->forceFill([
            'name' => 'Platform Admin',
            'password' => Hash::make('Admin123!'),
            'email_verified_at' => now(),
            'is_active' => true,
            'currency' => 'USD',
            'language' => 'en',
        ])->save();

        $admin->assignRole('admin');
    }
}
