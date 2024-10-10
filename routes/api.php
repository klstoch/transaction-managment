<?php

use App\Http\Controllers\Api\V1\Transactions\TransactionController;
use App\Http\Controllers\Api\V1\User\AuthenticationController;
use App\Http\Controllers\Api\V1\User\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/register', [RegistrationController::class, 'register']);
    Route::post('/login', [AuthenticationController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/deposit', [TransactionController::class, 'deposit']);
        Route::post('/withdraw', [TransactionController::class, 'withdraw']);
        Route::post('/transfer', [TransactionController::class, 'transfer']);

        Route::get('/transactions', [TransactionController::class, 'transactionHistory']);

        Route::post('/logout', [AuthenticationController::class, 'logout']);
    });
});
