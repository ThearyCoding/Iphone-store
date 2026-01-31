<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpec;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);

        // Create Brands
        $apple   = Category::create(['name' => 'Apple']);
        $samsung = Category::create(['name' => 'Samsung']);
        $huawei  = Category::create(['name' => 'Huawei']);

        // Helper function
        $create = function ($brand, $name, $color, $storage, $price, $discount = null, $imageUrl = null) use ($apple, $samsung, $huawei) {

            $brandId = $brand === 'Apple'
                ? $apple->id
                : ($brand === 'Samsung' ? $samsung->id : $huawei->id);

            $product = Product::create([
                'name'           => $name,
                'brand_id'       => $brandId,
                'description'    => "$name - $color, $storage",
                'price'          => $price,
                'discount_price' => $discount,
                'stock'          => 99,
                'image'          => $imageUrl,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            ProductSpec::create([
                'product_id'  => $product->id,
                'ram'         => $brand === 'Apple' ? '8GB' : '12GB',
                'storage'     => $storage,
                'color'       => $color,
                'battery'     => '5000mAh',
                'chipset'     => $brand === 'Apple' ? 'A18 / A17' : 'Snapdragon 8 Gen 3',
                'screen_size' => '6.7"',
            ]);
        };

        // -----------------------------------------------------
        // 📱 Apple iPhones (Using your direct image URLs)
        // -----------------------------------------------------

        $create('Apple', 'iPhone 17 Pro', 'Natural Titanium', '256GB', 1299, 1199,
            'https://www.apple.com/v/iphone-17-pro/d/images/overview/contrast/iphone_17_pro__dwccrdina7qu_large_2x.jpg'
        );

        $create('Apple', 'iPhone Air', 'Blue', '128GB', 899, 829,
            'https://www.apple.com/v/iphone-17-pro/d/images/overview/contrast/iphone_air__fe2gdmh5u5qy_large_2x.jpg'
        );

        $create('Apple', 'iPhone 17', 'Black', '128GB', 799, 749,
            'https://www.apple.com/v/iphone-17/d/images/overview/contrast/iphone_17__ck7zzemcw37m_large_2x.jpg'
        );

        $create('Apple', 'iPhone 16e', 'Yellow', '128GB', 599, 549,
            'https://www.apple.com/v/iphone-16e/f/images/overview/contrast/iphone_16e__dxha4illuf2a_xlarge_2x.jpg'
        );

        $create('Apple', 'iPhone 16', 'Green', '128GB', 699, 649,
            'https://www.apple.com/v/iphone-16e/f/images/overview/contrast/iphone_16__dhi77ut7vgcy_xlarge_2x.jpg'
        );


        // -----------------------------------------------------
        // 📱 Huawei (Full-resolution GSM Arena images)
        // -----------------------------------------------------

        $create('Huawei', 'Huawei Pura 80 Pro', 'Black', '256GB', 999, 949,
            'https://fdn2.gsmarena.com/vv/bigpic/huawei-pura80-pro.jpg'
        );

        $create('Huawei', 'Huawei Pura 80', 'Blue', '256GB', 799, 749,
            'https://fdn2.gsmarena.com/vv/bigpic/huawei-pura80.jpg'
        );

        $create('Huawei', 'Huawei Pura 80 Ultra', 'White', '512GB', 1499, 1399,
            'https://fdn2.gsmarena.com/vv/bigpic/huawei-pura80-ultra-.jpg'
        );


        // -----------------------------------------------------
        // 📱 Samsung (Direct high-resolution URLs)
        // -----------------------------------------------------

        $create('Samsung', 'Galaxy S25 Ultra', 'Black', '256GB', 1399, 1299,
            'https://images.samsung.com/is/image/samsung/p6pim/us/2501/gallery/us-galaxy-s25-s938-sm-s938uzkaxaa-544887989'
        );

        $create('Samsung', 'Galaxy Z Fold7', 'Silver', '512GB', 1799, 1699,
            'https://images.samsung.com/is/image/samsung/p6pim/us/f2507/gallery/us-galaxy-z-fold7-f966-sm-f966ulgaxaa-547827985'
        );

        $create('Samsung', 'Galaxy S25', 'Blue', '256GB', 999, 949,
            'https://images.samsung.com/is/image/samsung/p6pim/us/sm-s931udbaxaa/gallery/us-galaxy-s25-s931-551170-sm-s931udbaxaa-547068490'
        );

        $create('Samsung', 'Galaxy S25 Edge', 'Silver', '256GB', 1099, 1049,
            'https://images.samsung.com/is/image/samsung/p6pim/us/sm-s937uzsaxaa/gallery/us-galaxy-s25-edge-s937-sm-s937uzsaxaa-547158601'
        );

        $this->command->info('✅ All products successfully seeded with image URLs!');
    }
}
