@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-2xl shadow-lg p-8 text-center">
            @if($success)
                <div class="text-green-500 text-6xl mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Pembayaran Berhasil!</h2>
                <p class="text-gray-600 mb-6">{{ $message }}</p>
                
                @if(isset($transaction))
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                    <h3 class="font-semibold text-gray-800 mb-2">Detail Transaksi:</h3>
                    <p><strong>Tryout:</strong> {{ $transaction->tryout->title }}</p>
                    <p><strong>Amount:</strong> Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                    <p><strong>Order ID:</strong> {{ $transaction->order_id }}</p>
                </div>
                @endif

                <div class="flex gap-4">
                    <a href="{{ route('tryout.detail', $transaction->tryout->slug) }}" 
                       class="flex-1 bg-primary text-white py-3 px-4 rounded-lg font-medium hover:bg-primary-dark transition-colors">
                        Mulai Tryout
                    </a>
                    <a href="{{ route('tryout') }}" 
                       class="flex-1 bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                        Lihat Tryout Lain
                    </a>
                </div>
            @else
                <div class="text-red-500 text-6xl mb-4">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Pembayaran Gagal</h2>
                <p class="text-gray-600 mb-6">{{ $message }}</p>
                
                @if(isset($transaction))
                <a href="{{ route('tryout.payment', $transaction->tryout->slug) }}" 
                   class="bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary-dark transition-colors inline-block">
                    Coba Lagi
                </a>
                @else
                <a href="{{ route('tryout') }}" 
                   class="bg-primary text-white py-3 px-6 rounded-lg font-medium hover:bg-primary-dark transition-colors inline-block">
                    Kembali ke Tryout
                </a>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection