<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DepotDashboardController;

// SCRUM-42: Peta & Depot GPS (Customer)
Route::get('/', [DepotController::class, 'index'])->name('home');
Route::get('depots', [DepotController::class, 'index'])->name('depots.index');
Route::get('api/depots', [DepotController::class, 'apiDepots'])->name('api.depots');

// SCRUM-49: Manajemen Produk (DAMIU)
Route::post('depot/products', [DepotDashboardController::class, 'storeProduct'])->name('depot.products.store');
Route::put('depot/products/{id}', [DepotDashboardController::class, 'updateProduct'])->name('depot.products.update');
Route::delete('depot/products/{id}', [DepotDashboardController::class, 'deleteProduct'])->name('depot.products.destroy');

// SCRUM-53: Form Pemesanan (Customer)
Route::get('order/{depotId}', [OrderController::class, 'create'])->name('orders.create');
Route::post('order', [OrderController::class, 'store'])->name('orders.store');

// SCRUM-56: Manajemen Antrean Depot & Tracking
Route::get('depot', [DepotDashboardController::class, 'index'])->name('depot.dashboard');
Route::post('depot/toggle-status', [DepotDashboardController::class, 'toggleStatus'])->name('depot.toggle_status');
Route::post('depot/order/{id}/status', [DepotDashboardController::class, 'updateOrderStatus'])->name('depot.update_order_status');
Route::get('order/track/{orderNumber}', [OrderController::class, 'track'])->name('orders.track');
Route::get('antrean', [OrderController::class, 'index'])->name('orders.index');
