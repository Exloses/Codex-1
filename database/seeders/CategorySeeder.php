<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fashion & Apparel',
                'slug' => 'fashion-apparel',
                'icon' => 'shirt',
                'guide' => [
                    'name' => 'Fashion Apparel Size Guide',
                    'columns' => ['US', 'EU', 'UK', 'CM'],
                    'rows' => [
                        ['US' => '6', 'EU' => '38', 'UK' => '5.5', 'CM' => '24'],
                        ['US' => '7', 'EU' => '39', 'UK' => '6.5', 'CM' => '25'],
                        ['US' => '8', 'EU' => '41', 'UK' => '7.5', 'CM' => '26'],
                        ['US' => '9', 'EU' => '42', 'UK' => '8.5', 'CM' => '27'],
                        ['US' => '10', 'EU' => '43', 'UK' => '9.5', 'CM' => '28'],
                    ],
                ],
            ],
            [
                'name' => 'Electronics & Gadgets',
                'slug' => 'electronics-gadgets',
                'icon' => 'smartphone',
                'guide' => $this->standardGuide('Electronics Fit Guide'),
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'icon' => 'home',
                'guide' => $this->standardGuide('Home Goods Size Guide'),
            ],
            [
                'name' => 'Beauty & Health',
                'slug' => 'beauty-health',
                'icon' => 'sparkles',
                'guide' => $this->standardGuide('Beauty Product Size Guide'),
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'icon' => 'dumbbell',
                'guide' => $this->standardGuide('Sports Gear Size Guide'),
            ],
        ];

        foreach ($categories as $index => $data) {
            $category = Category::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );

            $category->sizeGuide()->updateOrCreate(
                ['category_id' => $category->id],
                [
                    'name' => $data['guide']['name'],
                    'columns' => $data['guide']['columns'],
                    'rows' => $data['guide']['rows'],
                    'notes' => 'Measurements are approximate. Choose the larger size when between two sizes.',
                ],
            );
        }
    }

    private function standardGuide(string $name): array
    {
        return [
            'name' => $name,
            'columns' => ['S', 'M', 'L', 'XL', 'XXL'],
            'rows' => [
                ['S' => 'Compact', 'M' => 'Standard', 'L' => 'Large', 'XL' => 'Extra large', 'XXL' => 'Oversized'],
                ['S' => 'Under 20cm', 'M' => '20-30cm', 'L' => '30-45cm', 'XL' => '45-60cm', 'XXL' => '60cm+'],
                ['S' => 'Light', 'M' => 'Daily', 'L' => 'Family', 'XL' => 'Bulk', 'XXL' => 'Commercial'],
            ],
        ];
    }
}
