<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@factory.com',
            'password' => Hash::make('password'),
            'role' => 'administrator',
        ]);

        User::create([
            'name' => 'Engineer User',
            'email' => 'engineer@factory.com',
            'password' => Hash::make('password'),
            'role' => 'engineer',
        ]);
    }
}
