<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('depot/products', [DepotDashboardController::class, 'storeProduct'])->name('depot.products.store');
Route::put('depot/products/{id}', [DepotDashboardController::class, 'updateProduct'])->name('depot.products.update');
Route::delete('depot/products/{id}', [DepotDashboardController::class, 'deleteProduct'])->name('depot.products.destroy');
