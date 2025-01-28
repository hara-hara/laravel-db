<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //\App\Models\User::factory(10)->create(); //usersテーブルのサンプルレコードを作成

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        $this->call([
            //Master_worktime_typesSeeder::class,
            //UsersSeeder::class,
            WorktypesSeeder::class,
            //WorktimesSeeder::class,
            //User_group_typeSeeder::class,
        ]);
        
    }
}
