<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VendorCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor_cards')->insert([
            [
             'name' => 'Telkomsel',
             'status' => 'active',
             'thumbnail' => 'img_telkomsel.png',
             'created_at' => now(),
             'updated_at' => now(),
            ],
            [
            'name' => 'Indosat',
            'status' => 'active',
            'thumbnail' => 'img_indosat.png',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'Singtel',
            'status' => 'active',
            'thumbnail' => 'img_singtel.png',
            'created_at' => now(),
            'updated_at' => now(),
            ],
         ]);
    }
}
