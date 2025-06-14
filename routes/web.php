<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Customer\CustomerHomeController;
use App\Http\Controllers\Customer\CustomerBookingController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Customer\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');
Route::get('/about-us', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'sendContactForm'])->name('contact.send');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');

// Authentication Routes
Auth::routes(['verify' => true]);

// Customer Routes
Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::prefix('customer')->name('customer.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CustomerHomeController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', [CustomerHomeController::class, 'index'])->name('home');
        
        // Bookings
        Route::resource('bookings', CustomerBookingController::class);
        Route::patch('/bookings/{booking}/cancel', [CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/bookings/{booking}/review', [CustomerBookingController::class, 'reviewForm'])->name('bookings.review');
        Route::post('/bookings/{booking}/review', [CustomerBookingController::class, 'storeReview'])->name('bookings.review.store');
        
        // Profile Management
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
        
        // Payments
        Route::get('/payments/{booking}', [CustomerPaymentController::class, 'checkout'])->name('payments.show');
        Route::post('/payments/{booking}/process', [CustomerPaymentController::class, 'process'])->name('payments.process');
        Route::post('/payments/{booking}/upload', [CustomerPaymentController::class, 'uploadProof'])->name('payments.upload');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        
        // Customer Support
        Route::get('/support', [CustomerHomeController::class, 'supportIndex'])->name('support.index');
        Route::get('/support/create', [CustomerHomeController::class, 'supportCreate'])->name('support.create');
        Route::post('/support', [CustomerHomeController::class, 'supportStore'])->name('support.store');
        Route::get('/support/{ticket}', [CustomerHomeController::class, 'supportShow'])->name('support.show');
    });
});

// Include Admin Routes
require __DIR__.'/admin.php';
