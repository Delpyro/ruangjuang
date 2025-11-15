<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // 1. Panggil TruncateSeeder untuk membersihkan semua data relasi
        $this->call(TruncateSeeder::class); 

        // 2. Panggil semua seeder data utama dalam urutan yang benar
        $this->call([
            // Data utama (harus ada dulu)
            UserSeeder::class, 
            TryoutSeeder::class, 
            
            // Data Soal (tergantung Tryout)
            QuestionCategorySeeder::class, 
            QuestionSubCategorySeeder::class,
            QuestionSeeder::class,
            AnswerSeeder::class,

            // Data Review (tergantung User & Tryout)
            ReviewSeeder::class, // <-- INI YANG DITAMBAHKAN
        ]);
    }
}