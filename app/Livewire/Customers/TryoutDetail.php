<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Tryout;
use App\Models\UserTryout;
use App\Models\Ranking;
use App\Models\Review;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\WithPagination; 

class TryoutDetail extends Component
{
    use WithPagination; // Menggunakan trait Pagination

    // Memberitahu Livewire untuk menggunakan tema pagination Tailwind (agar tidak refresh)
    protected $paginationTheme = 'tailwind';

    public Tryout $data;
    public $userTryoutHistory;
    public $selectedAttempt = null;

    // --- Properti untuk data tambahan ---
    public $rankings; // Ini akan menyimpan Top 10
    public $averageRating = 0;
    public $totalReviews = 0;
    public $userRanking = null; 
    
    /**
     * Mount komponen dan memuat data tryout.
     */
    public function mount($tryout) 
    {
        // Muat tryout berdasarkan slug, atau gagal
        $this->data = Tryout::where('slug', $tryout)->firstOrFail();
        
        // --- Load Data Non-Paginasi ---
        
        // 1. Ambil 10 Peringkat Teratas
        $this->rankings = Ranking::where('tryout_id', $this->data->id)
                                    ->with('user') 
                                    ->orderBy('score', 'desc')
                                    ->limit(10) 
                                    ->get();

        // 2. Buat query dasar untuk review (dipakai untuk total & avg)
        $baseReviewQuery = Review::where('tryout_id', $this->data->id);

        // 3. Hitung total dan rata-rata
        $this->totalReviews = $baseReviewQuery->count();
        $this->averageRating = $this->totalReviews > 0 ? $baseReviewQuery->avg('rating') : 0;

        // 4. Ambil ranking user saat ini
        if (Auth::check()) {
            $userId = Auth::id();
            $isUserInTop10 = $this->rankings->contains('id_user', $userId);

            if (!$isUserInTop10) {
                $this->userRanking = Ranking::where('tryout_id', $this->data->id)
                                            ->where('id_user', $userId)
                                            ->with('user')
                                            ->first();
            }
        }
        
        // Muat riwayat pengerjaan user
        $this->loadUserTryoutHistory();
    }
    
    /**
     * Helper untuk memuat riwayat pengerjaan user.
     */
    protected function loadUserTryoutHistory()
    {
        if (Auth::check()) {
            $this->userTryoutHistory = UserTryout::where('id_user', Auth::id())
                                                ->where('tryout_id', $this->data->id)
                                                ->orderBy('attempt', 'asc')
                                                ->get();
        } else {
            $this->userTryoutHistory = collect();
        }
    }
    
    /**
     * Dipanggil saat user mengklik "Mulai Percobaan Ke-X".
     */
    public function confirmStart($attemptNumber)
    {
        $this->selectedAttempt = $attemptNumber;
        $this->dispatch('show-copyright-modal');
    }
    
    /**
     * Dipanggil setelah user mengklik "Saya Mengerti & Lanjutkan" di modal.
     */
    public function startAttempt()
    {
        $attemptNumber = $this->selectedAttempt;
        
        if (!$attemptNumber) {
            session()->flash('error', 'Kesalahan internal: Nomor percobaan tidak ditemukan.');
            return;
        }

        $user = Auth::user();
        $userTryout = UserTryout::where('id_user', $user->id)
                                ->where('tryout_id', $this->data->id)
                                ->where('attempt', $attemptNumber)
                                ->first();
        
        if (!$userTryout) {
            session()->flash('error', 'Gagal memulai. Sesi tryout percobaan ke-' . $attemptNumber . ' tidak ditemukan.');
            return;
        }

        if ($userTryout->is_completed) {
            session()->flash('error', 'Percobaan ini sudah selesai dan tidak dapat diulang.');
            return;
        }
        
        if (is_null($userTryout->started_at)) {
            $now = Carbon::now();
            $endTime = $now->copy()->addMinutes($this->data->duration);

            $userTryout->update([
                'started_at' => $now,
                'ended_at' => $endTime,
                'is_completed' => false,
            ]);
        } else {
            if (Carbon::now()->isAfter($userTryout->ended_at)) {
                $userTryout->update(['is_completed' => true]); 
                session()->flash('error', 'Waktu pengerjaan untuk percobaan ini sudah habis.');
                return;
            }
        }
        
        $redirectUrl = route('tryout.start', [
            'tryout' => $this->data->slug,
            'attempt' => $attemptNumber
        ]);
        
        $this->selectedAttempt = null; 
        
        return $this->redirect($redirectUrl, navigate: false);
    }

    /**
     * [PERBAIKAN] Hook ini berjalan SETELAH halaman paginasi diperbarui.
     * Kita kirim event 'review-page-changed' ke browser.
     */
    public function updatedPage()
    {
        $this->dispatch('review-page-changed');
    }

    /**
     * Render tampilan.
     */
    public function render()
    {
        // Ambil data review dengan paginasi
        $reviews = Review::where('tryout_id', $this->data->id)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->paginate(5); 

        return view('livewire.customers.tryout-detail', [
            'userTryout' => $this->userTryoutHistory,
            'reviews' => $reviews, // Kirim data review yang sudah dipaginasi
        ])
        ->layout('layouts.app');
    }
}