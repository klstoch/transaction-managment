<?php

use App\Http\Controllers\Api\V1\Transaction\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// routes/api.php

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/deposit', [TransactionController::class, 'deposit']);
    Route::post('/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/transfer', [TransactionController::class, 'transfer']);

    Route::get('/transactions', [TransactionController::class, 'transactionHistory']);
});

