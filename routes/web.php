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
Route::post('/api/ongkir/calculate', [\App\Http\Controllers\Api\OngkirController::class, 'calculate']);
// Public Product Routes (no login required)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Public Storefront (no login required)
Route::get('/toko/{slug}', [\App\Http\Controllers\Public\StoreController::class, 'show'])->name('store.show');

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
    
    // Switch Role Endpoint
    Route::get('/switch-role/{role}', [\App\Http\Controllers\RoleSwitchController::class, 'switchRole'])->name('switch.role');

    // Notifications
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Termasuk file route berdasarkan peran
require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/seller.php';
require __DIR__.'/admin.php';

