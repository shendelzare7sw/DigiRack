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

// Seller Registration (accessible by verified authenticated users)
Route::middleware(['auth', 'verified'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/register', [\App\Http\Controllers\Seller\SellerRegistrationController::class, 'showForm'])->name('register.form');
    Route::post('/register', [\App\Http\Controllers\Seller\SellerRegistrationController::class, 'register'])->name('register.store');

    Route::get('/identity', [\App\Http\Controllers\Seller\SellerRegistrationController::class, 'showIdentityForm'])->name('identity.form');
    Route::post('/identity', [\App\Http\Controllers\Seller\SellerRegistrationController::class, 'submitIdentity'])->name('identity.submit');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return view('seller.dashboard');
        }

        if (!$user->store) {
            return redirect()->route('seller.register.form')
                ->with('info', 'Untuk menjadi penjual, Anda perlu mendaftarkan toko terlebih dahulu.');
        }

        return view('seller.dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:seller,admin', 'seller.approved'])->prefix('seller')->name('seller.')->group(function () {

    // Store Profile (Fase 6 & 9)
    Route::get('/store', [\App\Http\Controllers\Seller\StoreProfileController::class, 'show'])->name('store.show');
    Route::post('/store', [\App\Http\Controllers\Seller\StoreProfileController::class, 'update'])->name('store.update');

    // Products (CRUD)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/report', [ProductController::class, 'report'])->name('products.report');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{id}/edit', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Orders (Fase 6 & 7)
    Route::get('/orders', [App\Http\Controllers\Seller\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/report', [App\Http\Controllers\Seller\OrderController::class, 'report'])->name('orders.report');
    Route::get('/orders/{id}', [App\Http\Controllers\Seller\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [App\Http\Controllers\Seller\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/delivered', [App\Http\Controllers\Seller\OrderController::class, 'markDelivered'])->name('orders.delivered');
    Route::post('/orders/{id}/cancellation', [App\Http\Controllers\Seller\OrderController::class, 'resolveCancellation'])->name('orders.cancellation');

    // Reviews (Fase 6)
    // Route::get('/reviews', [App\Http\Controllers\Seller\ReviewController::class, 'index'])->name('reviews.index');

    // Reports (Fase 7)
    // Route::get('/reports', [App\Http\Controllers\Seller\ReportController::class, 'index'])->name('reports.index');

    // Wallet & Payout
    Route::get('/wallet', [\App\Http\Controllers\Seller\WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/payout', [\App\Http\Controllers\Seller\WalletController::class, 'requestPayout'])->name('wallet.payout');

    // Store Couriers (Fase 8)
    Route::get('/couriers', [\App\Http\Controllers\Seller\CourierController::class, 'index'])->name('couriers.index');
    Route::post('/couriers', [\App\Http\Controllers\Seller\CourierController::class, 'store'])->name('couriers.store');
    Route::post('/couriers/expeditions', [\App\Http\Controllers\Seller\CourierController::class, 'updateExpeditions'])->name('couriers.expeditions');
    Route::post('/couriers/{id}/toggle', [\App\Http\Controllers\Seller\CourierController::class, 'toggleActive'])->name('couriers.toggle');
    Route::delete('/couriers/{id}', [\App\Http\Controllers\Seller\CourierController::class, 'destroy'])->name('couriers.destroy');

    // Profile
    // Route::get('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'edit'])->name('profile.edit');
    // Route::post('/profile', [App\Http\Controllers\Seller\ProfileController::class, 'update'])->name('profile.update');
});
