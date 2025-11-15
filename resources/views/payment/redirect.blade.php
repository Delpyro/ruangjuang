<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-primary mx-auto mb-6"></div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Mengarahkan ke Pembayaran</h2>
            <p class="text-gray-600 mb-6">Anda akan diarahkan ke halaman pembayaran Midtrans. Harap tunggu...</p>
            <p class="text-sm text-gray-500">Jika tidak otomatis redirect, klik tombol di bawah</p>
            
            <form action="{{ $redirect_url }}" method="POST" id="midtransForm">
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg font-medium mt-4">
                    Lanjutkan ke Pembayaran
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form after 2 seconds
    setTimeout(function() {
        document.getElementById('midtransForm').submit();
    }, 2000);
});
</script>