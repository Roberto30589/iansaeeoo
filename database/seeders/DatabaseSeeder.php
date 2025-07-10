<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'roberto30589@gmail.com',
            'password' => bcrypt('password'), // Use a secure password
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null, // Ensure the user is not soft deleted
        ]);
    }
}
