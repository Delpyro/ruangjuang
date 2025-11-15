<div class="min-h-screen bg-gray-50 py-12 mt-24">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="text-center mb-8" data-aos="fade-up">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Checkout Tryout</h1>
            <p class="text-gray-600">Lengkapi pembayaran untuk mengakses tryout</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Detail Tryout</h2>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $tryout->title }}</h3>
                            <p class="text-gray-600 text-sm">{{ $tryout->active_questions_count }} Soal</p>
                        </div>
                        <div class="text-right">
                            @if($tryout->discount > 0)
                                <div class="flex flex-col items-end">
                                    <span class="text-2xl font-bold text-primary">Rp {{ number_format($tryout->final_price, 0, ',', '.') }}</span>
                                    <span class="text-gray-400 line-through text-sm">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                                    <span class="text-green-600 text-sm font-medium">Diskon {{ $tryout->discount_percentage }}%</span>
                                </div>
                            @else
                                <span class="text-2xl font-bold text-primary">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Metode Pembayaran</h2>
                    
                    <form id="paymentForm" action="{{ route('payment.create', $tryout->slug) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Bank Transfer -->
                            <div class="payment-method-option">
                                <input type="radio" name="payment_method" value="bank_transfer" id="bank_transfer" class="hidden" required>
                                <label for="bank_transfer" class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                            <div class="w-3 h-3 rounded-full bg-primary hidden"></div>
                                        </div>
                                        <i class="fas fa-university text-xl text-gray-600"></i>
                                        <span class="font-medium">Transfer Bank</span>
                                    </div>
                                </label>
                            </div>

                            <!-- E-Wallet -->
                            <div class="payment-method-option">
                                <input type="radio" name="payment_method" value="ewallet" id="ewallet" class="hidden" required>
                                <label for="ewallet" class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                            <div class="w-3 h-3 rounded-full bg-primary hidden"></div>
                                        </div>
                                        <i class="fas fa-wallet text-xl text-gray-600"></i>
                                        <span class="font-medium">E-Wallet</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Credit Card -->
                            <div class="payment-method-option">
                                <input type="radio" name="payment_method" value="credit_card" id="credit_card" class="hidden" required>
                                <label for="credit_card" class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                            <div class="w-3 h-3 rounded-full bg-primary hidden"></div>
                                        </div>
                                        <i class="fas fa-credit-card text-xl text-gray-600"></i>
                                        <span class="font-medium">Kartu Kredit</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            id="payButton"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-4 px-6 rounded-xl transition-colors duration-300 flex items-center justify-center"
                            disabled
                        >
                            <i class="fas fa-lock mr-2"></i>
                            Bayar Rp {{ number_format($tryout->final_price, 0, ',', '.') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h3>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga Tryout</span>
                            <span class="text-gray-800">Rp {{ number_format($tryout->price, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($tryout->discount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Diskon</span>
                            <span class="text-green-600">-Rp {{ number_format($tryout->discount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        
                        <div class="border-t pt-3">
                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span class="text-primary">Rp {{ number_format($tryout->final_price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-semibold">Akses Instan</p>
                                <p class="mt-1">Setelah pembayaran berhasil, Anda dapat langsung mengakses tryout</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('.payment-method-option');
    const payButton = document.getElementById('payButton');
    
    paymentMethods.forEach(method => {
        const input = method.querySelector('input');
        const label = method.querySelector('label');
        const checkIcon = method.querySelector('.w-3.h-3');
        
        input.addEventListener('change', function() {
            // Reset all
            paymentMethods.forEach(m => {
                m.querySelector('label').classList.remove('border-primary', 'bg-blue-50');
                m.querySelector('.w-3.h-3').classList.add('hidden');
            });
            
            // Activate selected
            if (input.checked) {
                label.classList.add('border-primary', 'bg-blue-50');
                checkIcon.classList.remove('hidden');
                payButton.disabled = false;
            }
        });
    });
    
    // Form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
        this.submit();
    });
});
</script>

<style>
.payment-method-option input:checked + label {
    border-color: #3b82f6;
    background-color: #eff6ff;
}
</style>
@endpush