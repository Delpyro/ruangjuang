<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle Midtrans notification (webhook).
     */
    public function handle(Request $request)
    {
        Log::info('🔄 Midtrans Callback Received', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        try {
            $notificationData = $request->all();
            $result = $this->midtransService->handleNotification($notificationData);

            if ($result['success']) {
                Log::info('✅ Callback processed successfully', [
                    'order_id' => $result['transaction']->order_id ?? 'unknown',
                    'updated' => $result['updated'] ?? false
                ]);
            } else {
                Log::error('❌ Callback processing failed internally', [
                    'order_id' => $notificationData['order_id'] ?? 'unknown',
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Notification acknowledged.'
            ]);

        } catch (\Exception $e) {
            Log::error('🔥 FATAL Exception in PaymentCallbackController', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Notification acknowledged but encountered a server error.'
            ]);
        }
    }

    /**
     * Halaman redirect setelah user menyelesaikan pembayaran.
     * SELALU sync dengan Midtrans saat diakses
     */
    public function finish(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('tryout.index')->with('error', 'Sesi pembayaran tidak valid.');
        }

        $transaction = Transaction::where('order_id', $orderId)->with(['tryout', 'user'])->firstOrFail();

        // SELALU sync status dengan Midtrans setiap kali halaman diakses
        Log::info("🔄 Finish page sync for order: {$orderId}");
        try {
            $statusResult = $this->midtransService->getStatus($orderId);
            if ($statusResult['success']) {
                $this->midtransService->handleNotification((array)$statusResult['response']);
                $transaction->refresh(); // Refresh data terbaru dari database
            }
        } catch (\Exception $e) {
            Log::warning("Failed to sync status on finish page for order {$orderId}", ['error' => $e->getMessage()]);
        }

        // Tentukan status untuk view berdasarkan status terbaru
        $status = 'error'; // Default untuk gagal
        
        if ($transaction->isSuccess()) { // isSuccess() mengecek status 'settlement'
            $status = Transaction::STATUS_SETTLEMENT; // 'settlement'
        } elseif ($transaction->isPending()) {
            $status = Transaction::STATUS_PENDING; // 'pending'
        } elseif ($transaction->status === Transaction::STATUS_CAPTURE) {
            $status = Transaction::STATUS_CAPTURE; // 'capture'
        }
        
        return view('customers.payment-finish', [
            'transaction' => $transaction,
            'status' => $status
        ]);
    }

    /**
     * Halaman redirect jika pembayaran masih pending
     * SELALU sync dengan Midtrans saat diakses
     */
    public function pending(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('tryout.index')->with('error', 'Sesi pembayaran tidak valid.');
        }
        
        $transaction = Transaction::where('order_id', $orderId)->with('tryout')->firstOrFail();

        // SELALU sync status dengan Midtrans setiap kali halaman diakses
        Log::info("🔄 Pending page sync for order: {$orderId}");
        try {
            $statusResult = $this->midtransService->getStatus($orderId);
            if ($statusResult['success']) {
                $this->midtransService->handleNotification((array)$statusResult['response']);
                $transaction->refresh(); // Refresh data terbaru dari database
            }
        } catch (\Exception $e) {
            Log::warning("Failed to sync status on pending page for order {$orderId}", ['error' => $e->getMessage()]);
        }

        return view('customers.payment-finish', [
            'transaction' => $transaction,
            'status' => $transaction->isPending() ? Transaction::STATUS_PENDING : $transaction->status
        ]);
    }

    /**
     * Halaman redirect jika terjadi error saat pembayaran
     * SELALU sync dengan Midtrans saat diakses
     */
    public function error(Request $request)
    {
        $orderId = $request->query('order_id');
        
        if (!$orderId) {
            return redirect()->route('tryout.index')->with('error', 'Sesi pembayaran tidak valid.');
        }
        
        $transaction = Transaction::where('order_id', $orderId)->with('tryout')->firstOrFail();

        // SELALU sync status dengan Midtrans setiap kali halaman diakses
        Log::info("🔄 Error page sync for order: {$orderId}");
        try {
            $statusResult = $this->midtransService->getStatus($orderId);
            if ($statusResult['success']) {
                $this->midtransService->handleNotification((array)$statusResult['response']);
                $transaction->refresh(); // Refresh data terbaru dari database
            }
        } catch (\Exception $e) {
            Log::warning("Failed to sync status on error page for order {$orderId}", ['error' => $e->getMessage()]);
        }

        return view('customers.payment-finish', [
            'transaction' => $transaction,
            'status' => $transaction->isFailed() ? 'error' : $transaction->status
        ]);
    }
}