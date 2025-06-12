<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'payment_type',
    'name' => 'payment_type',
], function () {
    Route::get('/', [PaymentTypeController::class, 'index'])->name('payment_type.index');
    Route::post('/create', [PaymentTypeController::class, 'store'])->name('payment_type.create');
    Route::put('/update/{id}', [PaymentTypeController::class, 'update'])->name('payment_type.update');
    Route::delete('/destroy/{id}', [PaymentTypeController::class, 'destroy'])->name('payment_type.destroy');
    Route::put('/active/{id}', [PaymentTypeController::class, 'active'])->name('payment_type.active');
});

Route::group([
    'prefix' => 'product',
    'name' => 'product',
], function () {
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::post('/create', [ProductController::class, 'store'])->name('product.create');
    Route::put('/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::put('/active/{id}', [ProductController::class, 'active'])->name('product.active');
});

Route::group([
    'prefix' => 'cart',
    'name' => 'cart',
], function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add_product', [CartController::class, 'store'])->name('cart.add_product');
    Route::put('/update_product', [CartController::class, 'update'])->name('cart.update_product');
    Route::delete('/clean', [CartController::class, 'destroy'])->name('cart.clean');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});