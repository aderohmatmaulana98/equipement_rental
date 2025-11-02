<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PemilikController;
use App\Http\Controllers\SewaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/signup', [AuthController::class, 'signup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup_action'])->name('signup_action');
Route::post('/login_action', [AuthController::class, 'login_action'])->name('login_action');
// Route::get('/register', [AuthController::class, 'register'])->name('register');
// Route::post('/register_action', [AuthController::class, 'register_action'])->name('register_action');
// Route::get('/payment', function () {
//     return view('payment'); // view untuk testing
// });


route::middleware(['auth'])->group(function () {
    // Routes untuk Pemilik Kantor (role_id = 1)
    Route::middleware(['role:1'])->prefix('pemilik')->group(function () {
        Route::get('/dashboard', [PemilikController::class, 'dashboard'])->name('pemilik.dashboard');
    });
    
    // Routes untuk Admin Kantor (role_id = 2)
    Route::middleware(['role:2'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::resource('jenis_barang', JenisBarangController::class);
        Route::resource('barang', BarangController::class);
        Route::get('/admin', [AdminController::class, 'admin'])->name('admin.admin');
        Route::post('/admin/store', [AdminController::class, 'admin_store'])->name('admin.store');
        Route::put('/admin/update/{id}', [AdminController::class, 'admin_update'])->name('admin.update');
        Route::delete('/admin/delete/{id}', [AdminController::class, 'admin_delete'])->name('admin.delete');
        
        Route::get('/customer', [AdminController::class, 'customer'])->name('customer.customer');
        Route::post('/customer/store', [AdminController::class, 'customer_store'])->name('customer.store');
        Route::put('/customer/update/{id}', [AdminController::class, 'customer_update'])->name('customer.update');
        Route::delete('/customer/delete/{id}', [AdminController::class, 'customer_delete'])->name('customer.delete');
    });
    
    // Routes untuk User biasa (role_id = 3)
    Route::middleware(['role:3'])->prefix('user')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/list-barang', [UserController::class, 'list_barang'])->name('user.list_barang');
        Route::resource('sewa', SewaController::class);
        Route::put('/confirm/{id}', [SewaController::class, 'confirm_pay'])->name('sewa.confirm');
        Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

        Route::post('/checkout', [SewaController::class, 'checkout'])->name('checkout');
    });
    // Routes untuk warehouse (role_id = 4)
    Route::middleware(['role:4'])->prefix('warehouse')->group(function () {
        Route::get('/dashboard', [WarehouseController::class, 'dashboard'])->name('warehouse.dashboard');
        Route::get('/list-barang', [WarehouseController::class, 'list_barang'])->name('warehouse.list_barang');
        Route::get('/sewa', [WarehouseController::class, 'penyewaan'])->name('warehouse.penyewaan');
        Route::get('/sewa/{id}', [WarehouseController::class, 'show'])->name('warehouse.detail');
        Route::post('/sewa/update-status', [WarehouseController::class, 'updateStatus'])->name('warehouse.updateStatus');
    });

    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/unauthorized', [AuthController::class, 'not_authorized'])->name('unauthorized');
});
Route::post('/payment/{id}', [PaymentController::class, 'createTransaction'])->name('payment');
Route::post('/midtrans/callback', [PaymentController::class, 'callback'])->withoutMiddleware([VerifyCsrfToken::class]);