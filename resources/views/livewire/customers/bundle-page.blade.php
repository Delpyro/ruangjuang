<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">Daftar Paket Bundle Try Out</h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                Dapatkan diskon dan paket lengkap dengan membeli bundle try out dengan penawaran terbaik.
            </p>
        </div>

        {{-- Filter & Sorting Area (Tetap ungu, sesuai permintaan) --}}
        <div class="bg-white rounded-xl shadow-2xl p-6 mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
                <div class="flex-1 w-full lg:w-auto">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Cari bundle..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                            wire:loading.attr="disabled"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <div wire:loading wire:target="search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-purple-500"></div>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-auto">
                    <select
                        wire:model.live="sort"
                        wire:loading.attr="disabled"
                        class="w-full lg:w-auto px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                    >
                        <option value="latest">Terbaru</option>
                        <option value="price_asc">Harga Terendah</option>
                        <option value="price_desc">Harga Tertinggi</option>
                    </select>
                </div>
            </div>
            @if($search || $sort !== 'latest')
            <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                <span class="text-gray-600">Filter aktif:</span>
                @if($search)
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full flex items-center">
                    Pencarian: "{{ $search }}"
                    <button wire:click="$set('search', '')" class="ml-2 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-times"></i>
                    </button>
                </span>
                @endif
                <button
                    wire:click="resetFilters"
                    class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center"
                >
                    <i class="fas fa-times mr-1"></i> Hapus Semua Filter
                </button>
            </div>
            @endif
        </div>
        
        {{-- Loading Spinner (Tidak Berubah) --}}
        <div wire:loading.delay class="fixed inset-0 bg-white bg-opacity-75 z-50 flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-xl p-8 max-w-sm mx-4">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto mb-4"></div>
                <p class="text-center text-gray-700 font-medium">Memuat bundle...</p>
                <p class="text-center text-gray-500 text-sm mt-2">Silakan tunggu sebentar</p>
            </div>
        </div>

        {{-- Daftar Bundle --}}
        <div wire:loading.remove class="contents">
            @if($bundles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach($bundles as $bundle)
                        {{-- Card Bundle --}}
                        <div class="bg-white rounded-xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 relative p-6 border border-gray-100"
                            wire:key="bundle-{{ $bundle->id }}-{{ $bundle->updated_at->timestamp }}">

                            {{-- Badge "BUNDLE HEMAT" Dihapus --}}

                            <h3 class="font-extrabold text-xl text-gray-900 leading-snug mb-3 mt-4">
                                {{ $bundle->title }}
                            </h3>
                            
                            {{-- Tryout List in Bundle --}}
                            <div class="mb-4 text-sm text-gray-600 pb-3 border-b border-gray-100">
                                <p class="font-semibold mb-1">Termasuk:</p>
                                <ul class="list-disc list-inside ml-2 space-y-0.5">
                                    @foreach($bundle->tryouts->take(3) as $tryout)
                                        <li class="truncate">{{ $tryout->title }}</li>
                                    @endforeach
                                    @if($bundle->tryouts->count() > 3)
                                        <li class="text-xs italic text-gray-500">+ {{ $bundle->tryouts->count() - 3 }} tryout lainnya...</li>
                                    @endif
                                </ul>
                            </div>
                            
                            {{-- Tombol/Link "Fasilitas yang didapatkan" --}}
                            <a href="{{ route('bundle.detail', ['bundle' => $bundle->slug]) }}"
                                wire:navigate
                                class="flex items-center justify-between text-blue-700 bg-blue-50 border border-blue-200 py-2.5 px-3 rounded-lg transition-colors duration-300 w-full font-medium mb-4 hover:bg-blue-100 text-sm">
                                <span>Fasilitas yang didapatkan</span>
                                <i class="fas fa-chevron-right ml-2 text-blue-500 text-xs"></i>
                            </a>

                            {{-- HARGA - Rapi dan Proporsional --}}
                            <div class="text-left mb-4">
                                @if($bundle->discount > 0)
                                    <p class="text-sm text-black line-through mb-1">
                                        Rp {{ number_format($bundle->price, 0, ',', '.') }}
                                    </p>
                                    <div class="flex items-baseline space-x-2">
                                        {{-- [DIUBAH] text-purple-700 -> text-green-700 --}}
                                        <span class="text-3xl font-bold text-green-700"> 
                                            Rp {{ number_format($bundle->final_price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-green-600 text-sm font-medium">
                                            Hemat {{ $bundle->discount_percentage }}%
                                        </span>
                                    </div>
                                @else
                                    {{-- [DIUBAH] text-purple-700 -> text-green-700 --}}
                                    <span class="text-3xl font-bold text-green-700">
                                        Rp {{ number_format($bundle->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            
                            {{-- Saving Info --}}
                            @if(isset($bundle->savings_percentage) && $bundle->savings_percentage > 0)
                                <div class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-3 py-1.5 rounded-lg mb-4 text-center">
                                    Hemat hingga {{ $bundle->savings_percentage }}% dibandingkan beli satuan!
                                </div>
                            @endif

                            {{-- Tombol Beli --}}
                            {{-- [DIUBAH] bg-purple-600 -> bg-green-600 dan hover:bg-purple-700 -> hover:bg-green-700 --}}
                            <a href="{{ route('bundle.payment', ['bundle_slug' => $bundle->slug]) }}"
                                wire:navigate
                                class="flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-medium py-3 rounded-lg shadow-md transition-colors duration-300 w-full mb-2">
                                <i class="fa-solid fa-money-bill-wave mr-2"></i> Beli Bundle
                            </a>
                            
                            {{-- Tombol Detail --}}
                            <a href="{{ route('bundle.detail', ['bundle' => $bundle->slug]) }}"
                                wire:navigate
                                class="flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 rounded-lg transition-colors duration-300 w-full text-sm">
                                <i class="fas fa-eye mr-2"></i> Detail Bundle
                            </a>

                        </div>
                    @endforeach
                </div>

                <div class="flex justify-center">
                    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-100">
                        {{ $bundles->links() }}
                    </div>
                </div>
            @else
                {{-- No Results --}}
                <div class="text-center py-12">
                    <div class="bg-white rounded-xl shadow-2xl p-8 max-w-md mx-auto border border-gray-100">
                        <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Bundle Tidak Ditemukan</h3>
                        <p class="text-gray-600 mb-4">Tidak ada bundle yang tersedia atau sesuai dengan pencarian Anda.</p>
                        <button wire:click="resetFilters" class="bg-purple-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-purple-700 transition-colors duration-300">
                            <i class="fas fa-refresh mr-2"></i> Reset Pencarian
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>