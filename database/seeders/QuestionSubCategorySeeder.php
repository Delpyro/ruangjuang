<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'TIU' => ['Verbal - Analogi', 'Verbal - Silogisme', 'Figural - Serial', 'Numerik - Berhitung'],
            'TWK' => ['Pilar Negara', 'Bela Negara', 'Integritas', 'Nasionalisme'],
            'TKP' => ['Pelayanan Publik', 'Sosial Budaya', 'Jejaring Kerja'],
        ];
        
        $categoriesInDb = DB::table('question_categories')->pluck('id', 'name')->toArray();
        $subCategoriesToInsert = [];

        foreach ($categories as $key => $subCats) {
            $categoryName = array_key_exists("Tes {$key}", $categoriesInDb) ? "Tes {$key}" : (array_key_exists("Tes $key", $categoriesInDb) ? "Tes $key" : "Tes $key");
            
            // Mencari ID Kategori
            $categoryId = null;
            foreach ($categoriesInDb as $dbName => $id) {
                if (str_contains($dbName, $key)) {
                    $categoryId = $id;
                    break;
                }
            }

            if ($categoryId) {
                foreach ($subCats as $subCatName) {
                    $subCategoriesToInsert[] = [
                        'name' => $subCatName,
                        'question_category_id' => $categoryId,
                        'is_active' => 1,
                        'created_at' => now(), 'updated_at' => now(), 'deleted_at' => null,
                    ];
                }
            }
        }

        DB::table('question_sub_categories')->insert($subCategoriesToInsert);
    }
}