<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Diego',
            'email' => 'diego.gomezd@edu.uah.es',
            'password' => Hash::make('teL8eevUy4n3Uvy'),
        ],[
            'name' => 'Antonio J. de Vicente',
            'email' => 'antonio.vicente@uah.es',
            'password' => '$2y$10$UJeL//zXEY/l3NsmlWEhU.JCHWjKGpUHIo6.MZeF4NWAVOdd/M1XS',
        ]);
    }
}
