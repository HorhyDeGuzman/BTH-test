<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Электроника',
                'name_en' => 'Electronics',
                'description' => 'Смартфоны, ноутбуки, телевизоры и другие гаджеты.',
                'description_en' => 'Phones, laptops, TVs and other gadgets.',
            ],
            [
                'name' => 'Книги',
                'name_en' => 'Books',
                'description' => 'Художественная и научно-популярная литература.',
                'description_en' => 'Fiction, non-fiction and educational titles.',
            ],
            [
                'name' => 'Одежда',
                'name_en' => 'Clothing',
                'description' => 'Повседневная и деловая одежда для любого сезона.',
                'description_en' => 'Casual and formal wear for all seasons.',
            ],
            [
                'name' => 'Дом и кухня',
                'name_en' => 'Home & Kitchen',
                'description' => 'Техника, посуда и всё необходимое для дома.',
                'description_en' => 'Appliances, cookware and everyday essentials.',
            ],
            [
                'name' => 'Спорт и отдых',
                'name_en' => 'Sports & Outdoors',
                'description' => 'Экипировка для фитнеса, туризма и активного отдыха.',
                'description_en' => 'Gear for fitness, travel and the outdoors.',
            ],
            [
                'name' => 'Игрушки и игры',
                'name_en' => 'Toys & Games',
                'description' => 'Настольные игры, конструкторы, приставки.',
                'description_en' => 'Board games, construction sets and consoles.',
            ],
            [
                'name' => 'Красота',
                'name_en' => 'Beauty',
                'description' => 'Уход за кожей, косметика и личная гигиена.',
                'description_en' => 'Skincare, cosmetics and personal care.',
            ],
            [
                'name' => 'Автотовары',
                'name_en' => 'Automotive',
                'description' => 'Аксессуары и всё для обслуживания автомобиля.',
                'description_en' => 'Car accessories and maintenance.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
