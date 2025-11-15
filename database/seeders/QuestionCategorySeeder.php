<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('question_categories')->insert([
            [
                'name' => 'Tes Intelegensia Umum (TIU)',
                'passing_grade' => 80.00,
                'is_active' => 1,
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
            ],
            [
                'name' => 'Tes Wawasan Kebangsaan (TWK)',
                'passing_grade' => 65.00,
                'is_active' => 1,
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
            ],
            [
                'name' => 'Tes Karakteristik Pribadi (TKP)',
                'passing_grade' => 166.00,
                'is_active' => 1,
                'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
            ],
        ]);
    }
}