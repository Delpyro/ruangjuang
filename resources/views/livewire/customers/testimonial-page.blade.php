<div>
    {{-- Latar belakang 'bg-white' murni untuk kesan minimalis --}}
    <section class="bg-white">
        
        {{-- 
          Container dikembalikan ke lebar default (cukup untuk 3 kolom)
          Padding atas 'pt-32 sm:pt-40' tetap untuk memberi jarak dari navbar.
        --}}
        <div class="container mx-auto px-4
                    pt-32 sm:pt-40 
                    pb-16 sm:pb-24">

            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">
                    Apa Kata Para Pejuang?
                </h2>
                <p class="text-lg text-gray-500 mt-3 max-w-xl mx-auto">
                    Lihat bagaimana Ruang Juang membantu perjalanan mereka.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @forelse ($reviews as $review)
                    <div class="bg-white rounded-xl border border-gray-200
                                flex flex-col 
                                transition-all duration-300 hover:border-gray-300"
                         data-aos="fade-up" 
                         data-aos-delay="{{ ($loop->index % 3) * 50 }}" {{-- Animasi estafet diubah ke % 3 --}}
                         wire:key="{{ $review->id }}">
                        
                        <div class="p-8 flex-grow">
                            
                            <div class="flex items-center mb-5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg @class([
                                        'w-5 h-5',
                                        'text-yellow-400' => $i <= $review->rating,
                                        'text-gray-300' => $i > $review->rating,
                                    ]) 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.49 7.09l6.57-.955L10 0l2.94 6.135 6.57.955-4.755 4.455 1.123 6.545z"/>
                                    </svg>
                                @endfor
                            </div>

                            <p class="text-xl font-medium text-gray-800 leading-relaxed min-h-[100px]">
                                {{ $review->review_text }}
                            </p>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-11 h-11 rounded-full bg-gray-100 text-primary 
                                            flex items-center justify-center font-semibold text-base">
                                    {{ $review->user ? mb_substr($review->user->name, 0, 1) : '?' }}
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">
                                        {{ $review->user->name ?? 'Pengguna' }}
                                    </h3>
                                    @if ($review->tryout)
                                        <p class="text-sm text-gray-500">
                                            Peserta {{ $review->tryout->title }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                    @empty
                    <div class="md:col-span-2 lg:col-span-3 bg-gray-50 rounded-lg p-10 text-center"> {{-- diubah ke lg:col-span-3 --}}
                        <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">
                            Belum ada testimoni yang dipublikasikan.
                        </p>
                    </div>
                @endforelse

            </div> <div class="mt-16">
                {{ $reviews->links() }}
            </div>

        </div>
    </section>
</div>