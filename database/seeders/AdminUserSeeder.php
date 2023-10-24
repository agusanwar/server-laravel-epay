<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admin_users')->insert([
            [ 
                 'name' => 'Anwar sha',
                 'email' => 'anwarsha@si.com',
                 'password' => bcrypt('anwahsha')
             ],
            [ 
                 'name' => 'Admin Epay',
                 'email' => 'adminepay@ai.com',
                 'password' => bcrypt('adminepay')
             ],
         ]);
    }
}
