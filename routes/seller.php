<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
| prefix: /seller
| middleware: auth, role:seller,admin
*/

Route::middleware(['auth', 'role:seller,admin'])->prefix('seller')->name('seller.')->group(function () {

    Route::get('/dashboard', function () {
        return view('seller.dashboard');
    })->name('dashboard');

    // Store Profile
    // Route::get('/store', [App\Http\Controllers\Seller\StoreProfileController::class, 'show'])->name('store.show');
    // Route::post('/store', [App\Http\Controllers\Seller\StoreProfileController::class, 'update'])->name('store.update');
    // Route::post('/store/settings', [App\Http\Controllers\Seller\StoreProfileController::class, 'settings'])->name('store.settings');

    // Products
    // Route::get('/products', [App\Http\Controllers\Seller\ProductController::class, 'index'])->name('products.index');
    // Route::get('/products/create', [App\Http\Controllers\Seller\ProductController::class, 'create'])->name('products.create');
    // Route::post('/products/create', [App\Http\Controllers\Seller\ProductController::class, 'store'])->name('products.store');
    // Route::get('/products/{id}/edit', [App\Http\Controllers\Seller\ProductController::class, 'edit'])->name('products.edit');
    // Route::post('/products/{id}/edit', [App\Http\Controllers\Seller\ProductController::class, 'update'])->name('products.update');
    // Route::delete('/products/{id}', [App\Http\Controllers\Seller\ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    // Route::get('/orders', [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{id}', [App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    // Route::post('/orders/{id}/status', [App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');

    // Reviews
    // Route::get('/reviews', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('reviews.index');

    // Reports
    // Route::get('/reports', [App\Http\Controllers\Seller\ReportController::class, 'index'])->name('reports.index');

    // Profile
    // Route::get('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('profile.update');
    // Route::post('/profile/password', [App\Http\Controllers\Seller\ProfileController::class, 'updatePassword'])->name('profile.password');
});
