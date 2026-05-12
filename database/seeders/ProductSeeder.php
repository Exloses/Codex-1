<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['Batik Parang Resort Shirt', 'batik-nusantara', 'fashion-apparel', 18, 28, 850, true, ['Color' => ['Indigo', 'Maroon'], 'Size' => ['M', 'L']]],
            ['Handwoven Tenun Tote Bag', 'batik-nusantara', 'fashion-apparel', 15, 24, 650, true, ['Color' => ['Natural', 'Navy'], 'Size' => ['Regular', 'Large']]],
            ['Rattan Table Organizer', 'batik-nusantara', 'home-living', 11, 18, 700, false, ['Color' => ['Honey', 'Dark Brown'], 'Size' => ['Small', 'Medium']]],
            ['Herbal Spa Gift Set', 'batik-nusantara', 'beauty-health', 13, 21, 900, false, ['Scent' => ['Jasmine', 'Frangipani'], 'Size' => ['Travel', 'Full']]],
            ['Outdoor Batik Bucket Hat', 'batik-nusantara', 'sports-outdoors', 9, 15, 250, false, ['Color' => ['Black', 'Cream'], 'Size' => ['M', 'L']]],
            ['Wireless Travel Charger', 'techgadget-id', 'electronics-gadgets', 22, 35, 350, true, ['Color' => ['Black', 'White'], 'Power' => ['15W', '20W']]],
            ['Bluetooth Mini Speaker', 'techgadget-id', 'electronics-gadgets', 20, 32, 550, true, ['Color' => ['Graphite', 'Blue'], 'Size' => ['Mini', 'Plus']]],
            ['Smart LED Desk Lamp', 'techgadget-id', 'home-living', 26, 40, 1200, false, ['Color' => ['White', 'Silver'], 'Brightness' => ['Warm', 'Daylight']]],
            ['Digital Fitness Counter', 'techgadget-id', 'sports-outdoors', 14, 23, 200, false, ['Color' => ['Black', 'Green'], 'Pack' => ['Single', 'Twin']]],
            ['Portable Beauty Mirror Light', 'techgadget-id', 'beauty-health', 16, 26, 420, false, ['Color' => ['Rose', 'Pearl'], 'Size' => ['Compact', 'Wide']]],
        ];

        foreach ($products as $index => [$name, $vendorSlug, $categorySlug, $vendorPrice, $sellingPrice, $weight, $featured, $attributes]) {
            $vendor = Vendor::where('slug', $vendorSlug)->firstOrFail();
            $category = Category::where('slug', $categorySlug)->firstOrFail();
            $slug = str($name)->slug()->toString();

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'vendor_id' => $vendor->id,
                    'category_id' => $category->id,
                    'size_guide_id' => $category->sizeGuide?->id,
                    'name' => $name,
                    'description' => $this->descriptionFor($name, $vendor->store_name),
                    'vendor_price' => $vendorPrice,
                    'selling_price' => $sellingPrice,
                    'compare_price' => round($sellingPrice * 1.2, 2),
                    'stock' => 80 + ($index * 11),
                    'weight' => $weight,
                    'sku' => 'GD-'.str($vendorSlug)->upper()->substr(0, 3).'-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'is_featured' => $featured,
                    'total_sales' => 0,
                    'average_rating' => 0,
                    'videos' => [
                        ['type' => 'image', 'url' => "https://picsum.photos/600/600?random=".($index + 101)],
                    ],
                ],
            );

            $this->syncAttributesAndVariants($product, $attributes, $index);
        }
    }

    private function syncAttributesAndVariants(Product $product, array $attributes, int $productIndex): void
    {
        $createdAttributes = [];

        foreach ($attributes as $name => $values) {
            $attribute = $product->attributes()->updateOrCreate(
                ['name' => $name],
                ['sort_order' => count($createdAttributes) + 1],
            );

            foreach ($values as $valueIndex => $value) {
                $attribute->values()->updateOrCreate(
                    ['value' => $value],
                    [
                        'color_hex' => $name === 'Color' ? $this->colorHex($value) : null,
                        'sort_order' => $valueIndex + 1,
                    ],
                );
            }

            $createdAttributes[$name] = $values;
        }

        $attributeNames = array_keys($createdAttributes);
        $variantNumber = 1;

        foreach ($createdAttributes[$attributeNames[0]] as $firstValue) {
            foreach ($createdAttributes[$attributeNames[1]] as $secondValue) {
                $combination = [
                    $attributeNames[0] => $firstValue,
                    $attributeNames[1] => $secondValue,
                ];

                $product->variants()->updateOrCreate(
                    ['sku' => $product->sku.'-V'.$variantNumber],
                    [
                        'combination' => $combination,
                        'price' => $product->selling_price + ($variantNumber - 1),
                        'vendor_price' => $product->vendor_price,
                        'stock' => 20 + ($productIndex * 3) + $variantNumber,
                        'image' => 'https://picsum.photos/600/600?random='.(($productIndex + 1) * 10 + $variantNumber),
                    ],
                );

                $variantNumber++;
            }
        }
    }

    private function descriptionFor(string $name, string $storeName): string
    {
        return "{$name} from {$storeName}, selected for global dropship buyers with reliable fulfillment from Indonesia.";
    }

    private function colorHex(string $color): ?string
    {
        return [
            'Indigo' => '#3730a3',
            'Maroon' => '#7f1d1d',
            'Natural' => '#d6d3d1',
            'Navy' => '#1e3a8a',
            'Honey' => '#b45309',
            'Dark Brown' => '#3f2f24',
            'Black' => '#111827',
            'Cream' => '#f5f5dc',
            'White' => '#ffffff',
            'Graphite' => '#374151',
            'Blue' => '#2563eb',
            'Silver' => '#cbd5e1',
            'Green' => '#16a34a',
            'Rose' => '#fb7185',
            'Pearl' => '#f8fafc',
        ][$color] ?? null;
    }
}
