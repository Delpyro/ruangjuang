<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\MidtransService;

class PaymentStatusController extends Controller
{
    public function show($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
        
        return response()->json([
            'order_id' => $transaction->order_id,
            'status' => $transaction->status,
            'amount' => $transaction->amount,
            'payment_method' => $transaction->payment_method,
            'created_at' => $transaction->created_at,
            'settlement_time' => $transaction->settlement_time
        ]);
    }
}