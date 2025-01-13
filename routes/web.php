<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AwsTest;
use App\Http\Controllers\AwsTest2;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\Dashboard;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/textTract', [AwsTest::class, 'textTract']);
Route::get('/textTract2', [AwsTest2::class, 'processDocument']);

Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');

Route::get('/purchaseInvoice', [PurchaseInvoiceController::class, 'index'])->name('purchaseInvoice');
Route::get('/purchaseInvoiceShow/{id}', [PurchaseInvoiceController::class, 'show'])->name('purchaseInvoiceShow');
Route::get('/purchaseInvoiceCreate', [PurchaseInvoiceController::class, 'create'])->name('purchaseInvoiceCreate');
Route::post('/purchaseInvoiceStore', [PurchaseInvoiceController::class, 'store'])->name('purchaseInvoice.upload');
