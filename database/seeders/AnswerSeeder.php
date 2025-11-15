<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection; // Tambahkan ini

class AnswerSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan Truncate dilakukan di TruncateSeeder.php!
        
        $questions = DB::table('questions')->select('id', 'id_question_categories')->get();
        $twk_tiu_cat_ids = DB::table('question_categories')->whereIn('name', ['Tes Wawasan Kebangsaan (TWK)', 'Tes Intelegensia Umum (TIU)'])->pluck('id')->toArray();
        
        if ($questions->isEmpty()) {
            $this->command->warn('Tabel questions kosong. Harap jalankan QuestionSeeder terlebih dahulu.');
            return;
        }

        $answers = [];
        $options = ['A', 'B', 'C', 'D', 'E'];

        foreach ($questions as $question) {
            $isTkp = !in_array($question->id_question_categories, $twk_tiu_cat_ids);
            
            if ($isTkp) {
                $points = [5, 4, 3, 2, 1];
                shuffle($points);
                $correctOption = null; 
            } else {
                $correctOption = fake()->randomElement($options);
            }

            foreach ($options as $index => $option) {
                $isCorrect = false;
                $currentPoints = 0;

                if ($isTkp) {
                    $currentPoints = $points[$index];
                } else {
                    if ($option === $correctOption) {
                        $currentPoints = 5;
                        $isCorrect = true;
                    }
                }
                
                $answers[] = [
                    'id_question' => $question->id,
                    'answer' => "Pilihan Jawaban {$option} (Poin: {$currentPoints})", 
                    'image' => null,
                    'is_correct' => $isCorrect,
                    'points' => $currentPoints, 
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // ====================================================================
        // PERBAIKAN UTAMA: Menggunakan Batch Insert (Chunking)
        // ====================================================================
        $chunkSize = 500; 
        
        // Konversi array ke Collection untuk menggunakan metode chunk()
        Collection::make($answers)->chunk($chunkSize)->each(function ($chunk) {
            DB::table('answers')->insert($chunk->toArray());
        });
        
        // DB::table('answers')->insert($answers); <--- BARIS LAMA DIHAPUS
    }
}