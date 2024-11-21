<?php

use App\Http\Controllers\PaystackController;
use App\Http\Controllers\PaystackCustomerController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/customers', [TransactionController::class, 'getCustomers']);
Route::post('/customers', [TransactionController::class, 'createCustomer']);
Route::patch('/customers/{customerId}', [TransactionController::class, 'updateCustomer']);

Route::post('/transactions', [TransactionController::class, 'createTransaction']);
Route::get('/transactions', [TransactionController::class, 'getTransaction']);
Route::get('/partner-transactions', [TransactionController::class, 'getPartnerTransactions']);
Route::patch('/aggre-transactions/{transactionId}', [TransactionController::class, 'aggreTransaction']);



//paystack
Route::get('/paystack/callback', [PaystackController::class, 'callback'])->name('paystack.callback');
Route::post('/paystack/initialize', [PaystackController::class, 'initialize']);
Route::get('/paystack/verify', [PaystackController::class, 'verify']);
Route::get('/paystack/list-transactions', [PaystackController::class, 'listTransactions']);
Route::get('/paystack/fetch-transaction/{id}', [PaystackController::class, 'getTransaction']);
// Route::get('/paystack/charge-authorization', [PaystackController::class, 'chargeAuthorization']);
//export transactions
Route::get('/paystack/export-transactions', [PaystackController::class, 'exportTransactions']);

//paystack customer
Route::post('/paystack/customer', [PaystackCustomerController::class, 'paystackCustomerCreate']);
Route::get('/paystack/list-customers', [PaystackCustomerController::class, 'listCustomers']);
Route::get('/paystack/fetch-customer/{code}', [PaystackCustomerController::class, 'getCustomer']);
Route::put('/paystack/update-customer/{id}', [PaystackCustomerController::class, 'updateCustomer']);