<?php

use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public API for locations & Webhooks
Route::get('/api/locations/provinces', [\App\Http\Controllers\Public\LocationController::class, 'getProvinces']);
Route::get('/api/locations/cities/{province_id}', [\App\Http\Controllers\Public\LocationController::class, 'getCities']);
Route::post('/api/midtrans/callback', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'midtransCallback']);

// Public Product Routes (no login required)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Auth-protected routes (Breeze profile)
Route::middleware('auth')->group(function () {
    // Smart dashboard redirect based on role
    Route::get('/dashboard', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'seller' => redirect()->route('seller.dashboard'),
            default => redirect()->route('buyer.dashboard'),
        };
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Termasuk file route berdasarkan peran
require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/seller.php';
require __DIR__.'/admin.php';

