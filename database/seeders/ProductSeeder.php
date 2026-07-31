<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            // ── WET WIPES ──────────────────────────────────────────────────
            'Wet Wipes' => [
                'Wet Wipes' => [
                    'Aloe-Vera Wet Wipes',
                    'Lemon Wet Wipes',
                    'Lemon Grass Wet Wipes',
                    'Vitamin C Wet Wipes',
                    'Premium Eau De Cologne Wet Wipes',
                    'Rose Wet Wipes',
                ],
            ],

            // ── SUNSCREEN ──────────────────────────────────────────────────
            'Sunscreen' => [
                'Sunscreen' => [
                    // List to be updated once product catalogue is provided
                    'SPF 30 Sunscreen Lotion',
                    'SPF 50 Sunscreen Cream',
                    'SPF 50+ PA+++ Sunscreen Gel',
                ],
            ],

            // ── SKINCARE ───────────────────────────────────────────────────
            'Skincare' => [
                'Face Wash' => [
                    'Lemon Face Wash',
                    'Strawberry Face Wash',
                    'Skin Detoxifying Neem Face Wash',
                    'Papaya Face Wash',
                ],
                'Face Cream' => [
                    'Papaya Cream',
                    'Lemon Cream',
                    'Orange Cream',
                    'Skin Detoxifying Neem Cream',
                ],
                'Facial Gel' => [
                    'Strawberry Facial Gel',
                    'Lemon Facial Gel',
                    'Papaya Facial Gel',
                    'Orange Facial Gel',
                    'Fresh Cucumber Facial Gel',
                    'Aloe-Vera Facial Gel',
                ],
                'Face Pack & Scrub' => [
                    'Acne Control Face Pack',
                    'Pimple Face Pack',
                    'Body Polish Walnut Scrub',
                    'Apricot Facial Scrub',
                ],
                'Cleansing Milk' => [
                    'Lemon Cleansing Milk',
                    'Orange Cleansing Milk',
                    'Papaya Cleansing Milk',
                    'Skin Detoxifying Neem Cleansing Milk',
                ],
                'Peel Off Mask' => [
                    'Orange Peel Off Mask',
                    'Lemon Peel Off Mask',
                ],
            ],

            // ── HAIRCARE ───────────────────────────────────────────────────
            'Haircare' => [
                'Shampoo and Conditioner' => [
                    'Special Herbal Shampoo and Conditioner',
                    'Barley Shampoo and Conditioner',
                    'Amla Henna Shampoo and Conditioner',
                    'Milk Shampoo and Conditioner',
                    'Amla Shikakai Shampoo and Conditioner',
                    'Pearl Aloe-Vera Shampoo and Conditioner',
                    'A.D Shampoo and Conditioner',
                    'Amla Aloe-Vera Shampoo and Conditioner',
                ],
                'Shampoo' => [
                    'Silky Soft Shampoo',
                ],
                'Conditioner' => [
                    'Balsam Conditioner',
                    'Protein Conditioner',
                ],
                'Hair Cream' => [
                    'Hair Spa Deep Smoothening Cream',
                ],
            ],

            // ── BODYCARE ───────────────────────────────────────────────────
            'Bodycare' => [
                'Lotion' => [
                    'Moisturizing Lotion',
                    'Astringent Lotion',
                    'Skin Toner & Fresher Lotion',
                    'Camphor Lotion',
                    'Hand & Body Lotion',
                    'Aloe-Vera Moisturizing Lotion',
                ],
                'Bath Salt' => [
                    'Lavender Bath Salt',
                    'Oud Bath Salt',
                    'Peach Addiction Bath Salt',
                    'Pineapple Bath Salt',
                    'Santra Bath Salt',
                    'Tea Rose Bath Salt',
                    'Turmeric Treasure Bath Salt',
                ],
            ],
        ];

        $skuCounter = 1;

        foreach ($catalog as $categoryName => $subCategories) {
            $category = Category::updateOrCreate(
                ['slug' => Str::slug($categoryName)],
                [
                    'name'       => $categoryName,
                    'slug'       => Str::slug($categoryName),
                    'is_active'  => true,
                    'sort_order' => $skuCounter,
                ]
            );

            foreach ($subCategories as $subCategoryName => $products) {
                $subCategory = SubCategory::updateOrCreate(
                    ['slug' => Str::slug($subCategoryName . '-' . $categoryName)],
                    [
                        'category_id' => $category->id,
                        'name'        => $subCategoryName,
                        'slug'        => Str::slug($subCategoryName . '-' . $categoryName),
                        'is_active'   => true,
                    ]
                );

                foreach ($products as $productName) {
                    $sku = 'BF-' . strtoupper(substr(Str::slug($categoryName), 0, 2))
                         . '-' . str_pad($skuCounter, 4, '0', STR_PAD_LEFT);

                    Product::updateOrCreate(
                        ['slug' => Str::slug($productName)],
                        [
                            'sub_category_id'   => $subCategory->id,
                            'name'              => $productName,
                            'slug'              => Str::slug($productName),
                            'sku'               => $sku,
                            'mrp'               => 0.00,   // To be updated by admin
                            'unit_price'        => 0.00,
                            'distributor_price' => 0.00,
                            'is_active'         => true,
                        ]
                    );

                    $skuCounter++;
                }
            }
        }

        $total = Product::count();
        $this->command->info("✅ Product catalog seeded: {$total} products across 5 categories.");
    }
}
