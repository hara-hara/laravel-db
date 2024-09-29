<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('groups')->insert([

            [
                'group_name' => 'うさぎ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],
            [
                'group_name' => 'かめ',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ],

        ]);
    }
}
