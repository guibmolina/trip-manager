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
            'name' => 'James',
            'email' => 'james@example.com',
            'password' => Hash::make('password'),
            'type' => 'SOLICITOR',
        ]);

        DB::table('users')->insert([
            'name' => 'Mary',
            'email' => 'mary@example.com',
            'password' => Hash::make('password'),
            'type' => 'SOLICITOR',
        ]);

        DB::table('users')->insert([
            'name' => 'Mark',
            'email' => 'mark@example.com',
            'password' => Hash::make('password'),
            'type' => 'MANAGER',
        ]);
    }
}
