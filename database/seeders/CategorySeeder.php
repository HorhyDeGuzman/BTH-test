<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Phones, laptops, TVs and other gadgets.'],
            ['name' => 'Books', 'description' => 'Fiction, non-fiction, and educational titles.'],
            ['name' => 'Clothing', 'description' => 'Casual and formal wear for all seasons.'],
            ['name' => 'Home & Kitchen', 'description' => 'Furniture, appliances and everyday essentials.'],
            ['name' => 'Sports & Outdoors', 'description' => 'Gear for fitness, travel and the outdoors.'],
            ['name' => 'Toys & Games', 'description' => 'Board games, toys and puzzles.'],
            ['name' => 'Beauty', 'description' => 'Skincare, cosmetics and personal care.'],
            ['name' => 'Automotive', 'description' => 'Car accessories and maintenance.'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']],
            );
        }
    }
}
