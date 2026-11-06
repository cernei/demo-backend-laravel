<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'name' => 'admin',
            'user_id' => 1,
            'permissions' => json_encode(array_keys(Config::get('auth.permissions')))
        ]);
        DB::table('roles')->insert([
            'name' => 'Warehouse worker',
            'user_id' => 1,
            'permissions' => json_encode(['stock_items.view', 'stock_items.create', 'stock_items.update', 'stock_items.delete'])
        ]);

        DB::table('admins')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
        DB::table('admins')->insert([
            'name' => 'Guest',
            'email' => 'guest@gmail.com',
            'role_id' => 2,
            'password' => bcrypt('12345678'),
        ]);

        DB::table('attributes')->insert([
            ['name' => 'Length'],
            ['name' => 'Width'],
            ['name' => 'Height'],
            ['name' => 'Weight'],
            ['name' => 'Material'],
        ]);

        $products = json_decode(
            file_get_contents(__DIR__ . '/products.json'),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $categories = collect($products)
            ->pluck('category')
            ->unique()
            ->values();

        foreach ($categories as $category) {
            DB::table('categories')->insert(['name' => $category]);
        }

        $categoryIds = DB::table('categories')
            ->whereIn('name', $categories)
            ->pluck('id', 'name');

        $productsBatch = collect($products)
            ->map(fn (array $product) => [
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => rand(1, 10),
                'category_id' => $categoryIds[$product['category']],
                'admin_id' => 1,
            ])
            ->all();

        DB::table('products')->insert($productsBatch);

        foreach (['A', 'B', 'C', 'D'] as $letter) {
            for ($i = 0; $i <= 9; $i++) {
                DB::table('stock_locations')->insert(['code' => $letter . $i]);
            }
        }

        for ($i = 1; $i <= 100; $i++) {
            DB::table('stock_items')->insert(['product_id' => $i, 'location_id' => rand(1,40), 'quantity' => 100]);
        }

        $faker = Factory::create();

        for ($i = 1; $i <= 100; $i++) {
            $orderId = DB::table('orders')->insertGetId([
                'email' => $faker->email(),
                'total_amount' => 0
            ]);
            $total_amount = 0;
            $productIds = range(1, 100);
            shuffle($productIds);

            $productIds = array_slice($productIds, 0, 5);
            foreach ($productIds as $productId) {
                $quantity = rand(1, 3);
                $price = rand(1, 10);
                DB::table('order_items')->insert([
                    'order_id'=> $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price
                ]);
                $total_amount += $quantity * $price;
            }
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'total_amount' => $total_amount
                ]);
        }
    }
}
