<?php

use App\Http\Controllers\Api\PaymentCallbackController;
use App\Http\Controllers\Auth\AdminRecoveryController;
use App\Http\Controllers\Auth\UserRecoveryController;
use App\Http\Controllers\IdentityDocumentController;
use App\Http\Controllers\IdentityVerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileAddressController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\LocationController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\ProductController;
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
Route::get('/api/locations/provinces', [LocationController::class, 'getProvinces']);
Route::get('/api/locations/cities/{province_id}', [LocationController::class, 'getCities']);
Route::get('/api/locations/districts/{city}', [LocationController::class, 'getDistricts']);
Route::post('/api/midtrans/callback', [PaymentCallbackController::class, 'midtransCallback']);
// Public Product Routes (no login required)
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}/reviews', [ProductController::class, 'reviews'])->name('products.reviews.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// User Recovery (public, no auth needed)
Route::get('/recovery', [UserRecoveryController::class, 'showForm'])->name('user.recovery.form');
Route::post('/recovery', [UserRecoveryController::class, 'store'])->name('user.recovery.store');

// Signed contact changes from verified email
Route::get('/profile/phone/confirm/{user}/{token}', [ProfileController::class, 'confirmPhoneChange'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('profile.phone.confirm');

// Admin Recovery (hidden / easter egg)
Route::post('/admin-recovery/unlock', [AdminRecoveryController::class, 'unlock'])->name('admin.recovery.unlock');
Route::get('/admin-recovery', [AdminRecoveryController::class, 'showForm'])->name('admin.recovery.form');
Route::post('/admin-recovery', [AdminRecoveryController::class, 'reset'])->name('admin.recovery.reset');

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
    Route::post('/profile/addresses', [ProfileAddressController::class, 'store'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}', [ProfileAddressController::class, 'update'])->name('profile.addresses.update');
    Route::delete('/profile/addresses/{address}', [ProfileAddressController::class, 'destroy'])->name('profile.addresses.destroy');
    Route::patch('/profile/addresses/{address}/primary', [ProfileAddressController::class, 'setPrimary'])->name('profile.addresses.set-primary');

    Route::get('/profile/verifikasi-ktp', [IdentityVerificationController::class, 'edit'])->name('profile.identity.edit');
    Route::post('/profile/verifikasi-ktp', [IdentityVerificationController::class, 'update'])->name('profile.identity.update');
    Route::get('/profile/dokumen-identitas/{identityVerification}', IdentityDocumentController::class)->name('profile.identity.document');

    // Notifications
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Termasuk file route berdasarkan peran
require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/admin.php';
