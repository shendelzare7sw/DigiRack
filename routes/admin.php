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

    // Recovery Tickets
    Route::get('/recovery-tickets', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'index'])->name('recovery-tickets.index');
    Route::post('/recovery-tickets/{ticket}/resend-reset', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'resendResetLink'])->name('recovery-tickets.resend-reset');
    Route::post('/recovery-tickets/{ticket}/resolve', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'resolve'])->name('recovery-tickets.resolve');
    Route::post('/recovery-tickets/{ticket}/expire', [\App\Http\Controllers\Admin\RecoveryTicketController::class, 'expire'])->name('recovery-tickets.expire');

    // Stores
    Route::get('/stores', [\App\Http\Controllers\Admin\StoreController::class, 'index'])->name('stores.index');
    Route::get('/stores/{id}', [\App\Http\Controllers\Admin\StoreController::class, 'show'])->name('stores.show');
    Route::post('/stores/{id}/toggle-active', [\App\Http\Controllers\Admin\StoreController::class, 'toggleActive'])->name('stores.toggle');
    Route::post('/stores/{id}/toggle-verify', [\App\Http\Controllers\Admin\StoreController::class, 'toggleVerification'])->name('stores.verify');
    Route::post('/stores/{id}/reject', [\App\Http\Controllers\Admin\StoreController::class, 'reject'])->name('stores.reject');

    // Products (moderation)
    Route::get('/products', [\App\Http\Controllers\Admin\ProductModerationController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [\App\Http\Controllers\Admin\ProductModerationController::class, 'show'])->name('products.show');
    Route::post('/products/{id}/toggle-status', [\App\Http\Controllers\Admin\ProductModerationController::class, 'toggleStatus'])->name('products.toggle');

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/report', [\App\Http\Controllers\Admin\OrderController::class, 'report'])->name('orders.report');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');

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

    // Payout logic
    Route::get('/payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts.index');
    Route::post('/payouts/{id}/approve', [\App\Http\Controllers\Admin\PayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('/payouts/{id}/reject', [\App\Http\Controllers\Admin\PayoutController::class, 'reject'])->name('payouts.reject');

    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'store'])->name('settings.store');

    // Transaction Fees
    Route::get('/transaction-fees', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'index'])->name('transaction_fees.index');
    Route::post('/transaction-fees', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'store'])->name('transaction_fees.store');
    Route::post('/transaction-fees/{id}/toggle', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'toggleActive'])->name('transaction_fees.toggle');
    Route::delete('/transaction-fees/{id}', [\App\Http\Controllers\Admin\BuyerTransactionFeeController::class, 'destroy'])->name('transaction_fees.destroy');
});
