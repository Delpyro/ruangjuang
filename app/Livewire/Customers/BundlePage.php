<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Bundle; // Import Model Bundle
use App\Models\Tryout; // Perlu diimpor karena digunakan di relasi
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class BundlePage extends Component
{
    use WithPagination;

    public $search = '';
    public $sort = 'latest'; // latest, price_asc, price_desc

    protected $queryString = [
        'search' => ['except' => '', 'as' => 'q'],
        'sort' => ['except' => 'latest', 'as' => 's'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }
    
    public function resetFilters()
    {
        $this->search = '';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        $bundles = Bundle::available() // Scope: aktif dan belum expired
            ->with('tryouts') // Load Tryout yang ada di dalamnya
            
            // Hanya tampilkan bundle yang BELUM dibeli oleh user ini
            // Menggunakan relasi 'purchasers' yang ada di Model Bundle
            ->whereDoesntHave('purchasers', function ($query) use ($userId) {
                $query->where('id_user', $userId);
            })

            // Filter Pencarian
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })

            // Sortir
            ->when($this->sort === 'latest', function ($query) {
                $query->latest();
            })
            ->when($this->sort === 'price_asc', function ($query) {
                // Urutkan berdasarkan harga final (dengan mempertimbangkan diskon)
                $query->orderByRaw('CASE WHEN discount IS NOT NULL AND discount > 0 THEN price - discount ELSE price END ASC');
            })
            ->when($this->sort === 'price_desc', function ($query) {
                // Urutkan berdasarkan harga final (dengan mempertimbangkan diskon)
                $query->orderByRaw('CASE WHEN discount IS NOT NULL AND discount > 0 THEN price - discount ELSE price END DESC');
            })
            ->paginate(12);

        return view('livewire.customers.bundle-page', [
            'bundles' => $bundles,
        ])->layout('layouts.app');
    }
}