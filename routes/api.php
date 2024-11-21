<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/customers', [TransactionController::class, 'createCustomer']);

Route::post('/transactions', [TransactionController::class, 'createTransaction']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);