<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// --- Guest ---------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// --- Authenticated -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('bank-accounts', BankAccountController::class)->except('show');

    Route::get('exchange-rates', [ExchangeRateController::class, 'index'])->name('exchange-rates.index');
    Route::post('exchange-rates', [ExchangeRateController::class, 'store'])->name('exchange-rates.store');
    Route::delete('exchange-rates/{exchangeRate}', [ExchangeRateController::class, 'destroy'])
        ->name('exchange-rates.destroy');

    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])->name('invoices.mark-sent');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::delete('invoices/{invoice}/payments/{payment}', [PaymentController::class, 'destroy'])
        ->name('payments.destroy');

    Route::get('settings', [CompanySettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [CompanySettingController::class, 'update'])->name('settings.update');
});
