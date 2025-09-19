<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@library.com',
            'password' => bcrypt('password'), // default password
            'role' => 'admin',
        ]);

        // Create a sample faculty user
        User::create([
            'name' => 'Faculty User',
            'email' => 'faculty@library.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
        ]);

        // Create a sample student user
        User::create([
            'name' => 'Student User',
            'email' => 'student@library.com',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
    }
}
