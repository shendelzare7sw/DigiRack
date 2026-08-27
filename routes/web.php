<?php

use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\ProfileController;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $wishlistIds = [];
    if (Auth::check()) {
        $wishlistIds = Wishlist::where('user_id', Auth::id())
            ->pluck('product_id')
            ->toArray();
    }

    return view('welcome', compact('wishlistIds'));
})->name('home');

Route::get('/download-app', [PageController::class, 'downloadApp'])->name('pages.download-app');
Route::get('/tentang-digital-hook', [PageController::class, 'about'])->name('pages.about');
Route::get('/wilayah-pengantaran', [PageController::class, 'b2b'])->name('pages.b2b');
Route::get('/promo-spesial', [PageController::class, 'promos'])->name('pages.promos');
Route::get('/pusat-bantuan', [PageController::class, 'help'])->name('pages.help');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/syarat-dan-ketentuan', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/sitemap', [PageController::class, 'sitemap'])->name('pages.sitemap');

// Public API for locations & Webhooks
Route::get('/api/locations/provinces', [\App\Http\Controllers\Public\LocationController::class, 'getProvinces']);
Route::get('/api/locations/cities/{province_id}', [\App\Http\Controllers\Public\LocationController::class, 'getCities']);
Route::get('/api/locations/districts/{city}', [\App\Http\Controllers\Public\LocationController::class, 'getDistricts']);
Route::post('/api/midtrans/callback', [\App\Http\Controllers\Api\PaymentCallbackController::class, 'midtransCallback']);
// Public Product Routes (no login required)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}/reviews', [ProductController::class, 'reviews'])->name('products.reviews.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Public Storefront (no login required)
Route::get('/toko/{slug}/reviews', [\App\Http\Controllers\Public\StoreController::class, 'reviews'])->name('store.reviews.index');
Route::get('/toko/{slug}', [\App\Http\Controllers\Public\StoreController::class, 'show'])->name('store.show');

// User Recovery (public, no auth needed)
Route::get('/recovery', [\App\Http\Controllers\Auth\UserRecoveryController::class, 'showForm'])->name('user.recovery.form');
Route::post('/recovery', [\App\Http\Controllers\Auth\UserRecoveryController::class, 'store'])->name('user.recovery.store');

// Signed contact changes from verified email
Route::get('/profile/phone/confirm/{user}/{token}', [ProfileController::class, 'confirmPhoneChange'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('profile.phone.confirm');

// Admin Recovery (hidden / easter egg)
Route::post('/admin-recovery/unlock', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'unlock'])->name('admin.recovery.unlock');
Route::get('/admin-recovery', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'showForm'])->name('admin.recovery.form');
Route::post('/admin-recovery', [\App\Http\Controllers\Auth\AdminRecoveryController::class, 'reset'])->name('admin.recovery.reset');

// Auth-protected routes (Breeze profile)
Route::middleware('auth')->group(function () {
    // Smart dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('buyer.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Address Management
    Route::post('/profile/addresses', [\App\Http\Controllers\ProfileAddressController::class, 'store'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [\App\Http\Controllers\ProfileAddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [\App\Http\Controllers\ProfileAddressController::class, 'destroy'])->name('profile.addresses.destroy');
    Route::patch('/profile/addresses/{address}/primary', [\App\Http\Controllers\ProfileAddressController::class, 'setPrimary'])->name('profile.addresses.set-primary');

    Route::get('/profile/verifikasi-ktp', [\App\Http\Controllers\IdentityVerificationController::class, 'edit'])->name('profile.identity.edit');
    Route::post('/profile/verifikasi-ktp', [\App\Http\Controllers\IdentityVerificationController::class, 'update'])->name('profile.identity.update');
    Route::get('/profile/dokumen-identitas/{identityVerification}', \App\Http\Controllers\IdentityDocumentController::class)->name('profile.identity.document');

    // Notifications
    Route::get('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Termasuk file route berdasarkan peran
require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/admin.php';
