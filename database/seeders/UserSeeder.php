<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Ganti dari DB ke Model User
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str; // Kita butuh Str untuk membuat slug

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Buat User Admin (Sesuai aslinya, tapi pakai Model)
        // Menggunakan User::create() akan otomatis mengisi created_at/updated_at
        User::create([
            'name' => 'Admin Utama',
            'slug' => 'admin-utama',
            'email' => 'admin@ruangjuang.com',
            'phone_number' => '081234567890',
            'role' => 'admin',
            'status' => 'active',
            'is_active' => 1,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => '127.0.0.1',
        ]);

        // 2. Buat User Budi (Sesuai aslinya, tapi pakai Model)
        User::create([
            'name' => 'Budi Santoso',
            'slug' => 'budi-santoso',
            'email' => 'budi.user@mail.com',
            'phone_number' => '082123456789',
            'role' => 'user',
            'status' => 'active',
            'is_active' => 1,
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'last_login_at' => now(),
            'last_login_ip' => '192.168.1.10',
        ]);

        // 3. Tambahkan 30 User palsu lainnya untuk ReviewSeeder
        // Kita akan menggunakan factory untuk ini
        // Pastikan Anda sudah punya UserFactory.php (biasanya ada bawaan Laravel)
        for ($i = 0; $i < 30; $i++) {
            $name = fake()->name(); // Gunakan global helper `fake()`
            
            // `User::factory()->create()` akan membuat user baru
            // dan menggunakan data palsu dari factory (spt email, password)
            // Kita menimpa (override) beberapa nilai agar sesuai kebutuhan
            User::factory()->create([
                'name' => $name,
                'slug' => Str::slug($name), // Buat slug dari nama
                'phone_number' => fake()->e164PhoneNumber(), // Buat no HP palsu
                'role' => 'user',
                'status' => 'active',
                'is_active' => 1,
                'last_login_at' => fake()->dateTimeThisYear(),
                'last_login_ip' => fake()->ipv4(),
                // 'email', 'password', 'email_verified_at' 
                // akan diurus oleh factory bawaan Laravel
            ]);
        }
    }
}