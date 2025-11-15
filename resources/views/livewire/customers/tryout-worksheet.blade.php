<div class="flex flex-col h-screen overflow-hidden">

    {{-- TOP HEADER GLOBAL (Nama User dan Timer Desktop) --}}
    <div class="flex-shrink-0 bg-white shadow z-10 border-b border-gray-200">
        <div class="flex justify-between items-center py-1 px-4 md:py-2">
            
            {{-- NAMA USER (Pojok Kiri) --}}
            <div class="text-sm md:text-base font-semibold text-gray-700 truncate max-w-[50%] flex items-center space-x-2">
                <i class="fas fa-user-circle text-lg text-blue-600"></i>
                <span class="hidden sm:inline">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </span>
                <span class="inline sm:hidden">
                    {{ Auth::user()->name ?? 'Pengguna' }}
                </span>
            </div>

            {{-- PERUBAHAN 1: Menggunakan 'bg-timer-blue' dari config --}}
            <div id="timer" class="text-base md:text-xl font-semibold bg-timer-blue text-white px-3 md:px-4 py-1 rounded shadow-lg transition duration-500 ease-in-out" wire:ignore>
                --:--
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT AREA (Sidebar dan Soal) --}}
    <div id="main-container" class="flex flex-1 relative overflow-hidden">

        <div id="mobile-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden transition-opacity duration-300 opacity-0"></div>

        {{-- SIDEBAR (DAFTAR SOAL) --}}
        <div id="sidebar"
             class="fixed inset-y-0 left-0 z-20 w-64 bg-white border-r overflow-y-scroll p-4 shadow-xl
                    transform -translate-x-full transition-transform duration-300 ease-in-out
                    md:relative md:transform-none md:w-64 md:shadow-none md:flex-shrink-0">
                    
            <div class="flex justify-between items-center mb-4 md:hidden">
                <h3 class="font-bold text-lg text-gray-800">Daftar Soal</h3>
                <button id="close-sidebar-btn" class="text-gray-500 hover:text-gray-900 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2">
                {{-- Loop Daftar Soal --}}
                @for ($index = 0; $index < $totalQuestions; $index++)
                    <button
                        wire:click="navigateToQuestion({{ $index }})"
                        class="w-full h-10 rounded text-white font-semibold flex items-center justify-center text-sm
                               hover:opacity-80 transition-all duration-150
                               {{ $this->getQuestionStatusClass($index) }}
                               
                               @if($currentQuestionIndex === $index)
                                   border-2 border-[#03A9F4] ring-2 ring-[rgba(3,169,244,0.5)]
                               @endif
                               ">
                        {{ $index + 1 }}
                    </button>
                @endfor
            </div>
        </div>

        {{-- MAIN CONTENT (AREA SOAL) --}}
        <div class="flex-1 flex flex-col relative overflow-y-auto bg-gray-50">

            {{-- HEADER DI ATAS SOAL (Hanya Tombol Toggle Mobile) --}}
            <div class="flex-shrink-0 bg-white border-b border-gray-200 flex justify-between items-center px-4 py-3 md:px-6 shadow md:hidden">

                {{-- Tombol Toggle Sidebar (Mobile Only) --}}
                <button id="toggle-sidebar-btn" class="bg-[#2563EA] hover:bg-[#1a47b3] px-3 py-1 rounded flex items-center gap-2 text-white text-sm font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    Daftar Soal
                </button>

                {{-- Kategori Soal Mobile --}}
                <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-semibold text-sm">
                        Soal No. {{ $currentQuestionIndex + 1 }}
                </div>
            </div>

            {{-- AREA SOAL YANG BISA DI-SCROLL --}}
            <div class="flex-1 py-4 md:py-6 px-4 md:px-6">
                @if ($currentQuestion)

                    {{-- PROGRESS BAR --}}
                    <div class="mb-3 relative w-full bg-gray-300 h-4 flex items-center justify-center overflow-hidden">
                        <div class="bg-[#2563EA] h-full absolute top-0 left-0 transition-all duration-500" style="width: {{ $progressPercent }}%;"></div>
                        <span class="text-sm font-semibold z-10 text-black shadow-text">
                            {{ round($progressPercent) }}%
                        </span>
                    </div>

                    {{-- Judul & Kategori (Header Biru) --}}
                    <div class="bg-[#2563EA] text-white p-4 rounded-lg mb-4">
                        <h4 class="font-bold text-lg uppercase">
                            {{ $currentQuestion->category->name ?? 'Kategori' }} ({{ substr($currentQuestion->category->name ?? 'Kategori', 0, 3) }}) - {{ $currentQuestion->subCategory->name ?? 'Subkategori' }}
                        </h4>
                   </div>

                    <div class="p-0 bg-gray-50 w-full"
                         wire:key="question-{{ $currentQuestion->id }}"
                         x-data="{ 
                            selected: @entangle('selectedAnswerId').live, 
                            doubtful: @entangle('isDoubtful').live 
                         }"
                    >

                        {{-- Pertanyaan --}}
                        <div class="mb-6 text-gray-800 text-base md:text-lg">
                            <span class="float-left mr-2">{{ $currentQuestionIndex + 1 }}.</span>
                            <div class="overflow-x-auto">{!! $currentQuestion->question !!}</div>
                        </div>

                        {{-- Gambar Soal --}}
                        @if ($currentQuestion->image)
                            <div class="my-4 p-2 border rounded-md bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $currentQuestion->image) }}" alt="Gambar Soal" class="max-w-full h-auto rounded-md mx-auto">
                            </div>
                        @endif

                        {{-- Pilihan Jawaban --}}
                        <div class="space-y-2 text-gray-700">
                            @foreach ($currentQuestion->answers as $answer)
                                <label
                                    class="flex items-start space-x-3 cursor-pointer p-3 
                                           rounded-lg
                                           hover:bg-gray-100
                                    "
                                >
                                    <input type="radio"
                                           x-model="selected"
                                           @click="doubtful = false"
                                           wire:loading.attr="disabled" 
                                           name="jawaban_{{ $currentQuestion->id }}"
                                           value="{{ $answer->id }}"
                                           class="radio-custom-blue"> 

                                    <div class="prose max-w-none w-full text-base md:text-lg text-gray-800">
                                        {!! $answer->answer !!}
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- Tombol Navigasi Bawah --}}
                        <div class="mt-8 flex flex-wrap justify-start items-center gap-3">

                            <button
                                wire:click="previousQuestion"
                                @if($currentQuestionIndex == 0) disabled @endif
                                class="bg-[#2563EA] text-white font-semibold px-4 py-2 rounded-lg shadow-md min-w-[120px] h-10
                                       {{ $currentQuestionIndex == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#1a47b3]' }}">
                                Sebelumnya
                            </button>

                            <label class="flex items-center gap-2 cursor-pointer px-4 py-2">
                                <input type="checkbox"
                                       x-model="doubtful"
                                       class="text-ragu-ragu w-5 h-5 border-ragu-ragu rounded focus:ring-ragu-ragu">
                                <span class="text-gray-700 font-medium text-sm">Ragu-ragu</span>
                            </label>

                            @if ($currentQuestionIndex < $totalQuestions - 1)
                                <button
                                    wire:click="saveAndNext"
                                    class="bg-[#2563EA] hover:bg-[#1a47b3] text-white font-semibold px-4 py-2 rounded-lg shadow-md min-w-[140px] h-10">
                                    Simpan & Lanjutkan
                                </button>
                            @else
                                <button
                                    wire:click.prevent="showFinishConfirmation" 
                                    class="bg-[#2563EA] hover:bg-[#1a47b3] text-white font-semibold px-4 py-2 rounded-lg shadow-md min-w-[140px] h-10">
                                    Simpan dan Kumpulkan
                                </button>
                            @endif

                        </div>
                    </div> 
                @else
                <div class="p-6 border rounded-lg shadow-lg bg-white mx-auto text-center">
                    <p class="text-lg font-semibold text-gray-700">Gagal memuat soal. Silakan muat ulang halaman.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

---

@push('styles')
<style>
    /* PERUBAHAN 2: Nama keyframe unik */
    @keyframes blink-timer-animation {
        0%, 100% { background-color: white; color: #EF4444; }
        50% { background-color: #EF4444; color: white; }
    }
    .blink {
        animation: blink-timer-animation 1s infinite;
    }

    .shadow-text {
        text-shadow: 0 0 2px rgba(255, 255, 255, 0.7);
    }
    .radio-custom-blue {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        border: 2px solid #D1D5DB;
        background-color: white;
        flex-shrink: 0;
        margin-top: 0.25rem;
        cursor: pointer;
    }
    .radio-custom-blue:checked {
        background-color: #2563EA !important;
        border-color: #2563EA !important;
        box-shadow: inset 0 0 0 4px white;
    }
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
    input[type="checkbox"].text-ragu-ragu:checked {
        background-color: #F9A825 !important;
        border-color: #F9A825 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        background-size: 1em 1em;
        background-position: center center;
        background-repeat: no-repeat;
    }
</style>
@endpush

---

{{-- PERUBAHAN 3: Seluruh blok <script> diperbarui --}}
@push('scripts')
<script>
    // Definisikan interval timer di global scope agar bisa di-clear
    window.appTimerInterval = null;

    function initializeTimer() {
        const endTime = @js($endTime); 
        // Guard clause jika endTime tidak ada
        if (!endTime) {
            console.error('endTime tidak terdefinisi.');
            return;
        }

        const targetTime = new Date(endTime).getTime();
        const timerEl = document.getElementById('timer');

        if (!timerEl) {
            console.error('Elemen Timer tidak ditemukan.');
            return;
        }

        // Hapus interval lama JIKA ada, ini penting
        if (window.appTimerInterval) {
            clearInterval(window.appTimerInterval);
        }

        function updateTimer() {
            const now = new Date().getTime();
            const distance = targetTime - now;

            if (distance > 0) {
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                const timeText = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                timerEl.textContent = timeText;
                
                // INI LOGIKA KEDIP-KEDIPNYA
                if (distance <= 600000) { // 10 menit
                    timerEl.classList.add('blink');
                    timerEl.classList.remove('bg-timer-blue'); 
                    timerEl.classList.remove('text-white'); // Hapus text-white agar warna dari animasi (merah) bisa terlihat
                } else {
                    timerEl.classList.remove('blink');
                    timerEl.classList.add('bg-timer-blue');
                    timerEl.classList.add('text-white'); // Kembalikan text-white
                }
            } else {
                // WAKTU HABIS
                clearInterval(window.appTimerInterval);
                timerEl.textContent = '00:00';
                timerEl.classList.remove('blink'); 
                
                // Panggil finishExam di controller
                // Ini akan memicu redirect ke halaman hasil
                @this.call('finishExam');
            }
        }

        updateTimer(); // Panggil sekali saat init
        window.appTimerInterval = setInterval(updateTimer, 1000); // Simpan ID interval
    }

    function setupSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');
        const overlay = document.getElementById('mobile-overlay');

        if (!sidebar || !toggleBtn || !closeBtn || !overlay) {
            return;
        }

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
            document.body.style.overflow = '';
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
        }

        // Hapus event listener lama jika ada (mencegah duplikat)
        toggleBtn.removeEventListener('click', openSidebar);
        closeBtn.removeEventListener('click', closeSidebar);
        overlay.removeEventListener('click', closeSidebar);

        // Tambah event listener baru
        toggleBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Hook ini *aman* di dalam listener, karena Livewire mengelolanya
        Livewire.hook('message.processed', (message, component) => {
            if (message.updateQueue.some(update => update.type === 'call' && ['navigateToQuestion', 'saveAndNext', 'previousQuestion'].includes(update.method))) {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            }
        });
    }

    function setupCustomAlerts() {
        // Listener Livewire.on aman untuk dipanggil ulang
        Livewire.on('show-finish-alert', () => {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: "Yakin Selesaikan Ujian?",
                    text: "Pastikan semua soal sudah dijawab. Anda akan diarahkan ke halaman hasil Tryout.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#2563EA",
                    cancelButtonColor: "#EF4444",
                    confirmButtonText: "Ya, Selesaikan!",
                    cancelButtonText: "Batal, Cek Lagi",
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('finishExam'); 
                    }
                });
            } else {
                if(confirm("Anda yakin ingin menyelesaikan ujian ini? Setelah ini, Anda akan diarahkan ke hasil Tryout.")) {
                    @this.call('finishExam');
                }
            }
        });
    }

    // Fungsi utama untuk menginisialisasi semua skrip di halaman ini
    function initWorksheetPage() {
        initializeTimer();
        setupSidebar();
        setupCustomAlerts();
    }

    // Jalankan skrip saat halaman dimuat pertama kali
    document.addEventListener('DOMContentLoaded', initWorksheetPage);

    // Jalankan skrip SETIAP KALI Livewire selesai menavigasi halaman
    document.addEventListener('livewire:navigated', initWorksheetPage);

</script>
@endpush