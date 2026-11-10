<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DepotDashboardController;

// SCRUM-42: Peta & Depot GPS (Customer)
Route::get('/', [DepotController::class, 'index'])->name('home');
Route::get('depots', [DepotController::class, 'index'])->name('depots.index');
Route::get('api/depots', [DepotController::class, 'apiDepots'])->name('api.depots');

// SCRUM-53: Form Pemesanan & Tracking (Customer)
Route::get('order/{depotId}', [OrderController::class, 'create'])->name('orders.create');
Route::post('order', [OrderController::class, 'store'])->name('orders.store');
Route::get('order/track/{orderNumber}', [OrderController::class, 'track'])->name('orders.track');
Route::get('antrean', [OrderController::class, 'index'])->name('orders.index');

// =========================================================================
// GRUP MIDDLEWARE DEPOT (Akses khusus untuk pemilik Depot)
// =========================================================================
Route::middleware(['auth', 'role:depot'])->prefix('depot')->name('depot.')->group(function () {

    // Halaman Utama Dashboard
    Route::get('/', [DepotDashboardController::class, 'index'])->name('dashboard');

    // Status Operasional
    Route::post('/toggle-status', [DepotDashboardController::class, 'toggleStatus'])->name('toggle_status');

    // Manajemen Antrean & Status Pesanan
    Route::post('/order/{id}/status', [DepotDashboardController::class, 'updateOrderStatus'])->name('update_order_status');

    // SCRUM-49: Manajemen Produk (DAMIU)
    Route::post('/products', [DepotDashboardController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [DepotDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [DepotDashboardController::class, 'deleteProduct'])->name('products.destroy');

    // SCRUM-66: Fitur Export Laporan yang Baru Ditambahkan
    Route::get('/report/export', [DepotDashboardController::class, 'exportReport'])->name('report.export');
});
