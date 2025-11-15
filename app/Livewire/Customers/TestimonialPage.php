<?php

namespace App\Livewire\Customers;

use App\Models\Review; // 👈 Import model Review
use Livewire\Component;
use Livewire\WithPagination; // 👈 Import trait untuk pagination

class TestimonialPage extends Component
{
    use WithPagination; // 👈 Gunakan trait pagination

    /**
     * Jika Anda menggunakan Tailwind CSS (bawaan layout Anda),
     * baris ini akan membuat tampilan link pagination-nya sesuai.
     */
    protected $paginationTheme = 'tailwind';

    public function render()
    {
        // 1. Ambil SEMUA review yang 'is_published' = 1
        // 2. Lakukan Eager Loading untuk 'user' dan 'tryout'
        // 3. Urutkan dari yang terbaru
        // 4. Bagi menjadi halaman-halaman (10 item per halaman)
        $reviews = Review::where('is_published', 1) 
                        ->with('user', 'tryout')     
                        ->latest()                  
                        ->paginate(10);             

        // Kirim data 'reviews' ke view
        // dan atur layout ke 'layouts.app'
        return view('livewire.customers.testimonial-page', [
            'reviews' => $reviews,
        ])
            ->layout('layouts.app') // 👈 Menggunakan 'layouts/app.blade.php'
            ->title('Testimoni - Ruang Juang'); // 👈 Mengisi <title>
    }
}