<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListParameter extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('tbl_listparameter')->insert([

            [
                'parameter' => 'pH',
                'script' => 'pH.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'TSS',
                'script' => 'TSS.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'Debit',
                'script' => 'Debit.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'COD',
                'script' => 'COD.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'NH3-N',
                'script' => 'NH3-N.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'Rainfall',
                'script' => 'Rainfall.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'Depth',
                'script' => 'Depth.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'Temperature',
                'script' => 'temperature.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'parameter' => 'Water Pressure',
                'script' => 'WaterPressure.py',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
