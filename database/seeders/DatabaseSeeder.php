<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shelter; // (1) Impor Shelter
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT AKUN ADMIN (Sesuai permintaan Anda)
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'], // Kunci pencarian agar tidak duplikat
            [
                'name' => 'Admin',
                'password' => Hash::make('password'), // password default adalah 'password'
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Kantor Pusat JAS Farm',
            ]
        );

        // 2. BUAT AKUN MITRA (PARTNER) - Untuk tes fitur Mitra
        $mitra = User::firstOrCreate(
            ['email' => 'mitra@example.com'],
            [
                'name' => 'Budi (Mitra Plasma)',
                'password' => Hash::make('password'),
                'role' => 'mitra',
                'phone' => '089876543210',
                'address' => 'Desa Suka Maju, Blok C',
            ]
        );

        // 3. BUAT AKUN STAF (PEGAWAI) - Untuk tes fitur Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'Ujang (Anak Kandang)',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'phone' => '081122334455',
                'address' => 'Mess Karyawan',
            ]
        );

        // 4. BUAT DATA KANDANG DUMMY (Milik Admin)
        // Kita buat kandang spesifik agar namanya jelas
        Shelter::firstOrCreate(
            ['name' => 'Kandang Utama A'],
            ['user_id' => $admin->id, 'capacity' => 50, 'description' => 'Kandang pembibitan utama']
        );

        Shelter::firstOrCreate(
            ['name' => 'Kandang Isolasi B'],
            ['user_id' => $admin->id, 'capacity' => 10, 'description' => 'Untuk domba sakit atau karantina']
        );

        // Buat 3 kandang tambahan acak menggunakan Factory
        Shelter::factory()->create([
            'user_id' => $admin->id
        ]);
        Shelter::factory()->create([
            'user_id' => $mitra->id
        ]);
        Shelter::factory()->create([
            'user_id' => $staff->id
        ]);

    }
}
