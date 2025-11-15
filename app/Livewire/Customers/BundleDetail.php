<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Bundle; // Pastikan Model Bundle sudah diimpor
use Illuminate\Support\Facades\Auth;

class BundleDetail extends Component
{
    /**
     * @var \App\Models\Bundle Data model Bundle, diisi otomatis oleh Livewire/Laravel (Route Model Binding).
     */
    public Bundle $bundle;

    /**
     * @var bool Status apakah user sudah membeli bundle ini.
     */
    public $hasPurchased = false;

    /**
     * Metode mount akan menerima objek Model Bundle yang sudah di-resolve
     * dari parameter rute {bundle:slug}.
     *
     * @param Bundle $bundle
     * @return void
     */
    public function mount(Bundle $bundle)
    {
        // Model Bundle sudah di-load ke $this->bundle
        $this->bundle = $bundle; 
        
        // Pastikan relasi diload, terutama 'tryouts' dan 'purchasers'
        $this->bundle->loadMissing(['tryouts', 'purchasers']); 
        
        $this->checkPurchaseStatus();
        
        // Cek jika bundle tidak tersedia (misal di Route Model Binding Scope 'available()')
        if (!$this->bundle || !$this->bundle->is_active) {
            abort(404, 'Paket Bundle tidak ditemukan atau tidak aktif.');
        }
    }

    /**
     * Mengecek status pembelian bundle oleh pengguna yang sedang login.
     *
     * @return void
     */
    private function checkPurchaseStatus()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Cek apakah user id ada di relasi 'purchasers' (asumsi relasi many-to-many ke user)
            $this->hasPurchased = $this->bundle->purchasers()
                                                ->where('id_user', $userId)
                                                ->exists();
        }
    }

    /**
     * Render komponen.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.customers.bundle-detail', [
            'bundle' => $this->bundle,
            'hasPurchased' => $this->hasPurchased,
        ])->layout('layouts.app');
    }
}