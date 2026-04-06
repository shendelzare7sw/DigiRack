<?php

use App\Http\Controllers\Seller\ProductController;
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

    // Store Profile (Fase 6)
    // Route::get('/store', [App\Http\Controllers\Seller\StoreProfileController::class, 'show'])->name('store.show');
    // Route::post('/store', [App\Http\Controllers\Seller\StoreProfileController::class, 'update'])->name('store.update');

    // Products (CRUD)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}/edit', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Orders (Fase 6)
    // Route::get('/orders', [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{id}', [App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    // Route::post('/orders/{id}/status', [App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');

    // Reviews (Fase 6)
    // Route::get('/reviews', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('reviews.index');

    // Reports (Fase 7)
    // Route::get('/reports', [App\Http\Controllers\Seller\ReportController::class, 'index'])->name('reports.index');

    // Profile
    // Route::get('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('profile.update');
});

