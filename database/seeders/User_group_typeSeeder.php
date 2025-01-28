<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class User_group_typeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('user_group_types')->insert([

            [
                'user_id' => 1,
                'group_id' => 1,
                'master_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 2,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 3,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 4,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 5,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 6,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 7,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 8,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 9,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 10,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 11,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'user_id' => 12,
                'group_id' => 2,
                'master_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
        ]);

    }
}
