<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MoreRecords extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $pass = bcrypt('12345678');

        for ($i = 0; $i < 1000; $i++) {
            $batch = [];
            for ($k = 0; $k < 100; $k++) {
                $batch[] = [
                    'name' =>  $faker->name(),
                    'address' =>  $faker->address(),
                    'phone' =>  $faker->phoneNumber(),
                    'email' => $faker->unique()->email(),
                    'role_id' => 1,
                    'password' => $pass,
                ];
            }
            DB::table('users')->insert($batch);

        }
        for ($i = 0; $i < 1000; $i++) {
            $batch = [];
            for ($k = 0; $k < rand(1, 10); $k++) {
                $batch[] = [
                    'content' => $faker->realText(),
                    'user_id' => rand(1, 10000),
                    'category_id' => rand(1, 100),
                ];
            }
            DB::table('posts')->insert($batch);
        }
    }
}
