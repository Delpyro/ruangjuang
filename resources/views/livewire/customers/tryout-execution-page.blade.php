<div x-data="{
    // Inisialisasi dari Livewire
    endTime: @js($endTime ?? 0),
    isExpired: @js($isExpired),
    currentTime: Date.now(),
    remainingTime: 0,
    timerInterval: null,

    // Fungsi Timer
    startTimer() {
        if (this.isExpired || this.endTime === 0) return;

        this.timerInterval = setInterval(() => {
            this.currentTime = Date.now();
            this.remainingTime = Math.max(0, this.endTime - this.currentTime);

            if (this.remainingTime === 0) {
                this.isExpired = true;
                clearInterval(this.timerInterval);
                // Panggil submit paksa Livewire jika waktu habis
                @this.submitTryout(true);
            }
        }, 1000);
    },

    formatTime(ms) {
        if (ms <= 0) return '00:00:00';
        const totalSeconds = Math.floor(ms / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        return [hours, minutes, seconds]
            .map(t => String(t).padStart(2, '0'))
            .join(':');
    },

    // Inisialisasi pada load
    init() {
        this.startTimer();
    }
}" x-init="init()" class="py-12 mt-24">
    
    {{-- Safety Check --}}
    @if(!$tryout || !$userTryout || $userTryout->is_completed)
        <div class="container mx-auto px-4"><div class="text-center bg-white p-8 rounded-xl shadow-lg mt-10">
            <h3 class="text-2xl font-bold text-red-500 mb-2">Sesi Sudah Selesai</h3>
            <p class="text-gray-600">Anda akan diarahkan ke halaman hasil.</p>
            <script>setTimeout(() => { window.location.href = '{{ route('tryout.result', ['tryout' => $tryout->slug ?? '', 'attempt' => $userTryout->attempt ?? 1]) }}'; }, 3000);</script>
        </div></div>
        @return
    @endif
    
    <div class="container mx-auto px-4">
        
        {{-- Header Timer & Info --}}
        <div class="bg-white rounded-2xl shadow-xl p-4 mb-6 sticky top-20 z-40">
            <div class="flex flex-wrap justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800 truncate max-w-xs">
                    {{ $tryout->title }} (Att. {{ $userTryout->attempt }})
                </h2>
                
                <div :class="{'text-red-600 animate-pulse': remainingTime < 300000 && remainingTime > 0, 'text-gray-700': remainingTime >= 300000 || remainingTime === 0}" class="flex items-center font-extrabold text-xl">
                    <i class="fas fa-clock mr-2"></i>
                    <span x-text="formatTime(remainingTime)">00:00:00</span>
                    <span x-show="isExpired" class="ml-2 text-sm text-red-600 font-bold">WAKTU HABIS</span>
                </div>

                <button 
                    wire:click="$dispatch('show-submit-modal')"
                    class="bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors disabled:opacity-50"
                    x-bind:disabled="isExpired"
                >
                    <i class="fas fa-paper-plane mr-2"></i> Selesaikan Tes
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            
            {{-- Question Area --}}
            <div class="lg:w-3/4">
                @if($currentQuestion)
                    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                        <div class="flex justify-between items-center mb-4 border-b pb-3">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Soal No. {{ $currentQuestionIndex + 1 }} / {{ $stats['totalQuestions'] }}
                            </h3>
                            <button wire:click="toggleDoubtful({{ $currentQuestion->id }})" 
                                    class="text-sm font-medium transition-colors disabled:opacity-50"
                                    x-bind:disabled="isExpired">
                                <span class="px-3 py-1 rounded-full shadow-md"
                                      :class="{'text-white bg-yellow-500 hover:bg-yellow-600': @js($userAnswers[$currentQuestion->id]['is_doubtful'] ?? false), 
                                                'text-yellow-600 hover:text-yellow-700 bg-yellow-100': !@js($userAnswers[$currentQuestion->id]['is_doubtful'] ?? false)}">
                                    <i class="fas fa-question-circle mr-1"></i> 
                                    Ragu-Ragu
                                </span>
                            </button>
                        </div>
                        
                        {{-- Question Content --}}
                        <div class="mb-6 text-gray-700 leading-relaxed prose max-w-none">
                            {!! $currentQuestion->question !!}
                        </div>

                        {{-- Answer Options --}}
                        <div class="space-y-3">
                            @foreach($currentQuestion->answers as $answer)
                                <label class="block cursor-pointer bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-primary transition-all duration-200"
                                       :class="{'bg-primary-50 border-primary shadow-md': {{ $userAnswers[$currentQuestion->id]['answer_id'] ?? 0 }} == {{ $answer->id }}}">
                                    <input 
                                        type="radio" 
                                        name="answer_{{ $currentQuestion->id }}" 
                                        value="{{ $answer->id }}" 
                                        wire:change="saveAnswer({{ $currentQuestion->id }}, $event.target.value)"
                                        class="mr-3 text-primary focus:ring-primary h-5 w-5"
                                        :checked="{{ $userAnswers[$currentQuestion->id]['answer_id'] ?? 0 }} == {{ $answer->id ? 'true' : 'false' }}"
                                        x-bind:disabled="isExpired"
                                    >
                                    <span class="text-gray-700">{!! $answer->content !!}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    {{-- Navigation Buttons --}}
                    <div class="flex justify-between mt-4">
                        <button wire:click="goToQuestion({{ $currentQuestionIndex - 1 }})" 
                                x-bind:disabled="{{ $currentQuestionIndex === 0 ? 'true' : 'false' }} || isExpired"
                                class="bg-gray-300 text-gray-800 px-6 py-2 rounded-lg font-medium hover:bg-gray-400 transition disabled:opacity-50">
                            <i class="fas fa-arrow-left mr-2"></i> Sebelumnya
                        </button>
                        
                        <button wire:click="goToQuestion({{ $currentQuestionIndex + 1 }})" 
                                x-bind:disabled="{{ $currentQuestionIndex === $stats['totalQuestions'] - 1 ? 'true' : 'false' }} || isExpired"
                                class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-dark transition disabled:opacity-50">
                            Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                @else
                     <div class="text-center text-red-500">Soal tidak ditemukan.</div>
                @endif
            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:w-1/4">
                <div class="bg-white rounded-2xl shadow-xl p-5 sticky top-40">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Navigasi Soal</h3>
                    
                    {{-- Quick Stats --}}
                    <div class="grid grid-cols-2 gap-3 mb-6 text-sm">
                        <div class="bg-green-100 text-green-800 p-2 rounded-lg text-center">Jawab: {{ $stats['answeredCount'] }}</div>
                        <div class="bg-red-100 text-red-800 p-2 rounded-lg text-center">Belum: {{ $stats['unansweredCount'] }}</div>
                        <div class="bg-yellow-100 text-yellow-800 p-2 rounded-lg text-center">Ragu: {{ $stats['doubtfulCount'] }}</div>
                        <div class="bg-gray-200 text-gray-700 p-2 rounded-lg text-center">Total: {{ $stats['totalQuestions'] }}</div>
                    </div>

                    {{-- Question Map --}}
                    <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto pr-2">
                        @foreach($questions as $index => $question)
                            @php
                                $status = 'bg-gray-100 text-gray-700'; // Default Belum Jawab
                                $userAnswerData = $userAnswers[$question->id] ?? ['answer_id' => null, 'is_doubtful' => false];
                                
                                if ($userAnswerData['is_doubtful']) {
                                    $status = 'bg-yellow-500 text-white';
                                } elseif ($userAnswerData['answer_id']) {
                                    $status = 'bg-green-500 text-white';
                                }
                                
                                if ($index === $currentQuestionIndex) {
                                    $status .= ' ring-4 ring-primary ring-offset-2';
                                }
                            @endphp
                            <button wire:click="goToQuestion({{ $index }})" 
                                    class="w-full h-10 rounded-lg font-medium transition-all duration-200 {{ $status }}"
                                    x-bind:disabled="isExpired">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Submission Confirmation Modal --}}
    <div x-data="{ showModal: false }" 
        x-on:show-submit-modal.window="showModal = true"
        x-show="showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4"
        style="display: none;">
        <div x-on:click.outside="showModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 text-center">
                <i class="fas fa-exclamation-circle text-yellow-500 text-6xl mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Selesaikan Tryout?</h3>
                <p class="text-gray-600 mb-4">Anda yakin ingin menyelesaikan pengerjaan Tryout **{{ $tryout->title }}**?</p>
                
                @if($stats['unansweredCount'] > 0)
                    <p class="text-red-500 font-bold mb-4">
                        PERHATIAN: Ada {{ $stats['unansweredCount'] }} soal yang belum terjawab!
                    </p>
                @endif
                
                <p class="text-gray-800 font-semibold text-sm">
                    Jawaban terjawab: {{ $stats['answeredCount'] }} / {{ $stats['totalQuestions'] }}
                </p>
                <p class="text-gray-800 font-semibold text-sm mb-4">
                    Waktu tersisa: <span x-text="formatTime(remainingTime)">00:00:00</span>
                </p>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <button
                    type="button"
                    x-on:click="showModal = false"
                    class="px-5 py-2 rounded-lg text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50"
                >
                    Kembali Mengerjakan
                </button>
                <button
                    type="button"
                    wire:click="submitTryout(false)"
                    wire:loading.attr="disabled"
                    wire:target="submitTryout"
                    class="px-5 py-2 rounded-lg text-sm font-medium bg-red-600 text-white hover:bg-red-700 disabled:opacity-75 disabled:cursor-wait inline-flex items-center"
                >
                    <span wire:loading.remove wire:target="submitTryout">
                        Ya, Selesaikan Sekarang
                    </span>
                    <span wire:loading wire:target="submitTryout">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                        Menyelesaikan...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
