<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
| prefix: /buyer
| middleware: auth, role:buyer,seller,admin
*/

Route::middleware(['auth', 'role:buyer,seller,admin'])->prefix('buyer')->name('buyer.')->group(function () {

    Route::get('/dashboard', function () {
        return view('buyer.dashboard');
    })->name('dashboard');

    // Orders
    // Route::get('/orders', [App\Http\Controllers\Buyer\OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{id}', [App\Http\Controllers\Buyer\OrderController::class, 'show'])->name('orders.show');
    // Route::post('/orders/{id}/confirm', [App\Http\Controllers\Buyer\OrderController::class, 'confirm'])->name('orders.confirm');

    // Cart
    // Route::get('/cart', [App\Http\Controllers\Buyer\CartController::class, 'index'])->name('cart.index');
    // Route::post('/cart', [App\Http\Controllers\Buyer\CartController::class, 'store'])->name('cart.store');
    // Route::delete('/cart/{id}', [App\Http\Controllers\Buyer\CartController::class, 'destroy'])->name('cart.destroy');

    // Wishlist
    // Route::get('/wishlist', [App\Http\Controllers\Buyer\WishlistController::class, 'index'])->name('wishlist.index');
    // Route::post('/wishlist/toggle', [App\Http\Controllers\Buyer\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    // Route::get('/checkout', [App\Http\Controllers\Buyer\CheckoutController::class, 'index'])->name('checkout.index');
    // Route::post('/checkout', [App\Http\Controllers\Buyer\CheckoutController::class, 'store'])->name('checkout.store');

    // Reviews
    // Route::get('/reviews', [App\Http\Controllers\Buyer\ReviewController::class, 'index'])->name('reviews.index');
    // Route::post('/reviews/{order_item_id}', [App\Http\Controllers\Buyer\ReviewController::class, 'store'])->name('reviews.store');

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
