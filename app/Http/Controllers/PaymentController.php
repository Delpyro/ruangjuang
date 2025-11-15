<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use App\Models\Transaction;
use App\Models\Tryout;
use App\Models\Bundle; // Import Model Bundle
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    // CATATAN: Metode createPayment ini biasanya diganti dengan Livewire Component (PaymentPage.php)
    // Saya hanya mempertahankan dan memperbaikinya agar dapat menerima Tryout ATAU Bundle
    public function createPayment(Request $request, $itemSlug)
    {
        $request->validate([
            'customer_details' => 'sometimes|array'
        ]);

        $user = Auth::user();

        // Coba cari sebagai Tryout
        $item = Tryout::where('slug', $itemSlug)->first();

        // Jika bukan Tryout, coba cari sebagai Bundle
        if (!$item) {
            $item = Bundle::where('slug', $itemSlug)->first();
        }

        if (!$item) {
            return redirect()->back()->with('error', 'Item yang akan dibeli tidak ditemukan.');
        }

        // Check if user already has access to this item
        if ($this->userHasItemAccess($user, $item)) {
            $route = $item instanceof Tryout ? route('tryout.detail', $item->slug) : route('bundle.detail', $item->slug);
            return redirect()->to($route)
                ->with('error', 'Anda sudah memiliki akses ke item ini.');
        }

        // Create transaction record first
        $transaction = Transaction::create([
            'id_user' => $user->id,
            'id_tryout' => $item instanceof Tryout ? $item->id : null,
            'id_bundle' => $item instanceof Bundle ? $item->id : null, // DITAMBAHKAN
            'amount' => $item->final_price, // Menggunakan final_price universal
            'status' => Transaction::STATUS_PENDING,
            'ip_user' => $request->ip(), // Tambahkan IP user
        ]);

        $result = $this->midtransService->createTransaction(
            $transaction, 
            $item, // Meneruskan item (Tryout atau Bundle)
            $user,
            $request->input('customer_details', [])
        );

        if (!$result['success']) {
            $transaction->delete();
            return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $result['error']);
        }

        // Karena kita menggunakan Livewire, ini mungkin tidak terpakai,
        // Tapi kita kembalikan view yang relevan (hanya simulasi)
        return view('customers.payment', [
            'snapToken' => $result['snap_token'],
            'transaction' => $transaction,
            'item' => $item
        ]);
    }

    /**
     * Midtrans Notification Callback Handler (Webhook).
     * HARUS selalu return 200 OK ke Midtrans
     */
    public function handleCallback(Request $request)
    {
        // Langsung panggil MidtransService untuk memproses dan meng-update DB
        $result = $this->midtransService->handleNotification($request->all());

        if ($result['success']) {
            \Log::info('✅ Callback processed successfully', [
                'order_id' => $request->input('order_id'),
                'status' => $result['transaction']->status ?? 'unknown',
            ]);
            
            return response()->json(['status' => 'success', 'message' => 'Notification processed successfully'], 200);
        }

        \Log::error('❌ Callback processing failed', [
            'order_id' => $request->input('order_id', 'N/A'),
            'error' => $result['error'] ?? 'Unknown error'
        ]);

        // Tetap return 200 OK meskipun ada error processing
        return response()->json(['status' => 'error', 'message' => 'Processing failed'], 200);
    }

    // Metode ini hanya untuk debugging, biasanya diadmin
    public function debugCallback(Request $request)
    {
        \Log::info('🐛 DEBUG Callback', $request->all());
        return $this->handleCallback($request);
    }
        
    /**
     * Midtrans Redirect Page: Success/Finish (Universal)
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('dashboard')->with('error', 'Order ID tidak valid.');
        }

        // Load transaksi dengan relasi universal
        $transaction = Transaction::where('order_id', $orderId)
            ->with(['tryout', 'bundle'])
            ->first();

        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Panggil service untuk memastikan status terbaru dan akses diberikan
        $this->midtransService->handleNotification(); 
        $transaction->refresh();
        
        $item = $transaction->item; // Menggunakan accessor $item
        
        // Tentukan rute kembali yang relevan
        $detailRoute = $this->getDetailRoute($item);

        return view('customers.payment-finish', [
            'transaction' => $transaction,
            'item' => $item,
            'status' => $transaction->status,
            'detailRoute' => $detailRoute
        ]);
    }

    /**
     * Midtrans Redirect Page: Pending (Universal)
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('dashboard')->with('error', 'Order ID tidak valid.');
        }

        $transaction = Transaction::where('order_id', $orderId)
            ->with(['tryout', 'bundle'])
            ->first();
        
        return view('customers.payment-pending', [
            'transaction' => $transaction,
            'item' => $transaction?->item
        ]);
    }

    /**
     * Midtrans Redirect Page: Error (Universal)
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        $errorMessage = $request->query('error_message', 'Terjadi kesalahan dalam pembayaran');

        $transaction = Transaction::where('order_id', $orderId)
            ->with(['tryout', 'bundle'])
            ->first();
        
        // Coba update status di DB
        if ($orderId) {
            $this->midtransService->handleNotification(); 
            $transaction?->refresh();
        }
        
        return view('customers.payment-error', [
            'transaction' => $transaction,
            'errorMessage' => $errorMessage,
            'item' => $transaction?->item
        ]);
    }
    
    // --- Helper Methods ---

    private function getDetailRoute($item): string
    {
        if ($item instanceof Tryout) {
            return route('tryout.detail', $item->slug);
        }
        if ($item instanceof Bundle) {
            return route('bundle.detail', $item->slug);
        }
        return route('dashboard');
    }

    /**
     * Cek apakah user sudah memiliki akses ke item (Tryout atau Bundle).
     */
    private function userHasItemAccess($user, $item): bool
    {
        // Logika kepemilikan sudah harus diimplementasikan di model User/Tryout/Bundle
        if ($item instanceof Tryout) {
            // Asumsi relasi user->purchasedTryouts ada
            return $user->purchasedTryouts()->where('tryout_id', $item->id)->exists();
        }
        
        if ($item instanceof Bundle) {
            // Asumsi relasi bundle->purchasers ada
            return $item->purchasers()->where('user_id', $user->id)->exists();
        }
        
        return false;
    }

    // Metode lain (checkStatus, cancelTransaction, manualCheckStatus)
    // Ditinggalkan karena biasanya ini adalah metode API atau Admin,
    // dan tidak memerlukan perubahan universal yang sama dengan redirect/callback.

    public function checkStatus($transactionId)
    {
        // ... (Logika checkStatus)
        $transaction = Transaction::where('order_id', $transactionId)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $result = $this->midtransService->getStatus($transaction->order_id);

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }
    
    public function cancelTransaction($orderId)
    {
        // ... (Logika cancelTransaction)
        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $result = $this->midtransService->cancelTransaction($orderId);

        if ($result['success']) {
            return response()->json(['message' => 'Transaction cancelled successfully']);
        }

        return response()->json(['error' => $result['error']], 400);
    }
}
