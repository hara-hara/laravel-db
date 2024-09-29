<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Master_worktime_typesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('master_worktime_types')->insert([
            [
				'able_worktime_start' => '8:00',
				'able_worktime_end' => '20:00',
				'basic_worktime_start' => '10:00',
				'basic_worktime_end' => '15:00',
				'lunch_break_times'=> '1',
				'dayoff_times'=> '4',
				'morningoff_times'=> '2',
				'aftenoonoff_times'=> '2',
            ],            
			[
				'able_worktime_start' => '8:00',
				'able_worktime_end' => '20:00',
				'basic_worktime_start' => '9:00',
				'basic_worktime_end' => '17:00',
				'lunch_break_times'=> '1',
				'dayoff_times'=> '7',
				'morningoff_times'=> '3',
				'aftenoonoff_times'=> '4',
            ],

        ]);
	}
}
