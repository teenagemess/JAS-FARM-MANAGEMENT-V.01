<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shelter; // (1) Impor Shelter

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // (2) Buat 1 User Admin (jika belum ada)
        $user = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            // password default adalah 'password'
        ]);

        // (3) Buat 5 data Kandang (Shelter) menggunakan user_id di atas
        Shelter::factory(5)->create([
            'user_id' => $user->id
        ]);

    }
}
