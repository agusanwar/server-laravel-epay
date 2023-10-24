<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PulsaPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pulsa_plans')->insert([
            [
             'name' => '10000',
             'price' => 11500,
             'vendor_card_id' => 1,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '20000',
             'price' => 21000,
             'vendor_card_id' => 2,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '25000',
             'price' => 26500,
             'vendor_card_id' => 3,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '50000',
             'price' => 501000,
             'vendor_card_id' => 3,
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
             'name' => '100000',
             'price' => 99000,
             'vendor_card_id' => 3,
             'created_at' => now(),
             'updated_at' => now(),
            ],
        ]);
    }
}
