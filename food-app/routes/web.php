<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [CustomerController::class, 'index'])->name('customer.hotels');
Route::get('/hotel/{hotel}/menu', [CustomerController::class, 'menu'])->name('customer.menu');

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [OrderController::class, 'create'])->name('orders.checkout');
    Route::post('/place-order', [OrderController::class, 'store'])->name('orders.place');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/pick', [OrderController::class, 'pick'])->name('orders.pick')->middleware('role:Delivery Person|Admin');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('role:Delivery Person');
    Route::get('/queries/create', [QueryController::class, 'create'])->name('queries.create');
    Route::post('/queries', [QueryController::class, 'store'])->name('queries.store');
    Route::get('/queries', [QueryController::class, 'index'])->name('queries.index');

    Route::middleware(['role:Admin|Employee'])->group(function () {
        Route::resource('/categories', CategoryController::class);
        Route::resource('/users', UserController::class);
        Route::resource('/hotels', HotelController::class);
        Route::put('/queries/{query}/resolve', [QueryController::class, 'update'])->name('queries.update');
    });

    Route::middleware(['role:Admin|Hotel Owner'])->group(function () {
        Route::resource('/products', ProductController::class);
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('/products/{product}/toggle', [ProductController::class, 'toggleAvailability'])->name('products.toggle');
    });
});
