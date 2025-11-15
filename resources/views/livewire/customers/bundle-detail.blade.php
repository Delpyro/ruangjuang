<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4 max-w-6xl"> 
        
        {{-- Tombol Kembali (Dipertahankan di Header) --}}
        <div class="mb-6">
            <a href="{{ route('bundle.index') }}" wire:navigate class="text-purple-600 hover:text-purple-800 font-medium flex items-center transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Bundle
            </a>
        </div>

        @if ($bundle)
        {{-- Main Card --}}
        <div class="bg-white rounded-xl shadow-2xl p-8 lg:p-10 border border-gray-100" data-aos="fade-up">
            
            {{-- Header Title --}}
            <div class="flex items-start justify-between mb-2 pb-4 border-b border-gray-100">
                <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">{{ $bundle->title }}</h1>
                
                {{-- Badge Status Pembelian --}}
                @if($hasPurchased)
                    <div class="flex-shrink-0 ml-4 mt-1">
                        <span class="bg-green-100 text-green-700 text-sm font-semibold px-3 py-1.5 rounded-full inline-flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> Sudah Dibeli
                        </span>
                    </div>
                @endif
            </div>

            {{-- Body Konten --}}
            <div class="space-y-8 mt-6"> 

                {{-- Deskripsi Paket --}}
                <div>
                    <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-3 text-purple-500 text-xl"></i> Detail Paket
                    </h2>
                    <div class="text-gray-700 leading-relaxed prose max-w-none text-base border p-4 rounded-lg bg-gray-50">
                        {!! $bundle->description !!} 
                    </div>
                </div>
            </div> {{-- End Body Konten --}}

            <hr class="my-8 border-gray-100">

            {{-- ** STRUKTUR DUA KOLOM ** --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10">

                {{-- KOLOM KIRI: Daftar Try Out (2/3 lebar di layar besar) --}}
                <div class="lg:col-span-2">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-list-alt mr-3 text-purple-500 text-xl"></i> Daftar Try Out dalam Paket ({{ $bundle->tryouts->count() }})
                    </h2>
                    <div class="space-y-3">
                        @forelse ($bundle->tryouts as $tryout)
                            <div class="flex items-start p-4 bg-white rounded-lg border border-gray-200 shadow-sm hover:bg-gray-50 transition duration-150">
                                <i class="fas fa-check-square text-green-500 mt-1 mr-3 text-lg flex-shrink-0"></i>
                                <div>
                                    <p class="font-semibold text-gray-800 truncate">{{ $tryout->title }}</p>
                                    <p class="text-sm text-gray-500">Harga Satuan: Rp {{ number_format($tryout->price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic p-4 bg-yellow-50 rounded-lg col-span-2">Tidak ada tryout dalam paket ini.</p>
                        @endforelse
                    </div>
                </div>

                {{-- KOLOM KANAN: Harga dan Aksi (1/3 lebar di layar besar) --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-28 bg-purple-50 p-6 rounded-xl shadow-lg border border-purple-200">
                        
                        {{-- Informasi Harga --}}
                        <div class="mb-6">
                            <p class="text-base text-gray-700 font-semibold mb-2">Harga Bundle Saat Ini:</p>
                            
                            @if($bundle->discount > 0)
                                {{-- Harga Coret --}}
                                <p class="text-sm text-gray-500 line-through mb-1">
                                    Harga Awal: Rp {{ number_format($bundle->price, 0, ',', '.') }}
                                </p>
                                <div class="flex items-baseline space-x-2">
                                    {{-- Harga Final --}}
                                    <span class="text-4xl font-extrabold text-purple-800">
                                        Rp {{ number_format($bundle->final_price, 0, ',', '.') }}
                                    </span>
                                    {{-- Hemat --}}
                                    <span class="text-base font-bold text-green-600">
                                        (Hemat Rp {{ number_format($bundle->discount, 0, ',', '.') }})
                                    </span>
                                </div>
                            @else
                                <span class="text-4xl font-extrabold text-purple-800">
                                    Rp {{ number_format($bundle->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- Tombol Aksi --}}
                        <div class="space-y-3">
                            @if(!$hasPurchased)
                            {{-- Tombol Beli (Warna Hijau) --}}
                            <a href="{{ route('bundle.payment', ['bundle_slug' => $bundle->slug]) }}"
                                wire:navigate
                                class="w-full flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-6 rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.01]">
                                <i class="fas fa-money-bill-wave mr-3 text-xl"></i> BELI PAKET INI
                            </a>
                            @else
                            {{-- Tombol Akses Try Out (Warna Primary/Purple) --}}
                            <a href="{{ route('tryout.my-tryouts') }}" 
                                wire:navigate 
                                class="w-full flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white font-bold py-3.5 px-6 rounded-lg shadow-md transition-all duration-300 transform hover:scale-[1.01]">
                                <i class="fas fa-play-circle mr-3 text-xl"></i> AKSES TRY OUT
                            </a>
                            @endif
                            
                            {{-- Tombol Kembali ke Katalog DIHILANGKAN --}}

                        </div>
                        
                    </div>
                </div>

            </div> {{-- ** END STRUKTUR DUA KOLOM ** --}}

        </div>
        @endif
    </div>
</div>