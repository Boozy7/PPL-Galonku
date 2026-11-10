<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepotController;
use App\Http\Controllers\DepotDashboardController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes - Galonku Surabaya
|--------------------------------------------------------------------------
*/

// Homepage & Pemetaan Lokasi Depot
Route::get('/', [DepotController::class, 'index'])->name('home');
Route::get('/depots', [DepotController::class, 'index'])->name('depots.index');
Route::get('/depots/{slug}', [DepotController::class, 'show'])->name('depots.show');
Route::get('/api/depots', [DepotController::class, 'apiDepots'])->name('api.depots');
Route::post('/depots/{id}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

// Pemesanan Refill Pickup & Antrean
Route::get('/antrean', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/order/{depotId}', [OrderController::class, 'create'])->name('orders.create');
Route::post('/order', [OrderController::class, 'store'])->name('orders.store');
Route::get('/order/track/{orderNumber}', [OrderController::class, 'track'])->name('orders.track');
Route::post('/order/track/{orderNumber}/advance', [OrderController::class, 'advanceStatus'])->name('orders.advance');

// Autentikasi Customer & Mitra Depot
Route::get('/login', [AuthController::class, 'showUserLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showUserRegister'])->name('register');
Route::post('/register', [AuthController::class, 'registerUser'])->name('register.submit');

Route::get('/depot/login', [AuthController::class, 'showDepotLogin'])->name('depot.login');
Route::get('/depot/register', [AuthController::class, 'showDepotRegister'])->name('depot.register');
Route::post('/depot/register', [AuthController::class, 'registerDepot'])->name('depot.register.submit');

Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Manajemen Mitra Depot
Route::middleware(['auth', 'role:depot'])->prefix('depot')->name('depot.')->group(function () {
    Route::get('/', [DepotDashboardController::class, 'index'])->name('dashboard');
    Route::post('/toggle-status', [DepotDashboardController::class, 'toggleStatus'])->name('toggle_status');
    Route::post('/operational-hours', [DepotDashboardController::class, 'updateOperationalHours'])->name('update_operational_hours');
    Route::post('/order/{id}/status', [DepotDashboardController::class, 'updateOrderStatus'])->name('update_order_status');
    Route::post('/order/{id}/finish', [DepotDashboardController::class, 'finishOrder'])->name('finish_order');
    Route::post('/cert/upload', [DepotDashboardController::class, 'uploadCert'])->name('upload_cert');

    // Laporan Rekapitulasi Penjualan (SCRUM-66)
    Route::get('/report/export', [DepotDashboardController::class, 'exportReport'])->name('report.export');

    // Katalog Produk & Layanan Depot
    Route::post('/products', [DepotDashboardController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{id}', [DepotDashboardController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{id}', [DepotDashboardController::class, 'deleteProduct'])->name('products.destroy');
});
