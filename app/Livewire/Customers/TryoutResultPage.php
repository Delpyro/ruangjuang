<?php

namespace App\Livewire\Customers;

use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\UserAnswer;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class TryoutResultPage extends Component
{
    public Tryout $tryout;
    public ?EloquentCollection $allCompletedAttempts = null;
    public array $resultsData = [];

    // --- Properti untuk Modal Review ---
    public bool $showReviewModal = false;
    public int $reviewRating = 0;
    public string $reviewText = '';

    // --- Properti untuk kontrol tampilan ---
    public bool $hasReviewed = false;
    
    // --- Properti internal ---
    public $allTryoutQuestions; 
    public $userId;

    /**
     * Mount komponen
     */
    public function mount(Tryout $tryout)
    {
        $this->tryout = $tryout;
        $this->userId = Auth::id();

        // 1. Cek pengerjaan yang sudah selesai
        $this->allCompletedAttempts = UserTryout::where('id_user', $this->userId)
            ->where('tryout_id', $this->tryout->id)
            ->where('is_completed', true)
            ->orderBy('attempt', 'asc')
            ->get();

        // 2. Jika tidak ada, redirect
        if ($this->allCompletedAttempts->isEmpty()) {
            session()->flash('error', 'Hasil tryout belum tersedia atau belum Anda selesaikan.');
            return $this->redirect(route('tryout.detail', $tryout->slug));
        }

        // 3. Cek apakah user sudah mereview
        $this->hasReviewed = Review::where('id_user', $this->userId)
                                     ->where('tryout_id', $this->tryout->id)
                                     ->exists();

        // 4. Muat soal (dibutuhkan untuk kalkulasi)
        $this->allTryoutQuestions = $this->tryout->activeQuestions()
                                            ->with('category')
                                            ->get();

        // 5. Logika Gated (Terkunci)
        if ($this->hasReviewed) {
            // --- JIKA SUDAH REVIEW ---
            $this->loadAndCalculateResults();
        } else {
            // --- JIKA BELUM REVIEW ---
            $this->showReviewModal = true;
        }
    }

    /**
     * Memuat dan menghitung hasil.
     */
    public function loadAndCalculateResults()
    {
        $this->resultsData = []; 

        foreach ($this->allCompletedAttempts as $attempt) {
            $this->resultsData[] = $this->calculateResultForAttempt($attempt, $this->allTryoutQuestions);
        }
    }

    /**
     * Dipanggil saat user klik rating bintang.
     */
    public function setRating($rating)
    {
        $this->reviewRating = (int) $rating;
    }

    /**
     * Dipanggil saat modal disubmit.
     */
    public function submitReview()
    {
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewText' => 'required|string|min:10|max:1000',
        ], [
            'reviewRating.min' => 'Harap berikan minimal 1 bintang.',
            'reviewText.required' => 'Ulasan tidak boleh kosong.',
            'reviewText.min' => 'Ulasan Anda terlalu singkat (minimal 10 karakter).',
        ]);

        // Simpan review
        Review::updateOrCreate(
            [
                'id_user'   => $this->userId,
                'tryout_id' => $this->tryout->id,
            ],
            [
                'rating'      => $this->reviewRating,
                'review_text' => $this->reviewText,
            ]
        );

        $this->showReviewModal = false;
        $this->hasReviewed = true; 

        // [PENTING] Hitung skor SEKARANG
        $this->loadAndCalculateResults();
    }


    /**
     * [LOGIKA DIPERBAIKI]
     * Logika inti untuk menghitung total skor dan statistik UNTUK SATU PERCOBAAN.
     * Kini lebih akurat membedakan 'salah' dan 'kosong'.
     */
    private function calculateResultForAttempt(UserTryout $userTryout, EloquentCollection $allTryoutQuestions): array
    {
        // 1. Load jawaban user, di-indeks berdasarkan 'question_id' untuk pencarian cepat
        $userAnswers = UserAnswer::where('user_tryout_id', $userTryout->id)
            ->with(['answer', 'question.category']) 
            ->get()
            ->keyBy('question_id'); // <-- Optimasi

        // 2. Siapkan variabel kalkulasi
        $totalQuestions = $allTryoutQuestions->count();
        $correctCount = 0;
        $wrongCount = 0;     // <-- [BARU] Eksplisit untuk yang dijawab salah
        $unansweredCount = 0; // <-- [BARU] Eksplisit untuk yang tidak dijawab
        $totalScore = 0.0;
        $categorySummary = [];

        // 3. Inisialisasi Kategori (berdasarkan SEMUA soal)
        foreach ($allTryoutQuestions as $question) {
            $categoryId = $question->category->id ?? 0;
            $categoryName = $question->category->name ?? 'Tanpa Kategori';
            
            if (!isset($categorySummary[$categoryId])) {
                $categorySummary[$categoryId] = [
                    'name' => $categoryName,
                    'total_soal' => 0,
                    'benar' => 0,
                    'salah' => 0,  // <-- Hanya untuk yg dijawab salah
                    'kosong' => 0, // <-- Hanya untuk yg tidak dijawab
                    'skor_kategori' => 0.0,
                    'percentage' => 0, // <-- [BARU] Untuk progress bar
                ];
            }
            $categorySummary[$categoryId]['total_soal']++;
        }

        // 4. Proses Jawaban
        // [LOGIKA DIUBAH] Kita loop SEMUA SOAL, bukan hanya jawaban user.
        // Ini memastikan soal yang tidak dijawab (kosong) terhitung.
        foreach ($allTryoutQuestions as $question) {
            $categoryId = $question->category->id ?? 0;
            $userAnswer = $userAnswers->get($question->id); // Ambil jawaban by question_id

            if ($userAnswer && $userAnswer->answer_id) {
                // --- KASUS 1: DIJAWAB ---
                $selectedAnswerModel = $userAnswer->answer;
                $pointsEarned = $userAnswer->score ?? ($selectedAnswerModel ? $selectedAnswerModel->points : 0);
                $isCorrect = $selectedAnswerModel ? $selectedAnswerModel->is_correct : false;

                if ($isCorrect) {
                    $correctCount++;
                    $categorySummary[$categoryId]['benar']++;
                } else {
                    $wrongCount++; // <-- [FIX]
                    $categorySummary[$categoryId]['salah']++; // <-- [FIX]
                }
                $totalScore += $pointsEarned;
                $categorySummary[$categoryId]['skor_kategori'] += $pointsEarned;

            } else {
                // --- KASUS 2: TIDAK DIJAWAB ---
                // (baik $userAnswer tidak ada ATAU $userAnswer->answer_id null)
                $unansweredCount++; // <-- [FIX]
                $categorySummary[$categoryId]['kosong']++; // <-- [FIX]
            }
        }
        
        // 5. [BARU] Hitung persentase per kategori (setelah semua dihitung)
        foreach ($categorySummary as $categoryId => &$summary) { // <-- & (by reference)
            $summary['percentage'] = ($summary['total_soal'] > 0) 
                ? ($summary['benar'] / $summary['total_soal']) * 100 
                : 0;
        }
        unset($summary); // Hapus referensi

        // 6. Kembalikan hasil kalkulasi
        return [
            'attempt' => $userTryout->attempt,
            'ended_at' => $userTryout->ended_at,
            'finalScore' => $totalScore,
            'totalQuestions' => $totalQuestions,
            'totalCorrect' => $correctCount,
            'totalWrong' => $wrongCount,         // <-- [BARU] Lebih eksplisit
            'totalUnanswered' => $unansweredCount, // <-- [BARU] Lebih eksplisit
            'totalAnswered' => $correctCount + $wrongCount, // Kalkulasi yang benar
            'categoryScores' => array_values($categorySummary),
        ];
    }

    /**
     * Aksi untuk mengarahkan pengguna ke halaman pembahasan.
     */
    public function goToDiscussion()
    {
        if (!$this->hasReviewed) {
            $this->showReviewModal = true;
            return;
        }
        
        return $this->redirect(route('tryout.discussion', $this->tryout->slug));
    }

    /**
     * Render komponen.
     */
    public function render()
    {
        return view('livewire.customers.tryout-result-page')
            ->layout('layouts.app', ['title' => 'Hasil Tryout: ' . $this->tryout->title]);
    }
}