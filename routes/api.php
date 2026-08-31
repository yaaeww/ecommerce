<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pembeli\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriProdukController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\UlasanController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Midtrans callback
Route::post('/midtrans-callback', [OrderController::class, 'callback'])->name('midtrans.callback');

// Public routes - Test API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API UMKM Indramayu berhasil!',
        'status' => 'success',
        'data' => [
            'version' => '1.0',
            'name' => 'UMKM Indramayu API'
        ]
    ]);
});

// Public routes - Authentication
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

// Public routes - Kategori
Route::get('/kategoris', [KategoriProdukController::class, 'indexApi']);
Route::get('/kategoris/{id}', [KategoriProdukController::class, 'showApi']);
Route::get('/kategoris/{id}/produks', [KategoriProdukController::class, 'produksByKategori']);

// Public routes - Produk
Route::get('/produks', [ProdukController::class, 'indexApi']);
Route::get('/produks/terbaru', [ProdukController::class, 'produkTerbaru']);
Route::get('/produks/{id}', [ProdukController::class, 'showApi']);
Route::get('/produks/{id}/ulasan', [ProdukController::class, 'ulasanByProduk']);

// Public routes - UMKM
Route::get('/umkms', [UmkmController::class, 'indexApi']);
Route::get('/umkms/{id}', [UmkmController::class, 'showApi']);

// Protected routes - Authentication required
Route::middleware('auth:sanctum')->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Keranjang
    Route::get('/keranjang', [KeranjangController::class, 'indexApi']);
    Route::post('/keranjang', [KeranjangController::class, 'storeApi']);
    Route::put('/keranjang/{id}', [KeranjangController::class, 'updateApi']);
    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroyApi']);
    Route::delete('/keranjang', [KeranjangController::class, 'clearApi']);

    // Ulasan
    Route::post('/ulasan', [UlasanController::class, 'storeApi']);
    Route::put('/ulasan/{id}', [UlasanController::class, 'updateApi']);
    Route::delete('/ulasan/{id}', [UlasanController::class, 'destroyApi']);

    // Orders
    Route::get('/orders', [OrderController::class, 'indexApi']);
    Route::post('/orders', [OrderController::class, 'storeApi']);
    Route::get('/orders/{id}', [OrderController::class, 'showApi']);

    // Profile
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar']);
});