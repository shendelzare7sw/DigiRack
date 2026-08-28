<?php

use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BuyerTransactionFeeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\RecoveryTicketController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProductController;
use App\Http\Controllers\Seller\StoreProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| prefix: /admin
| middleware: auth, role:admin
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::patch('/users/{user}/identity/approve', [UserController::class, 'approveIdentity'])->name('users.identity.approve');
    Route::patch('/users/{user}/identity/reject', [UserController::class, 'rejectIdentity'])->name('users.identity.reject');

    // Recovery Tickets
    Route::get('/recovery-tickets', [RecoveryTicketController::class, 'index'])->name('recovery-tickets.index');
    Route::post('/recovery-tickets/{ticket}/resend-reset', [RecoveryTicketController::class, 'resendResetLink'])->name('recovery-tickets.resend-reset');
    Route::post('/recovery-tickets/{ticket}/resolve', [RecoveryTicketController::class, 'resolve'])->name('recovery-tickets.resolve');
    Route::post('/recovery-tickets/{ticket}/expire', [RecoveryTicketController::class, 'expire'])->name('recovery-tickets.expire');

    // The admin directly manages the identity of the only Digital Hook business.
    Route::get('/profil-bisnis', [StoreProfileController::class, 'show'])->name('business-profile.show');
    Route::post('/profil-bisnis', [StoreProfileController::class, 'update'])->name('business-profile.update');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/report', [ProductController::class, 'report'])->name('products.report');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}/edit', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/report', [OrderController::class, 'report'])->name('orders.report');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/delivered', [OrderController::class, 'markDelivered'])->name('orders.delivered');
    Route::post('/orders/{id}/cancellation', [OrderController::class, 'resolveCancellation'])->name('orders.cancellation');
    Route::post('/orders/{id}/reviews/{review}/reply', [OrderController::class, 'replyReview'])->name('orders.reviews.reply');
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/toggle-active', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Banners
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::post('/banners/{id}', [BannerController::class, 'update'])->name('banners.update');
    Route::post('/banners/{id}/toggle-active', [BannerController::class, 'toggleActive'])->name('banners.toggle');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Flash Sales
    Route::get('/flash-sales', [FlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::post('/flash-sales', [FlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::put('/flash-sales/{id}', [FlashSaleController::class, 'update'])->name('flash-sales.update');
    Route::post('/flash-sales/{id}/toggle-active', [FlashSaleController::class, 'toggleActive'])->name('flash-sales.toggle');
    Route::delete('/flash-sales/{id}', [FlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    // Transaction Fees
    Route::get('/transaction-fees', [BuyerTransactionFeeController::class, 'index'])->name('transaction_fees.index');
    Route::post('/transaction-fees', [BuyerTransactionFeeController::class, 'store'])->name('transaction_fees.store');
    Route::post('/transaction-fees/{id}/toggle', [BuyerTransactionFeeController::class, 'toggleActive'])->name('transaction_fees.toggle');
    Route::delete('/transaction-fees/{id}', [BuyerTransactionFeeController::class, 'destroy'])->name('transaction_fees.destroy');
});
