<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->insert([
            [
                'name' => '原　明弘',
                'email' => 'hara@hara.ciao.jp',
                'email_verified_at' => null,
                'password' => '$2y$10$gMdx7cLjVIkVI4GKJw63d.BYpDd1aT7.Xr/k93Rm26Wc53N7LqwHC',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
