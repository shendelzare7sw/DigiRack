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

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle');
    Route::patch('/users/{user}/identity/approve', [\App\Http\Controllers\Admin\UserController::class, 'approveIdentity'])->name('users.identity.approve');
    Route::patch('/users/{user}/identity/reject', [\App\Http\Controllers\Admin\UserController::class, 'rejectIdentity'])->name('users.identity.reject');

    // Recovery Tickets
    Route::get('/recovery-tickets', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'index'])->name('recovery-tickets.index');
    Route::post('/recovery-tickets/{ticket}/resend-reset', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'resendResetLink'])->name('recovery-tickets.resend-reset');
    Route::post('/recovery-tickets/{ticket}/resolve', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'resolve'])->name('recovery-tickets.resolve');
    Route::post('/recovery-tickets/{ticket}/expire', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'expire'])->name('recovery-tickets.expire');

    // Single-store operations (admin is also the only seller).
    Route::get('/store', [\App\Http\Controllers\Seller\StoreProfileController::class, 'show'])->name('store.show');
    Route::post('/store', [\App\Http\Controllers\Seller\StoreProfileController::class, 'update'])->name('store.update');
    Route::get('/products', [\App\Http\Controllers\Seller\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/report', [\App\Http\Controllers\Seller\ProductController::class, 'report'])->name('products.report');
    Route::get('/products/create', [\App\Http\Controllers\Seller\ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create', [\App\Http\Controllers\Seller\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [\App\Http\Controllers\Seller\ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}/edit', [\App\Http\Controllers\Seller\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\Seller\ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/report', [\App\Http\Controllers\Seller\OrderController::class, 'report'])->name('orders.report');
    Route::get('/orders/{id}', [\App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/delivered', [\App\Http\Controllers\Seller\OrderController::class, 'markDelivered'])->name('orders.delivered');
    Route::post('/orders/{id}/cancellation', [\App\Http\Controllers\Seller\OrderController::class, 'resolveCancellation'])->name('orders.cancellation');
    Route::post('/orders/{id}/reviews/{review}/reply', [\App\Http\Controllers\Seller\OrderController::class, 'replyReview'])->name('orders.reviews.reply');
    Route::post('/store-reviews/{storeReview}/reply', [\App\Http\Controllers\Seller\StoreReviewController::class, 'reply'])->name('store-reviews.reply');

    // Categories
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/toggle-active', [\App\Http\Controllers\Admin\CategoryController::class, 'toggleActive'])->name('categories.toggle');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Banners
    Route::get('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banners.store');
    Route::post('/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banners.update');
    Route::post('/banners/{id}/toggle-active', [\App\Http\Controllers\Admin\BannerController::class, 'toggleActive'])->name('banners.toggle');
    Route::delete('/banners/{id}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banners.destroy');

    // Flash Sales
    Route::get('/flash-sales', [\App\Http\Controllers\Admin\FlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::post('/flash-sales', [\App\Http\Controllers\Admin\FlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::put('/flash-sales/{id}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'update'])->name('flash-sales.update');
    Route::post('/flash-sales/{id}/toggle-active', [\App\Http\Controllers\Admin\FlashSaleController::class, 'toggleActive'])->name('flash-sales.toggle');
    Route::delete('/flash-sales/{id}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

    // Transaction Fees
    Route::get('/transaction-fees', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'index'])->name('transaction_fees.index');
    Route::post('/transaction-fees', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'store'])->name('transaction_fees.store');
    Route::post('/transaction-fees/{id}/toggle', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'toggleActive'])->name('transaction_fees.toggle');
    Route::delete('/transaction-fees/{id}', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'destroy'])->name('transaction_fees.destroy');
});
