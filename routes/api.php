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
use App\Http\Controllers\Api\AlamatApiController;
use App\Http\Controllers\Api\BuyerOrderApiController;
use App\Http\Controllers\Api\KomplainApiController;
use App\Http\Controllers\Api\SellerApiController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\Api\OtpPasswordResetApiController;
use App\Http\Controllers\Api\GoogleAuthApiController;

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

// Midtrans callback
Route::post('/midtrans-callback', [OrderController::class, 'callback'])->name('midtrans.callback');

// Public routes - Test API
Route::get('/test', function () {
    return response()->json([
        'message' => 'API Juragan Pelem E-Commerce berhasil running!',
        'status' => 'success',
        'data' => [
            'version' => '2.0',
            'name' => 'Juragan Pelem API'
        ]
    ]);
});

// Public routes - Authentication
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

// Public routes - Google OAuth (mobile)
Route::post('/auth/google', [GoogleAuthApiController::class, 'login'])->middleware('throttle:10,1');
Route::post('/auth/google/choose-role', [GoogleAuthApiController::class, 'chooseRole'])->middleware('throttle:10,1');

// Public routes - Forgot Password & OTP Reset
Route::post('/forgot-password', [OtpPasswordResetApiController::class, 'requestOtp'])->middleware('throttle:5,1');
Route::post('/forgot-password/verify-otp', [OtpPasswordResetApiController::class, 'verifyOtp'])->middleware('throttle:10,1');
Route::post('/forgot-password/resend-otp', [OtpPasswordResetApiController::class, 'resendOtp'])->middleware('throttle:3,1');
Route::post('/forgot-password/new-password', [OtpPasswordResetApiController::class, 'updatePassword'])->middleware('throttle:5,1');

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

    // User & Profile
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        if ($user->role === 'penjual') {
            $user->load('umkm');
        }
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::post('/profile/avatar', [UserController::class, 'updateAvatar']);

    // ==========================================
    // 🛒 BUYER (PEMBELI) API
    // ==========================================
    Route::middleware('role:pembeli')->group(function () {
        // Keranjang
        Route::get('/keranjang', [KeranjangController::class, 'indexApi']);
        Route::post('/keranjang', [KeranjangController::class, 'storeApi']);
        Route::put('/keranjang/{id}', [KeranjangController::class, 'updateApi']);
        Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroyApi']);
        Route::delete('/keranjang', [KeranjangController::class, 'clearApi']);

        // Buku Alamat
        Route::get('/alamat', [AlamatApiController::class, 'index']);
        Route::post('/alamat', [AlamatApiController::class, 'store']);
        Route::put('/alamat/{id}', [AlamatApiController::class, 'update']);
        Route::post('/alamat/{id}/utama', [AlamatApiController::class, 'setUtama']);
        Route::delete('/alamat/{id}', [AlamatApiController::class, 'destroy']);

        // Orders
        Route::get('/orders', [BuyerOrderApiController::class, 'index']);
        Route::post('/orders', [BuyerOrderApiController::class, 'checkout']);
        Route::get('/orders/{id}', [BuyerOrderApiController::class, 'show']);
        Route::post('/orders/{id}/cancel', [BuyerOrderApiController::class, 'cancel']);
        Route::post('/orders/{id}/terima', [BuyerOrderApiController::class, 'confirmReceived']);
        Route::post('/orders/{order_id_midtrans}/cek-status', [BuyerOrderApiController::class, 'checkStatus']);

        // Ulasan & Rating
        Route::post('/ulasan', [UlasanController::class, 'storeApi']);
        Route::put('/ulasan/{id}', [UlasanController::class, 'updateApi']);
        Route::delete('/ulasan/{id}', [UlasanController::class, 'destroyApi']);

        // Garansi & Komplain
        Route::get('/komplain', [KomplainApiController::class, 'index']);
        Route::post('/komplain/{order_id}', [KomplainApiController::class, 'store']);
        Route::get('/komplain/{id}', [KomplainApiController::class, 'show']);
    });

    // ==========================================
    // 🏪 SELLER (PENJUAL) API
    // ==========================================
    Route::middleware('role:penjual')->prefix('seller')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SellerApiController::class, 'dashboard']);

        // Toko UMKM
        Route::get('/umkm', [SellerApiController::class, 'getUmkm']);
        Route::post('/umkm', [SellerApiController::class, 'storeUmkm']);
        Route::put('/umkm', [SellerApiController::class, 'updateUmkm']);
        Route::post('/umkm/toggle-libur', [SellerApiController::class, 'toggleLibur']);

        // Produk
        Route::get('/produks', [SellerApiController::class, 'getProduks']);
        Route::post('/produks', [SellerApiController::class, 'storeProduk']);
        Route::get('/produks/{id}', [SellerApiController::class, 'showProduk']);
        Route::post('/produks/{id}', [SellerApiController::class, 'updateProduk']);
        Route::delete('/produks/{id}', [SellerApiController::class, 'deleteProduk']);
        Route::post('/produks/{id}/quick-stock', [SellerApiController::class, 'quickStock']);
        Route::post('/produks/{id}/toggle-status', [SellerApiController::class, 'toggleStatusProduk']);

        // Pesanan
        Route::get('/pesanan', [SellerApiController::class, 'getPesanan']);
        Route::get('/pesanan/{id}', [SellerApiController::class, 'showPesanan']);
        Route::post('/pesanan/{id}/update-status', [SellerApiController::class, 'updatePesananStatus']);

        // Pendapatan & Penarikan
        Route::get('/pendapatan', [SellerApiController::class, 'getPendapatan']);
        Route::get('/penarikan', [SellerApiController::class, 'getPenarikan']);
        Route::post('/penarikan', [SellerApiController::class, 'storePenarikan']);

        // Balas Ulasan
        Route::post('/ulasan/{id}/balas', [SellerApiController::class, 'replyUlasan']);
    });

    // ==========================================
    // 💬 CHAT & AI ASSISTANT (SHARED)
    // ==========================================
    Route::prefix('chat')->group(function () {
        Route::get('/contacts', [ChatApiController::class, 'getContacts']);
        Route::get('/history/{userId}', [ChatApiController::class, 'getHistory']);
        Route::post('/send', [ChatApiController::class, 'sendMessage'])->middleware('throttle:60,1');
        Route::delete('/clear/{userId}', [ChatApiController::class, 'clearChat']);
    });
});