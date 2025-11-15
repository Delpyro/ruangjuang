<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review; // Model Review Anda
use App\Models\User;   // Model User
use App\Models\Tryout; // Model Tryout

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Tentukan target kita
        $minReviewsPerTryout = 12;

        // 2. Ambil semua ID Tryout
        $tryoutIds = Tryout::pluck('id');
        
        // 3. Ambil semua ID User (yang bukan admin)
        $userIds = User::where('role', 'user')->pluck('id');

        // 4. Siapkan contoh teks review (lebih positif untuk bintang 5)
        $dummyTexts = [
            'Tryout-nya sangat membantu! Soal-soalnya relevan dan pembahasannya jelas.',
            'Penjelasannya mudah dipahami, nilaiku jadi lebih baik setelah ikut tryout ini.',
            'Sangat bermanfaat untuk mengukur kemampuan. Terbaik!',
            'Platformnya stabil dan mudah digunakan. Sangat direkomendasikan!',
            'Luar biasa! Materi dan kualitas soalnya sangat memuaskan.',
            'Sangat puas dengan tryout ini. Membantu saya menemukan apa yang perlu dipelajari lagi.',
            'Keren banget! Soal-soalnya HOTS dan menantang. 100% recommended.',
        ];

        // 5. [PERINGATAN] Cek apakah kita punya cukup user
        if ($userIds->count() < $minReviewsPerTryout) {
            // Ini sudah benar, menggunakan $this->
            $this->command->warn("GAGAL: Tidak dapat membuat {$minReviewsPerTryout} review per tryout.");
            $this->command->warn("Anda hanya memiliki {$userIds->count()} user (role 'user'), butuh minimal {$minReviewsPerTryout}.");
            return; // Hentikan seeder
        }
        
        if ($tryoutIds->isEmpty()) {
            // Ini sudah benar, menggunakan $this->
            $this->command->warn("Tidak ada Tryout di database. ReviewSeeder dilewati.");
            return;
        }

        $totalCreated = 0;
        
        // 6. Loop setiap tryout
        // [PERBAIKAN DI SINI] Mengganti $this. menjadi $this->
        $this->command->info("Membuat {$minReviewsPerTryout} review (BINTANG 5) untuk setiap tryout...");

        foreach ($tryoutIds as $tryoutId) {
            
            // 7. Ambil 12 ID User secara acak DARI SEMUA user yang ada
            $selectedUserIds = $userIds->random($minReviewsPerTryout);

            // 8. Loop 12 user yang dipilih tadi dan buat review untuk tryout INI
            foreach ($selectedUserIds as $userId) {
                Review::create([
                    'id_user'      => $userId,
                    'tryout_id'    => $tryoutId,
                    'rating'       => 5,
                    'review_text'  => $dummyTexts[array_rand($dummyTexts)],
                    'is_published' => 1,
                ]);
                $totalCreated++;
            }
        }
        
        // [PERBAIKAN DI SINI] Mengganti $this. menjadi $this->
        $this->command->info("Selesai! Berhasil membuat {$totalCreated} review (bintang 5).");
        
        // [PERBAIKAN DI SINI] Mengganti $this. menjadi $this->
        $this->command->info("({$tryoutIds->count()} tryout x {$minReviewsPerTryout} review/tryout)");
    }
}