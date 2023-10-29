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
            // [ 
            //      'name' => 'Anwar sha',
            //      'email' => 'anwarsha@ai.com',
            //      'password' => bcrypt('123456')
            //  ],
            // [ 
            //      'name' => 'Admin Epay',
            //      'email' => 'admin@gmail.com',
            //      'password' => bcrypt('123456')
            //  ],
            [ 
                 'name' => 'Admin 3',
                 'email' => 'adminepay@gmail.com',
                 'password' => bcrypt('123456')
             ],
         ]);
    }
}
