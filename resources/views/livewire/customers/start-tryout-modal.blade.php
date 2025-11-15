<div>
    <!-- Main Modal -->
    <div x-data="{ show: @entangle('showModal') }" 
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none"
         x-cloak
         x-show="show">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background Overlay -->
            <div x-show="show" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 aria-hidden="true">
            </div>

            <!-- Modal Panel -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-title">
                
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modal-title" class="text-lg font-bold text-gray-900">
                        Konfirmasi Mulai Tryout
                    </h3>
                    <button @click="show = false; $wire.closeModal()" 
                            type="button"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="mb-6">
                    @if($tryout)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-yellow-800 mb-2">Perhatian!</h4>
                                    <ul class="text-yellow-700 text-sm space-y-1">
                                        <li>• Tryout akan berlangsung selama <strong>{{ $tryout->duration }} menit</strong></li>
                                        <li>• Waktu tidak dapat dihentikan setelah dimulai</li>
                                        <li>• Pastikan koneksi internet stabil</li>
                                        <li>• Jawaban akan disimpan otomatis</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>Judul Tryout:</span>
                                <span class="font-semibold text-gray-800">{{ $tryout->title }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Durasi:</span>
                                <span class="font-semibold text-gray-800">{{ $tryout->duration }} menit</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Jumlah Soal:</span>
                                <span class="font-semibold text-gray-800">{{ $tryout->active_questions_count }} soal</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-4">
                            <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                            <p>Data tryout tidak ditemukan</p>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="flex gap-3 justify-end">
                    <button @click="show = false; $wire.closeModal()"
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        Batal
                    </button>
                    
                    <button wire:click="startTryout"
                            wire:loading.attr="disabled"
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="startTryout">
                            <i class="fas fa-play-circle mr-2"></i>Saya Mengerti & Lanjutkan
                        </span>
                        <span wire:loading wire:target="startTryout">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Memulai...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Local Storage -->
    @script
    <script>
        $wire.on('tryoutStarted', (data) => {
            // Simpan ke localStorage
            localStorage.setItem(`tryout_${data.tryout_id}`, JSON.stringify({
                started_at: data.started_at,
                ended_at: data.ended_at,
                last_updated: new Date().toISOString()
            }));

            // Juga simpan di sessionStorage untuk akses lebih cepat
            sessionStorage.setItem(`tryout_${data.tryout_id}`, JSON.stringify({
                started_at: data.started_at,
                ended_at: data.ended_at
            }));

            // Dispatch event untuk component lain
            window.dispatchEvent(new CustomEvent('tryout-timer-started', {
                detail: data
            }));
        });

        // Check local storage saat page load
        document.addEventListener('DOMContentLoaded', function() {
            // Anda bisa menambahkan logic untuk sync data dari local storage ke server
            // jika diperlukan saat page reload
        });
    </script>
    @endscript
</div>