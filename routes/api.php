<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::post('/midtrans/callback', [PaymentController::class, 'handleCallback']);
Route::get('/midtrans/check-status/{orderId}', [PaymentController::class, 'manualStatusCheck']);
// routes/api.php
Route::post('/midtrans/debug', [PaymentController::class, 'debugCallback']);