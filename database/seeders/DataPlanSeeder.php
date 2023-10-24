<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('data_plans')->insert([
            [
             'name' => '10 Gb',
             'price' => 25000,
             'vendor_card_id' => 1,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '80 Gb',
             'price' => 190000,
             'vendor_card_id' => 2,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '25 Gb',
             'price' => 75000,
             'vendor_card_id' => 3,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '100 Gb',
             'price' => 200000,
             'vendor_card_id' => 3,
             'created_at' => now(),
             'updated_at' => now(),
            ],
        ]);
    }
}
