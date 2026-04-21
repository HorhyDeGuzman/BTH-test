<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoriesByEn = Category::pluck('id', 'name_en');

        if ($categoriesByEn->isEmpty()) {
            return;
        }

        foreach ($this->catalog() as $index => $item) {
            $categoryId = $categoriesByEn[$item['category']] ?? null;
            if (! $categoryId) {
                continue;
            }

            // loremflickr serves tag-matched Flickr photos. 'lock' makes the
            // returned image deterministic per product, so reseeding doesn't
            // shuffle the catalog thumbnails.
            $keyword = $item['image_keyword'];
            $lock = $index + 1;
            $imageUrl = "https://loremflickr.com/600/450/{$keyword}?lock={$lock}";

            Product::updateOrCreate(
                ['name' => $item['name']],
                [
                    'name_en' => $item['name_en'],
                    'description' => $item['description'],
                    'description_en' => $item['description_en'],
                    'price' => $item['price'],
                    'image_url' => $imageUrl,
                    'category_id' => $categoryId,
                ],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            // Electronics
            [
                'category' => 'Electronics',
                'image_keyword' => 'headphones',
                'name' => 'Беспроводные наушники Sony WH-1000XM5',
                'name_en' => 'Sony WH-1000XM5 Wireless Headphones',
                'description' => 'Премиальные наушники с шумоподавлением, до 30 часов работы и поддержкой LDAC.',
                'description_en' => 'Premium noise-cancelling headphones with up to 30 hours of battery life and LDAC support.',
                'price' => 349.99,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'iphone',
                'name' => 'Apple iPhone 15 Pro, 256 ГБ',
                'name_en' => 'Apple iPhone 15 Pro, 256 GB',
                'description' => 'Флагман с титановым корпусом, процессором A17 Pro и системой камер Pro.',
                'description_en' => 'Flagship smartphone with titanium body, A17 Pro chip and Pro camera system.',
                'price' => 1099.00,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'macbook',
                'name' => 'MacBook Air 13" M3, 16 ГБ / 512 ГБ',
                'name_en' => 'MacBook Air 13" M3, 16 GB / 512 GB',
                'description' => 'Ультратонкий ноутбук на чипе Apple M3 с тихой пассивной системой охлаждения.',
                'description_en' => 'Ultra-thin laptop powered by the Apple M3 chip with silent fanless cooling.',
                'price' => 1499.00,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'television',
                'name' => 'Телевизор Samsung 55" 4K QLED',
                'name_en' => 'Samsung 55" 4K QLED Smart TV',
                'description' => 'Яркий QLED-экран с поддержкой HDR10+, 120 Гц и голосовым помощником.',
                'description_en' => 'Bright QLED panel with HDR10+ support, 120 Hz and a built-in voice assistant.',
                'price' => 799.00,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'computer,mouse',
                'name' => 'Мышь Logitech MX Master 3S',
                'name_en' => 'Logitech MX Master 3S Mouse',
                'description' => 'Эргономичная беспроводная мышь с тихими кликами и MagSpeed-колесом.',
                'description_en' => 'Ergonomic wireless mouse with quiet clicks and the MagSpeed scroll wheel.',
                'price' => 99.99,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'ipad,tablet',
                'name' => 'Apple iPad Air 11"',
                'name_en' => 'Apple iPad Air 11"',
                'description' => 'Лёгкий планшет с экраном Liquid Retina и поддержкой Apple Pencil Pro.',
                'description_en' => 'Lightweight tablet with a Liquid Retina display and Apple Pencil Pro support.',
                'price' => 599.00,
            ],
            [
                'category' => 'Electronics',
                'image_keyword' => 'charger,battery',
                'name' => 'Повербанк Anker 737, 24 000 мА·ч',
                'name_en' => 'Anker 737 Power Bank, 24,000 mAh',
                'description' => 'Ёмкий повербанк с двумя USB-C и быстрой зарядкой мощностью 140 Вт.',
                'description_en' => 'High-capacity power bank with two USB-C ports and 140 W fast charging.',
                'price' => 149.99,
            ],

            // Books
            [
                'category' => 'Books',
                'image_keyword' => 'book,reading',
                'name' => '«Чистый код», Роберт Мартин',
                'name_en' => 'Clean Code, Robert C. Martin',
                'description' => 'Классика о том, как писать поддерживаемый и понятный код.',
                'description_en' => 'A classic on writing maintainable and understandable code.',
                'price' => 34.99,
            ],
            [
                'category' => 'Books',
                'image_keyword' => 'book,desk',
                'name' => '«Атомные привычки», Джеймс Клир',
                'name_en' => 'Atomic Habits, James Clear',
                'description' => 'Простая система изменения привычек шаг за шагом.',
                'description_en' => 'A simple framework for changing habits one small step at a time.',
                'price' => 16.99,
            ],
            [
                'category' => 'Books',
                'image_keyword' => 'bookshelf',
                'name' => '«Программист-прагматик»',
                'name_en' => 'The Pragmatic Programmer',
                'description' => 'Классическая книга для инженеров о мастерстве и ремесле.',
                'description_en' => 'A timeless book for engineers about craftsmanship and mastery.',
                'price' => 39.99,
            ],
            [
                'category' => 'Books',
                'image_keyword' => 'novel,library',
                'name' => '«1984», Джордж Оруэлл',
                'name_en' => '1984, George Orwell',
                'description' => 'Дистопия о тоталитаризме, наблюдении и правде.',
                'description_en' => 'A dystopian novel about totalitarianism, surveillance and truth.',
                'price' => 12.99,
            ],
            [
                'category' => 'Books',
                'image_keyword' => 'book,coffee',
                'name' => '«Sapiens. Краткая история человечества»',
                'name_en' => 'Sapiens: A Brief History of Humankind',
                'description' => 'Увлекательный рассказ о том, как Homo sapiens захватил планету.',
                'description_en' => 'A captivating account of how Homo sapiens came to dominate the planet.',
                'price' => 19.99,
            ],
            [
                'category' => 'Books',
                'image_keyword' => 'books,stack',
                'name' => 'Гарри Поттер — комплект из 7 книг',
                'name_en' => 'Harry Potter 7-Book Box Set',
                'description' => 'Полная коллекция романов о волшебном мире Хогвартса.',
                'description_en' => 'The complete collection of novels set in the wizarding world of Hogwarts.',
                'price' => 89.99,
            ],

            // Clothing
            [
                'category' => 'Clothing',
                'image_keyword' => 'jeans,denim',
                'name' => 'Джинсы Levi\'s 501 Original',
                'name_en' => "Levi's 501 Original Jeans",
                'description' => 'Классические прямые джинсы из плотного хлопкового денима.',
                'description_en' => 'Classic straight-leg jeans in heavyweight cotton denim.',
                'price' => 79.99,
            ],
            [
                'category' => 'Clothing',
                'image_keyword' => 'tshirt,athletic',
                'name' => 'Футболка Nike Dri-FIT',
                'name_en' => 'Nike Dri-FIT T-Shirt',
                'description' => 'Лёгкая спортивная футболка с влагоотводящей тканью.',
                'description_en' => 'Lightweight athletic t-shirt with moisture-wicking fabric.',
                'price' => 29.99,
            ],
            [
                'category' => 'Clothing',
                'image_keyword' => 'jacket,fleece',
                'name' => 'Флисовая куртка Patagonia Better Sweater',
                'name_en' => 'Patagonia Better Sweater Fleece Jacket',
                'description' => 'Тёплая флисовая куртка из переработанного полиэстера.',
                'description_en' => 'Warm fleece jacket made from recycled polyester.',
                'price' => 149.00,
            ],
            [
                'category' => 'Clothing',
                'image_keyword' => 'sneakers,shoes',
                'name' => 'Кроссовки Adidas Ultraboost 23',
                'name_en' => 'Adidas Ultraboost 23 Running Shoes',
                'description' => 'Беговые кроссовки с энергичной подошвой Boost и сеткой Primeknit.',
                'description_en' => 'Running shoes with a responsive Boost midsole and Primeknit upper.',
                'price' => 179.99,
            ],
            [
                'category' => 'Clothing',
                'image_keyword' => 'sunglasses',
                'name' => 'Солнцезащитные очки Ray-Ban Aviator',
                'name_en' => 'Ray-Ban Aviator Sunglasses',
                'description' => 'Классические очки-авиаторы в металлической оправе, UV-защита 100%.',
                'description_en' => 'Classic aviator sunglasses with a metal frame and 100% UV protection.',
                'price' => 179.00,
            ],
            [
                'category' => 'Clothing',
                'image_keyword' => 'shirt,cotton',
                'name' => 'Рубашка Uniqlo Oxford',
                'name_en' => 'Uniqlo Oxford Shirt',
                'description' => 'Повседневная рубашка из хлопка оксфорд, на пуговицах.',
                'description_en' => 'Everyday button-down shirt made from cotton oxford fabric.',
                'price' => 39.90,
            ],

            // Home & Kitchen
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'vacuum,cleaner',
                'name' => 'Пылесос Dyson V15 Detect',
                'name_en' => 'Dyson V15 Detect Cordless Vacuum',
                'description' => 'Беспроводной пылесос с лазерной подсветкой пыли и мощной тягой.',
                'description_en' => 'Cordless vacuum with laser dust detection and powerful suction.',
                'price' => 749.99,
            ],
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'kitchen,cooking',
                'name' => 'Мультиварка Instant Pot Duo, 6 л',
                'name_en' => 'Instant Pot Duo 6-Quart Multi-Cooker',
                'description' => 'Скороварка 7-в-1: тушение, рис, йогурт, стерилизация и многое другое.',
                'description_en' => '7-in-1 pressure cooker: slow cooking, rice, yogurt, sterilising and more.',
                'price' => 99.99,
            ],
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'mixer,baking',
                'name' => 'Миксер KitchenAid Artisan',
                'name_en' => 'KitchenAid Artisan Stand Mixer',
                'description' => 'Планетарный миксер объёмом 4.8 л с металлическим корпусом.',
                'description_en' => '4.8 L planetary stand mixer with a durable metal body.',
                'price' => 449.00,
            ],
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'fryer,kitchen',
                'name' => 'Аэрогриль Philips Airfryer XXL',
                'name_en' => 'Philips Airfryer XXL',
                'description' => 'Большая аэрогриль-фритюрница для здоровой готовки без масла.',
                'description_en' => 'Extra-large air fryer for healthy cooking with little or no oil.',
                'price' => 299.99,
            ],
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'blender,smoothie',
                'name' => 'Блендер Ninja Professional Plus',
                'name_en' => 'Ninja Professional Plus Blender',
                'description' => 'Мощный стационарный блендер на 1400 Вт с функцией колки льда.',
                'description_en' => 'Powerful 1400 W countertop blender with Total Crushing technology.',
                'price' => 129.99,
            ],
            [
                'category' => 'Home & Kitchen',
                'image_keyword' => 'cookware,pot',
                'name' => 'Чугунная кастрюля Le Creuset, 5 л',
                'name_en' => 'Le Creuset 5.3 L Dutch Oven',
                'description' => 'Эмалированная чугунная кастрюля, подходит для всех типов плит.',
                'description_en' => 'Enamelled cast-iron Dutch oven, works on every cooktop type.',
                'price' => 379.99,
            ],

            // Sports & Outdoors
            [
                'category' => 'Sports & Outdoors',
                'image_keyword' => 'thermos,mug',
                'name' => 'Термокружка Yeti Rambler, 590 мл',
                'name_en' => 'Yeti Rambler 20 oz Tumbler',
                'description' => 'Стальная термокружка с двойной изоляцией, держит тепло до 12 часов.',
                'description_en' => 'Stainless steel tumbler with double-wall insulation — stays hot for 12 hours.',
                'price' => 34.99,
            ],
            [
                'category' => 'Sports & Outdoors',
                'image_keyword' => 'backpack,hiking',
                'name' => 'Туристический рюкзак Osprey Atmos AG 65',
                'name_en' => 'Osprey Atmos AG 65 Backpack',
                'description' => 'Походный рюкзак с подвеской Anti-Gravity и регулируемой поясной системой.',
                'description_en' => 'Hiking backpack with the Anti-Gravity suspension and adjustable hipbelt.',
                'price' => 289.99,
            ],
            [
                'category' => 'Sports & Outdoors',
                'image_keyword' => 'tennis,racket',
                'name' => 'Теннисная ракетка Wilson Pro Staff',
                'name_en' => 'Wilson Pro Staff Tennis Racket',
                'description' => 'Профессиональная графитовая ракетка с идеальным балансом.',
                'description_en' => 'Pro-level graphite racket with a balanced swing weight.',
                'price' => 199.00,
            ],
            [
                'category' => 'Sports & Outdoors',
                'image_keyword' => 'smartwatch,fitness',
                'name' => 'Спортивные часы Garmin Forerunner 265',
                'name_en' => 'Garmin Forerunner 265',
                'description' => 'GPS-часы для бега с AMOLED-экраном и мульти-частотным позиционированием.',
                'description_en' => 'GPS running watch with an AMOLED display and multi-band positioning.',
                'price' => 449.99,
            ],
            [
                'category' => 'Sports & Outdoors',
                'image_keyword' => 'massage,spa',
                'name' => 'Перкуссионный массажёр Theragun Prime',
                'name_en' => 'Theragun Prime Massage Gun',
                'description' => 'Массажёр для восстановления мышц с пятью скоростями и тихим мотором.',
                'description_en' => 'Muscle recovery percussion massager with five speeds and a quiet motor.',
                'price' => 299.00,
            ],

            // Toys & Games
            [
                'category' => 'Toys & Games',
                'image_keyword' => 'lego,toys',
                'name' => 'LEGO Star Wars: Тысячелетний сокол',
                'name_en' => 'LEGO Star Wars Millennium Falcon',
                'description' => 'Детализированный конструктор для коллекционеров, 1 353 детали.',
                'description_en' => 'Detailed collector set with 1,353 pieces.',
                'price' => 169.99,
            ],
            [
                'category' => 'Toys & Games',
                'image_keyword' => 'boardgame,strategy',
                'name' => 'Настольная игра «Колонизаторы»',
                'name_en' => 'Catan Board Game',
                'description' => 'Классическая стратегия про освоение острова, от 3 до 4 игроков.',
                'description_en' => 'Classic strategy board game about settling an island, 3–4 players.',
                'price' => 54.99,
            ],
            [
                'category' => 'Toys & Games',
                'image_keyword' => 'nintendo,console',
                'name' => 'Nintendo Switch OLED',
                'name_en' => 'Nintendo Switch OLED',
                'description' => 'Портативная консоль с ярким 7" OLED-экраном и улучшенным звуком.',
                'description_en' => 'Handheld console with a vivid 7" OLED screen and improved audio.',
                'price' => 349.99,
            ],
            [
                'category' => 'Toys & Games',
                'image_keyword' => 'monopoly,boardgame',
                'name' => 'Монополия — классика',
                'name_en' => 'Monopoly Classic Board Game',
                'description' => 'Легендарная семейная настольная игра про недвижимость.',
                'description_en' => 'The legendary family board game about buying and trading real estate.',
                'price' => 24.99,
            ],

            // Beauty
            [
                'category' => 'Beauty',
                'image_keyword' => 'skincare,serum',
                'name' => 'Сыворотка The Ordinary Hyaluronic Acid 2%',
                'name_en' => 'The Ordinary Hyaluronic Acid 2% + B5',
                'description' => 'Увлажняющая сыворотка с гиалуроновой кислотой и витамином B5.',
                'description_en' => 'Hydrating serum with hyaluronic acid and vitamin B5.',
                'price' => 8.90,
            ],
            [
                'category' => 'Beauty',
                'image_keyword' => 'sunscreen,beach',
                'name' => 'Санскрин La Roche-Posay Anthelios SPF 50',
                'name_en' => 'La Roche-Posay Anthelios Sunscreen SPF 50',
                'description' => 'Солнцезащитный крем для лица с широким спектром защиты.',
                'description_en' => 'Broad-spectrum facial sunscreen with a lightweight feel.',
                'price' => 35.00,
            ],
            [
                'category' => 'Beauty',
                'image_keyword' => 'hairdryer,salon',
                'name' => 'Фен Dyson Supersonic',
                'name_en' => 'Dyson Supersonic Hair Dryer',
                'description' => 'Фен с интеллектуальным контролем температуры и насадками Magnetic.',
                'description_en' => 'Hair dryer with intelligent heat control and magnetic attachments.',
                'price' => 429.00,
            ],
            [
                'category' => 'Beauty',
                'image_keyword' => 'cosmetics,makeup',
                'name' => 'Тональный крем Fenty Beauty Pro Filt\'r',
                'name_en' => "Fenty Beauty Pro Filt'r Foundation",
                'description' => 'Матовый тональный крем с 40 оттенками.',
                'description_en' => 'Soft-matte foundation available in 40 shades.',
                'price' => 39.00,
            ],

            // Automotive
            [
                'category' => 'Automotive',
                'image_keyword' => 'tire,wheel',
                'name' => 'Летние шины Michelin Pilot Sport 4',
                'name_en' => 'Michelin Pilot Sport 4 Tires',
                'description' => 'Производительные летние шины с отличным сцеплением.',
                'description_en' => 'Performance summer tires with excellent wet and dry grip.',
                'price' => 189.99,
            ],
            [
                'category' => 'Automotive',
                'image_keyword' => 'windshield,car',
                'name' => 'Стеклоочистители Bosch Aerotwin, пара',
                'name_en' => 'Bosch Aerotwin Wiper Blades (pair)',
                'description' => 'Бескаркасные щётки с плотным прилеганием к стеклу.',
                'description_en' => 'Frameless wiper blades that hug the windshield evenly.',
                'price' => 34.99,
            ],
            [
                'category' => 'Automotive',
                'image_keyword' => 'suv,roof',
                'name' => 'Багажный бокс Thule Motion XT XL',
                'name_en' => 'Thule Motion XT XL Roof Cargo Box',
                'description' => 'Аэродинамический багажный бокс на крышу объёмом 500 л.',
                'description_en' => 'Aerodynamic 500 L roof cargo box for long trips.',
                'price' => 899.00,
            ],
            [
                'category' => 'Automotive',
                'image_keyword' => 'carwash,detailing',
                'name' => 'Набор для мойки авто Chemical Guys',
                'name_en' => 'Chemical Guys Car Wash Kit',
                'description' => 'Полный набор для мойки и ухода: шампунь, микрофибра, аппликаторы.',
                'description_en' => 'Complete wash and detailing kit: shampoo, microfibers, applicators.',
                'price' => 79.99,
            ],
        ];
    }
}
