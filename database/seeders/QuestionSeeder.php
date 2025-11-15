<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $tryouts = DB::table('tryouts')->pluck('id');
        $subCategories = DB::table('question_sub_categories')->get();

        if ($tryouts->isEmpty() || $subCategories->isEmpty()) {
            $this->command->warn('Tryouts atau Subkategori kosong. Harap jalankan Seeder sebelumnya.');
            return;
        }

        $questions = [];
        $allocation = [
            'TWK' => 30, 'TIU' => 35, 'TKP' => 45,
        ];
        
        // Ambil data kategori dengan namanya untuk memudahkan relasi
        $categories = DB::table('question_categories')->get()->keyBy('name');

        foreach ($tryouts as $tryoutId) {
            $questionNumber = 1;

            foreach ($allocation as $key => $count) {
                $categoryName = "Tes {$key}";
                $category = $categories->first(fn($c) => str_contains($c->name, $key)); // Temukan kategori yang sesuai
                
                if (!$category) continue;

                $relevantSubCats = $subCategories->where('question_category_id', $category->id)->values(); 
                if ($relevantSubCats->isEmpty()) continue;
                
                $subCatIndex = 0;

                for ($i = 0; $i < $count; $i++) {
                    $subCat = $relevantSubCats->get($subCatIndex % $relevantSubCats->count());

                    $questions[] = [
                        'id_tryout' => $tryoutId,
                        'id_question_categories' => $category->id,
                        'id_question_sub_category' => $subCat->id,
                        'question' => "Soal {$key} No. {$questionNumber} untuk Tryout {$tryoutId}: Apa dasar negara Indonesia?",
                        'image' => null,
                        'explanation' => 'Penjelasan rinci untuk jawaban yang benar.',
                        'is_active' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ];

                    $subCatIndex++;
                    $questionNumber++;
                }
            }
        }

        DB::table('questions')->insert($questions);
    }
}