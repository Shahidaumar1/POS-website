<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleItemController;
use Illuminate\Support\Facades\Route;

// Home Route (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Auth Routes (Login, Register, etc.)
Auth::routes();

// User Authentication Routes (with middleware)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProductController::class, 'dashboard'])->name('dashboard');
    
    // Product routes
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/update-price', [ProductController::class, 'updatePrice']);

    // Category routes
    Route::resource('categories', CategoryController::class);
    Route::get('/categories-dashboard', [CategoryController::class, 'dashboard'])->name('categories.dashboard');

    // Sale routes (POS, Sales, SaleItems)
    Route::get('/pos', [SaleController::class, 'create'])->name('pos.create');
    Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::resource('sale_items', SaleItemController::class);
});

// Admin Routes (for Admin Users)
Route::middleware(['auth', 'admin'])->group(function () {
    // User management routes
    Route::resource('users', UserController::class);
});

// Profile Routes (for authenticated users to update profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
