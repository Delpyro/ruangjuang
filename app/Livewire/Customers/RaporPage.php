<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Ranking;
use App\Models\TryoutCategoryScore;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class RaporPage extends Component
{
    // --- Properti untuk Tabel ---
    public $reportData = [];

    // --- Properti untuk Grafik (Chart) ---
    public $chartLabels = [];
    public $chartSeries = [];
    public Collection $allCategoryNames; // Untuk header tabel dinamis

    /**
     * Mount komponen dan siapkan data rapor.
     */
    public function mount()
    {
        $userId = Auth::id();

        // 1. Ambil semua data skor kategori (attempt 1) milik user
        // Ini adalah sumber data utama kita untuk nama kategori
        $allCategoryScores = TryoutCategoryScore::whereHas('userTryout', function ($query) use ($userId) {
                $query->where('id_user', $userId)->where('attempt', 1);
            })
            ->with(['questionCategory:id,name', 'userTryout:id,tryout_id'])
            ->get();

        // 2. Ambil semua nama kategori yang unik (misal: "TWK", "TIU", "TKP")
        $categoryNames = $allCategoryScores->pluck('questionCategory.name')->unique()->sort()->values();
        $this->allCategoryNames = $categoryNames; // Simpan untuk header tabel

        // 3. Kelompokkan skor kategori berdasarkan tryout_id untuk pencarian cepat
        $groupedCategoryScores = $allCategoryScores->groupBy('userTryout.tryout_id');

        // 4. Ambil data skor total (dari tabel Ranking)
        // Ini akan menjadi PENGGERAK UTAMA untuk urutan chart (vol 1, vol 2, dst.)
        $rankings = Ranking::where('id_user', $userId)
            ->with('tryout:id,title')
            ->orderBy('created_at', 'asc') // Urutkan berdasarkan kapan attempt 1 selesai
            ->get();

        if ($rankings->isEmpty()) {
            return; // Tidak ada data untuk ditampilkan
        }

        // --- 5. Siapkan Data untuk Chart & Tabel ---
        $labels = [];
        $seriesData = [];
        $tableData = [];

        // Inisialisasi series chart
        $seriesData['Total Score'] = [];
        foreach ($categoryNames as $name) {
            $seriesData[$name] = []; // Buat series untuk "TWK", "TIU", "TKP", dll.
        }

        // Loop berdasarkan data Ranking (yang sudah terurut)
        foreach ($rankings as $ranking) {
            $tryoutId = $ranking->tryout_id;
            
            // --- A. Data untuk Chart ---
            $labels[] = $ranking->tryout->title; // Label X-Axis
            $seriesData['Total Score'][] = $ranking->score; // Data untuk "Total Score"
            
            // Ambil skor kategori untuk tryout ini
            $currentTryoutCategories = $groupedCategoryScores->get($tryoutId);

            // --- B. Data untuk Tabel ---
            $tableRow = [
                'title' => $ranking->tryout->title,
                'tanggal' => $ranking->created_at, // Tanggal selesai attempt 1
                'total_score' => $ranking->score,
                'categories' => []
            ];

            // Loop daftar nama kategori UTAMA untuk memastikan data chart sejajar
            foreach ($categoryNames as $name) {
                $categoryScore = 0; // Default 0
                
                if ($currentTryoutCategories) {
                    // Cari skor untuk kategori ini di tryout ini
                    $scoreModel = $currentTryoutCategories->firstWhere('questionCategory.name', $name);
                    if ($scoreModel) {
                        $categoryScore = $scoreModel->score;
                    }
                }
                
                $seriesData[$name][] = $categoryScore; // Tambahkan ke data chart
                $tableRow['categories'][$name] = $categoryScore; // Tambahkan ke data tabel
            }
            
            $tableData[] = $tableRow;
        }

        // --- 6. Format Final untuk Chart ---
        $chartSeries = [];
        foreach ($seriesData as $name => $data) {
            $chartSeries[] = [
                'name' => $name,
                'data' => $data
            ];
        }

        // 7. Set Properti Publik
        $this->chartLabels = $labels;
        $this->chartSeries = $chartSeries;
        $this->reportData = $tableData;
    }

    /**
     * Render tampilan.
     */
    public function render()
    {
        return view('livewire.customers.rapor-page')
               ->layout('layouts.app', ['title' => 'Rapor Saya']);
    }
}