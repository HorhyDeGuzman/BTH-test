<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id');

        if ($categoryIds->isEmpty()) {
            return;
        }

        Product::factory()
            ->count(50)
            ->state(fn () => ['category_id' => $categoryIds->random()])
            ->create();
    }
}
