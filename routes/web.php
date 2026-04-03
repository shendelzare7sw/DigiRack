<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProductController;
use App\Http\Controllers\Public\CategoryController;
use App\Http\Controllers\Public\StoreController;
use App\Http\Controllers\Public\SearchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth-protected routes (Breeze profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Termasuk file route berdasarkan peran
require __DIR__.'/auth.php';
require __DIR__.'/buyer.php';
require __DIR__.'/seller.php';
require __DIR__.'/admin.php';
