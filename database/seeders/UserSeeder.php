<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'superadmin',
            'email' => 'duta.nugraha82@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'superadmin',
            'user_code' => 'superadmin'
        ]);
    }
}
