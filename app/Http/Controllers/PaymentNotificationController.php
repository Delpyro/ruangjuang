<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\MidtransService;

class PaymentNotificationController extends Controller
{
    public function handle(Request $request)
    {
        $midtransService = new MidtransService();
        
        $notification = $request->all();
        
        $transaction = Transaction::where('order_id', $notification['order_id'])->first();
        
        if ($transaction) {
            $transaction->update([
                'transaction_id' => $notification['transaction_id'],
                'status' => $notification['transaction_status'],
                'payment_type' => $notification['payment_type'],
                'fraud_status' => $notification['fraud_status'],
                'settlement_time' => $notification['settlement_time'] ?? null,
            ]);
        }
        
        return response()->json(['status' => 'success']);
    }
}