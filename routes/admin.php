<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CarController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SupportController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are protected by the admin middleware to ensure only admin users
| can access them.
|
*/

// The issue was here - we need to apply middleware FIRST before route grouping
Route::middleware(['web', 'auth', 'admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', function() { return redirect()->route('admin.dashboard'); });
        
        // Cars
        Route::resource('cars', CarController::class);
        
        // Bookings
        Route::resource('bookings', BookingController::class);
        Route::post('/bookings/{booking}/confirm', [BookingController::class, 'approve'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'reject'])->name('bookings.cancel');
        Route::get('/bookings/export', [BookingController::class, 'export'])->name('bookings.export');
        
        // Customers
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/{customer}/activate', [CustomerController::class, 'toggleStatus'])->name('customers.activate');
        Route::post('/customers/{customer}/deactivate', [CustomerController::class, 'toggleStatus'])->name('customers.deactivate');
        Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
        
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify.process');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'reject'])->name('payments.refund');
        Route::get('/payments/{payment}/invoice', [PaymentController::class, 'downloadProof'])->name('payments.invoice');
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
        
        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        
        // Quick Stats
        Route::get('/quick-stats', [DashboardController::class, 'quickStats'])->name('quick-stats');
    });
});
