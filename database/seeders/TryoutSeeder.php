<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TryoutSeeder extends Seeder
{
    public function run(): void
    {
        $startDate = now();
        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            
            $title = "TRYOUT $i SKD KEDINASAN & CPNS";
            
            // <-- 👇 HARGA DIUBAH DI SINI 👇 -->
            $price = 25000; 
            
            $discountActive = ($i % 4 === 0);
            $discountAmount = $discountActive ? fake()->numberBetween(1000, 5000) : 0;

            $data[] = [
                'title' => $title,
                'slug' => Str::slug($title), 
                'is_hots' => $i % 3 === 0, 
                'duration' => fake()->randomElement([90, 100, 120]), 
                'content' => fake()->paragraphs(3, true),
                'quote' => fake()->sentence(8),
                'price' => $price, // <-- Nilai 25000 akan digunakan di sini
                'discount' => $discountAmount,
                'discount_start_date' => $discountActive ? $startDate->copy()->addDays(fake()->numberBetween(-10, 0)) : null,
                'discount_end_date' => $discountActive ? $startDate->copy()->addDays(fake()->numberBetween(7, 30)) : null,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }

        DB::table('tryouts')->insert($data);
    }
}