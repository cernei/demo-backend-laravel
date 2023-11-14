<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'password' => bcrypt('12345678'),
        ]);
        DB::table('roles')->insert([
            'name' => 'admin',
            'user_id' => 1,
            'permissions' => json_encode(['users.view', 'users.create', 'users.delete'])

        ]);
    }
}
