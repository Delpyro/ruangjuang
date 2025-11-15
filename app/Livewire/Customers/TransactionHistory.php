<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Transaction; // Pastikan model Transaction di-import
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination; // Import trait untuk pagination

class TransactionHistory extends Component
{
    use WithPagination; // Gunakan trait pagination

    /**
     * Menggunakan tema pagination bawaan Tailwind.
     */
    protected $paginationTheme = 'tailwind';

    /**
     * Render komponen.
     */
    public function render()
    {
        // Ambil data transaksi HANYA untuk user yang sedang login
        $transactions = Transaction::where('id_user', Auth::id())
                                    ->with(['tryout', 'bundle']) // Eager load untuk info item
                                    ->orderBy('created_at', 'desc') // Tampilkan yang terbaru dulu
                                    ->paginate(10); // Ambil 10 data per halaman

        return view('livewire.customers.transaction-history', [
            'transactions' => $transactions,
        ])
        ->layout('layouts.app', ['title' => 'History Transaksi']); // Gunakan layout utama Anda
    }
}