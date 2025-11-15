<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Tryout; 
use Illuminate\Support\Facades\DB; 

class Dashboard extends Component
{
    public function render()
    {
        // Ambil 4 tryout dengan persentase diskon tertinggi.
        $tryouts = Tryout::where('is_active', true)
                         ->whereNotNull('discount')
                         ->where('discount', '>', 0)
                         // Memastikan pembagi (harga awal) tidak nol
                         ->whereRaw('(price + discount) > 0') 
                         
                         // Hitung Persentase Diskon: (discount / (price + discount)) * 100
                         ->select('tryouts.*', 
                            DB::raw('(discount / (price + discount) * 100) AS discount_percent_calculated')
                         )
                         
                         // Urutkan berdasarkan persentase diskon (tertinggi ke terendah)
                         ->orderByDesc('discount_percent_calculated')
                         ->take(4) 
                         ->get();

        // Tambahkan atribut 'formatted_discount' dan 'discount_percentage'
        $tryouts->each(function ($tryout) {
            if ($tryout->discount > 0) {
                // Formatting diskon (jumlah penghematan)
                $tryout->formatted_discount = number_format($tryout->discount, 0, ',', '.');
                
                // Ambil persentase diskon yang sudah dihitung oleh DB
                $tryout->discount_percentage = round($tryout->discount_percent_calculated);
            } else {
                $tryout->formatted_discount = null;
                $tryout->discount_percentage = 0;
            }
        });

        // Melewatkan data ke view
        return view('livewire.customers.dashboard', [
            'isGuest' => Auth::guest(),
            'tryouts' => $tryouts,
        ])->layout('layouts.app');
    }
}