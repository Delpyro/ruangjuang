<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateSeeder extends Seeder
{
    public function run(): void
    {
        // Menonaktifkan Foreign Key Check
        Schema::disableForeignKeyConstraints();

        // URUTAN PEMBERSIHAN FINAL (Anak ke Induk, termasuk tabel yang menyebabkan konflik)
        
        // Tabel paling bergantung (Jawaban Pengguna, jika ada)
        if (Schema::hasTable('users_answers')) {
             DB::table('users_answers')->truncate();
        }
       
        // Jawaban (Anak dari Questions, Induk dari users_answers)
        DB::table('answers')->truncate(); 
        
        // Soal (Induk Answers)
        DB::table('questions')->truncate(); 
        
        // Sub Kategori (Anak dari Kategori)
        DB::table('question_sub_categories')->truncate();
        
        // Induk Utama
        DB::table('question_categories')->truncate();
        DB::table('tryouts')->truncate();
        DB::table('users')->truncate(); 
        
        // Mengaktifkan kembali Foreign Key Check
        Schema::enableForeignKeyConstraints();
    }
}