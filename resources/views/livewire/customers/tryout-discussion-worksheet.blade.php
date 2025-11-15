<div>
    {{-- PROGRESS BAR DIHAPUS --}}

    <div id="main-container" class="flex h-screen relative overflow-hidden"> 
        
        <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden transition-opacity duration-300 opacity-0"></div>

        {{-- SIDEBAR (DAFTAR SOAL) --}}
        <div id="sidebar" 
             class="fixed inset-y-0 left-0 z-50 w-3/4 max-w-xs bg-white border-r overflow-y-scroll p-4 shadow-2xl
                    transform -translate-x-full transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0"> 
            
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2 md:grid-cols-5"> 
                {{-- Loop menggunakan questionIds --}}
                @for ($index = 0; $index < $totalQuestions; $index++)
                    @php
                        $statusClass = $this->getQuestionStatusClass($index);
                        $activeClassMarker = ' z-10 scale-105 border-2 border-cyan-400 ring-2 ring-cyan-200 shadow-lg';
                        $baseBgClass = str_replace($activeClassMarker, '', $statusClass);
                    @endphp
                    <button 
                        wire:click="navigateToQuestion({{ $index }})"
                        class="w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm 
                               transition relative 
                               {{ $baseBgClass }}
                               @if($currentQuestionIndex === $index)
                                   z-10 scale-105 border-2 border-cyan-400 ring-2 ring-cyan-200 shadow-lg
                               @endif
                               "> 
                        {{ $index + 1 }}
                    </button>
                @endfor
            </div>
            
            <div class="mt-6 pt-4 border-t">
                <a href="{{ route('tryout.my-results', $tryout->slug) }}" class="w-full inline-flex justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    &larr; Ke Ringkasan Hasil
                </a>
            </div>
        </div>

        {{-- MAIN CONTENT (SOAL & PEMBAHASAN) --}}
        <div class="flex-1 flex flex-col relative min-w-0"> 
            {{-- TOP HEADER --}}
            <div class="bg-blue-700 text-white flex justify-between items-center px-4 py-3 sticky top-0 z-30 md:px-6 shadow-md"> 
                
                <button id="toggle-sidebar-btn" class="md:hidden bg-blue-600 hover:bg-blue-800 px-3 py-1 rounded flex items-center gap-2 text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <span id="btn-text">Daftar Soal</span>
                </button>
                
                <h2 class="font-bold text-lg hidden md:block">Pembahasan: {{ $tryout->title }}</h2>
                <h2 class="font-bold text-lg block md:hidden truncate">Soal No. {{ $currentQuestionIndex + 1 }}</h2>

                <div class="text-sm font-semibold bg-blue-600 px-3 py-0.5 rounded shadow-lg md:text-xl md:px-4 md:py-1">
                    Pembahasan
                </div>
            </div>

            {{-- AREA SOAL --}}
            <div class="flex-1 py-3 overflow-y-auto bg-gray-50 px-3 md:py-6 md:px-6"> 
                @if ($currentQuestion)
                
                @php
                    $question = $currentQuestion;
                    $categoryName = $question->category->name ?? 'Kategori';
                    $subCategoryName = $question->subCategory->name ?? 'Subkategori';
                    $history = $this->getCurrentQuestionAnswerHistory(); 
                    $correctAnswerId = $this->getCorrectAnswerId();
                    $firstAttempt = $history[0] ?? null;
                    $optionLetters = ['A', 'B', 'C', 'D', 'E'];
                @endphp
                
                <div class="space-y-3"> 
                    
                    {{-- NAMA SUB KATEGORI --}}
                    <div class="p-2 mb-2 rounded-lg bg-blue-600 text-white font-bold text-center text-sm md:text-base uppercase">
                        {{ $categoryName }} - {{ $subCategoryName }}
                    </div>
                    
                    {{-- 
                      [PERBAIKAN SOAL]
                      - font-semibold DIHAPUS dari div ini.
                      - text-base md:text-lg DIHAPUS untuk membiarkan font bawaan TinyMCE.
                    --}}
                    <div class="mb-3 text-gray-800"> 
                        {{-- [DIUBAH] font-bold DITAMBAHKAN hanya ke nomor --}}
                        <span class="float-left mr-2 flex-shrink-0 font-bold text-base md:text-lg">{{ $currentQuestionIndex + 1 }}.</span>
                        <div class="overflow-x-auto">{!! $question->question !!}</div> 
                    </div>

                    {{-- Gambar Soal (jika ada) --}}
                    @if ($question->hasImage())
                        <div class="my-3 p-1 border rounded-md">
                            <img src="{{ asset('storage/' . $question->image) }}" alt="Gambar Soal" class="max-w-full h-auto rounded-md mx-auto">
                        </div>
                    @endif

                    {{-- Pilihan Jawaban (Dengan Riwayat) --}}
                    <div class="space-y-2 text-gray-700">
                        @foreach ($question->answers->sortBy('id') as $indexAnswer => $answer)
                            @php
                                $letter = $optionLetters[$indexAnswer] ?? '';
                                $isCorrectAnswer = ($correctAnswerId == $answer->id);
                                
                                $baseClass = 'flex flex-col md:flex-row justify-between items-start md:items-center px-3 py-2 border rounded transition'; // text-sm DIHAPUS
                                $highlightClass = 'border-gray-200';
                                $statusTexts = [];
                                $isChosenInFirst = $firstAttempt && $firstAttempt['answer_id'] == $answer->id;

                                // --- LOGIKA RIWAYAT & STYLING ---
                                foreach ($history as $attempt) {
                                    if ($attempt['answer_id'] == $answer->id) {
                                        $statusTexts[] = "Dipilih pada Pengerjaan ke-{$attempt['attempt_number']}";
                                    }
                                }

                                if ($isCorrectAnswer) {
                                    $highlightClass = 'bg-green-50 border-green-400';
                                    if ($isChosenInFirst) {
                                        $highlightClass = 'bg-green-100 border-green-600';
                                    }
                                } elseif ($isChosenInFirst) {
                                    $highlightClass = 'bg-red-100 border-red-500'; 
                                }
                            @endphp

                            <div class="{{ $baseClass }} {{ $highlightClass }} w-full">
                                {{-- Teks Jawaban --}}
                                <div class="flex items-start flex-1 min-w-0 w-full">
                                    <span class="font-bold mr-2 w-4 flex-shrink-0 text-left text-base">{{ $letter }}.</span>
                                    
                                    {{-- 
                                      [PERBAIKAN JAWABAN]
                                      - Kelas 'prose' dan 'prose-sm' DIHAPUS dari div ini.
                                      - Ini akan membiarkan HTML dari TinyMCE tampil apa adanya.
                                    --}}
                                    <div class="max-w-none flex-1 min-w-0 overflow-x-auto">
                                        {!! $answer->answer !!}
                                    </div>
                                </div>
                                
                                {{-- Status Riwayat --}}
                                @if (!empty($statusTexts))
                                    <div class="mt-2 md:mt-0 md:ml-4 flex flex-wrap gap-1 text-xs font-semibold flex-shrink-0 md:text-right"> 
                                        @foreach ($statusTexts as $text)
                                            @php
                                                $textColor = 'text-blue-600 bg-blue-100';
                                                if (str_contains($text, 'Pengerjaan ke-1')) {
                                                    $textColor = $isCorrectAnswer ? 'text-green-700 bg-green-200' : 'text-red-700 bg-red-200';
                                                }
                                            @endphp
                                            <span class="block px-2 py-0.5 rounded {{ $textColor }}">
                                                {{ $text }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- SECTION PEMBAHASAN --}}
                    <div class="mt-6 pt-4 border-t border-dashed border-gray-300"> 
                        <details open>
                            <summary class="font-extrabold text-base md:text-lg cursor-pointer text-indigo-700 hover:text-indigo-800 flex items-center gap-2">
                                <svg class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                Penjelasan
                            </summary>
                            {{-- [PERBAIKAN PEMBAHASAN] Kelas 'prose' DIHAPUS --}}
                            <div class="max-w-none mt-3 p-4 bg-indigo-50 rounded-lg text-gray-800 overflow-x-auto">
                                @if ($question->explanation)
                                    {!! $question->explanation !!}
                                @else
                                    <p class="text-sm italic text-gray-600">Mohon maaf, pembahasan belum tersedia untuk soal ini.</p>
                                @endif
                            </div>
                        </details>
                    </div>

                    {{-- Tombol Navigasi Bawah --}}
                    <div class="mt-4 pb-4 flex flex-row flex-wrap justify-center md:justify-start items-center gap-2">
                        
                        <button 
                            wire:click="previousQuestion"
                            @if($currentQuestionIndex == 0) disabled @endif
                            class="bg-blue-600 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 text-sm
                                   {{ $currentQuestionIndex == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700' }}">
                            &larr; Sebelumnya
                        </button>
                        
                        <a href="{{ route('tryout.my-results', $tryout->slug) }}"
                           class="bg-gray-600 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 hover:bg-gray-700 text-center text-sm">
                            Ke Ringkasan
                        </a>
                        
                        <button 
                            wire:click="nextQuestion"
                            @if($currentQuestionIndex == $totalQuestions - 1) disabled @endif
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-1 rounded-lg shadow-md w-auto h-8 text-sm
                                   {{ $currentQuestionIndex == $totalQuestions - 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                            Selanjutnya &rarr;
                        </button>
                    </div>
                </div>
                @else
                <div class="p-6 border rounded-lg shadow-lg bg-white w-full"> 
                    <p class="text-lg font-semibold text-gray-700 text-center">Gagal memuat soal. Silakan muat ulang halaman.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Scrollbar halus */
    ::-webkit-scrollbar {
        width: 8px;
    }
    ::-webkit-scrollbar-thumb {
        background-color: #9CA3AF;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background-color: #6B7280;
    }

    /* Animasi untuk details summary */
    details[open] summary svg {
        transform: rotate(180deg);
    }
    
    /* [DIHAPUS] .prose { max-width: none !important; }
      Kelas .prose sudah dihapus dari HTML, jadi style ini tidak diperlukan lagi.
    */
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        // Logika Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const overlay = document.getElementById('mobile-overlay');

        if (!sidebar || !toggleBtn || !closeBtn || !overlay) return;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden', 'opacity-0');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            setTimeout(() => {
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300); 
        }

        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
        
        // Hook untuk menutup sidebar saat navigasi (mobile only)
        Livewire.hook('message.processed', (message, component) => {
            if (message.updateQueue.some(update => update.type === 'call' && ['navigateToQuestion', 'nextQuestion', 'previousQuestion'].includes(update.method))) {
                if (window.matchMedia('(max-width: 767px)').matches) {
                    closeSidebar();
                }
            }
        });

        // Pastikan sidebar tertutup saat di desktop
        if (window.matchMedia('(min-width: 768px)').matches) {
             sidebar.classList.remove('-translate-x-full'); 
             overlay.classList.add('hidden'); 
        }

    });
</script>
@endpush