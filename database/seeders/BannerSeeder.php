<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Curated Indonesian Finds for Global Buyers',
                'image' => 'https://picsum.photos/1200/400?random=1',
                'link' => '/products',
            ],
            [
                'title' => 'Fast-Moving Fashion and Lifestyle Drops',
                'image' => 'https://picsum.photos/1200/400?random=2',
                'link' => '/category/fashion-apparel',
            ],
            [
                'title' => 'Trusted Vendors, Global Delivery',
                'image' => 'https://picsum.photos/1200/400?random=3',
                'link' => '/faq',
            ],
        ];

        foreach ($banners as $index => $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title']],
                $banner + ['is_active' => true, 'sort_order' => $index + 1],
            );
        }
    }
}
