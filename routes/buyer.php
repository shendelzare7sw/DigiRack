<?php

use App\Http\Controllers\Buyer\CartController;
use App\Http\Controllers\Buyer\CheckoutController;
use App\Http\Controllers\Buyer\OrderController;
use App\Http\Controllers\Buyer\ReviewController;
use App\Http\Controllers\Buyer\WishlistController;
use App\Http\Middleware\EnforceActiveBuyerRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
| prefix: /buyer
| middleware: auth, role:buyer
*/

Route::middleware(['auth', 'verified', 'role:buyer', EnforceActiveBuyerRole::class])->prefix('buyer')->name('buyer.')->group(function () {

    Route::get('/dashboard', function () {
        return view('buyer.dashboard');
    })->name('dashboard');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Orders (Fase 5B)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Checkout (Fase 5B)
    Route::post('/checkout/init', [CheckoutController::class, 'init'])->name('checkout.init');
    Route::post('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/orders/{id}/upload-proof', [CheckoutController::class, 'uploadProof'])->name('orders.upload-proof');

    // Reviews
    // Route::get('/reviews', [App\Http\Controllers\Buyer\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{orderItem}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::post('/reviews/{orderItem}', [ReviewController::class, 'store'])->name('reviews.store');
    // Profile
    // Route::get('/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/profile', [App\Http\Controllers\Buyer\ProfileController::class, 'update'])->name('profile.update');
    // Route::post('/profile/password', [App\Http\Controllers\Buyer\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Addresses
    // Route::get('/addresses', [App\Http\Controllers\Buyer\AddressController::class, 'index'])->name('addresses.index');
    // Route::post('/addresses', [App\Http\Controllers\Buyer\AddressController::class, 'store'])->name('addresses.store');
    // Route::get('/addresses/{id}/edit', [App\Http\Controllers\Buyer\AddressController::class, 'edit'])->name('addresses.edit');
    // Route::post('/addresses/{id}/edit', [App\Http\Controllers\Buyer\AddressController::class, 'update'])->name('addresses.update');
    // Route::delete('/addresses/{id}', [App\Http\Controllers\Buyer\AddressController::class, 'destroy'])->name('addresses.destroy');
    // Route::post('/addresses/{id}/primary', [App\Http\Controllers\Buyer\AddressController::class, 'setPrimary'])->name('addresses.primary');
});
