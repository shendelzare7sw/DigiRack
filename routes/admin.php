<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| prefix: /admin
| middleware: auth, role:admin
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Users
    // Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    // Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    // Route::post('/users/{id}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle');

    // Stores
    // Route::get('/stores', [App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    // Route::get('/stores/{id}', [App\Http\Controllers\Admin\StoreController::class, 'show'])->name('stores.show');
    // Route::post('/stores/{id}/toggle-active', [App\Http\Controllers\Admin\StoreController::class, 'toggleActive'])->name('stores.toggle');

    // Products (moderation)
    // Route::get('/products', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    // Route::get('/products/{id}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');
    // Route::post('/products/{id}/approve', [App\Http\Controllers\Admin\ProductController::class, 'approve'])->name('products.approve');
    // Route::post('/products/{id}/reject', [App\Http\Controllers\Admin\ProductController::class, 'reject'])->name('products.reject');

    // Orders
    // Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    // Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

    // Categories
    // Route::get('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    // Route::post('/categories', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    // Route::get('/categories/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    // Route::post('/categories/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    // Route::delete('/categories/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Banners
    // Route::get('/banners', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    // Route::post('/banners', [App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    // Route::get('/banners/{id}/edit', [App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banners.edit');
    // Route::post('/banners/{id}/edit', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    // Route::delete('/banners/{id}', [App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

    // Flash Sales
    // Route::get('/flash-sales', [App\Http\Controllers\Admin\FlashSaleController::class, 'index'])->name('flash-sales.index');
    // Route::post('/flash-sales', [App\Http\Controllers\Admin\FlashSaleController::class, 'store'])->name('flash-sales.store');
    // Route::get('/flash-sales/{id}/edit', [App\Http\Controllers\Admin\FlashSaleController::class, 'edit'])->name('flash-sales.edit');
    // Route::post('/flash-sales/{id}/edit', [App\Http\Controllers\Admin\FlashSaleController::class, 'update'])->name('flash-sales.update');
    // Route::delete('/flash-sales/{id}', [App\Http\Controllers\Admin\FlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    // Reviews
    // Route::get('/reviews', [App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    // Route::delete('/reviews/{id}', [App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');
});
