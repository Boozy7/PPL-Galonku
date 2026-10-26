<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('depot/products', [DepotDashboardController::class, 'storeProduct'])->name('depot.products.store');
Route::put('depot/products/{id}', [DepotDashboardController::class, 'updateProduct'])->name('depot.products.update');
Route::delete('depot/products/{id}', [DepotDashboardController::class, 'deleteProduct'])->name('depot.products.destroy');
Route::get('order/{depotId}', [OrderController::class, 'create'])->name('orders.create');
Route::post('order', [OrderController::class, 'store'])->name('orders.store');