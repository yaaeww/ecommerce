<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ChatBotController;

// 🔹 Chat per Role
use App\Http\Controllers\Admin\AdminChatController;
use App\Http\Controllers\Penjual\PenjualChatController;
use App\Http\Controllers\User\UserChatController;

// 🔹 Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardAdminController,
    ProdukAdminController,
    KategoriController,
    AdminUmkmController,
    AdminProfileController,
    AdminPesananController,
    AdminPengirimanController,
    AdminUlasanController,
    AdminKeranjangController,
    AdminPenarikanController,
    AdminActivityLogController,
    AdminNotificationController,
    AdminLedgerController,
    AdminKomplainController,
    PenjualController,
    PembeliController,
    PendapatanController as AdminPendapatanController
};

// 🔹 Penjual Controllers
use App\Http\Controllers\Penjual\{
    DashboardPenjualController,
    ProdukPenjualController,
    PenjualUmkmController,
    PenjualProfileController,
    PenjualPesananController,
    PenjualInvoiceController,
    PenjualPenarikanController,
    PenjualNotificationController,
    PenjualUlasanController,
    PendapatanController
};

// 🔹 Pembeli Controllers
use App\Http\Controllers\Pembeli\{
    DashboardPembeliController,
    ProdukPembeliController,
    PembeliProfileController,
    PembeliAlamatController,
    KomplainPembeliController,
    KeranjangController,
    OrderController,
    CheckoutController,
    PesananController,
    RatingController
};

// 🔹 Invoice Controller (umum)
use App\Http\Controllers\InvoiceController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/kategori', [LandingController::class, 'kategori'])->name('kategori');
Route::get('/tentang', [LandingController::class, 'tentang'])->name('tentang');
Route::get('/api/search/live', [LandingController::class, 'liveSearch'])->name('api.search.live');
Route::get('/produk/{id}', [ProdukPembeliController::class, 'show'])->name('pembeli.produk.show');
/*
|--------------------------------------------------------------------------
| 💬 CHAT & CHATBOT ROUTES
|--------------------------------------------------------------------------
| Semua role (admin, penjual, pembeli) bisa chat AI atau antar-user.
| - ChatBotController      => Chat umum AI publik
| - UserChatController     => Pembeli
| - PenjualChatController  => Penjual
| - AdminChatController    => Admin
|
*/
/* =================================================== 🧑 Pembeli Chat (UserChatController) =================================================== */
Route::middleware(['auth', 'role:pembeli'])->prefix('pembeli/chat')->name('pembeli.chat.')->group(function () {

    Route::get('/{id?}', [UserChatController::class, 'index'])->name('index');

    Route::post('/send', [UserChatController::class, 'chat'])->name('send')->middleware('throttle:60,1');
    Route::get('/history/{userId}', [UserChatController::class, 'history'])->name('history');
    Route::delete('/clear/{userId}', [UserChatController::class, 'clear'])->name('clear');
});
/* =================================================== (AKHIR Pembeli Chat) =================================================== */
/* ===================================================
🛍️ Penjual Chat (PenjualChatController)
=================================================== */
Route::middleware(['auth', 'role:penjual'])
    ->prefix('penjual/chat')
    ->name('penjual.chat.')
    ->group(function () {
        Route::get('/', [PenjualChatController::class, 'index'])->name('index');
        Route::post('/send', [PenjualChatController::class, 'sendMessage'])->name('send')->middleware('throttle:60,1');
        Route::get('/history/{receiverId}', [PenjualChatController::class, 'history'])->name('history');
    });


/*
|--------------------------------------------------------------------------
| Google Authentication
|--------------------------------------------------------------------------
*/
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::get('/auth/google/role', [GoogleController::class, 'chooseRole'])->name('auth.google.role');
Route::post('/auth/google/save-role', [GoogleController::class, 'saveRole'])->name('auth.google.saveRole');

/*
|--------------------------------------------------------------------------
| Redirect After Login
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->get('/redirect-after-login', function () {
    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'penjual' => redirect()->route('penjual.dashboard'),
        'pembeli' => redirect()->route('pembeli.dashboard'),
        default => abort(403),
    };
});
Route::middleware('auth')->get('/dashboard', fn() => redirect('/redirect-after-login'))->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');

    Route::resource('produk', ProdukAdminController::class)->except(['create', 'edit', 'update', 'store', 'show']);
    Route::resource('kategori', KategoriController::class)->except(['edit', 'update']);
    Route::get('/kategori/{id}/edit', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');

    Route::prefix('umkm')->name('umkm.')->group(function () {
        Route::get('/', [AdminUmkmController::class, 'index'])->name('index');
        Route::get('/{id}/show', [AdminUmkmController::class, 'show'])->name('show');
        Route::get('/{id}/products', [AdminUmkmController::class, 'products'])->name('products');
        Route::post('/{id}/approve', [AdminUmkmController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [AdminUmkmController::class, 'reject'])->name('reject');
        Route::post('/{id}/notify', [AdminUmkmController::class, 'sendNotification'])->name('notify');
        Route::delete('/produk/{id}', [AdminUmkmController::class, 'destroyProduct'])->name('produk.destroy');
        Route::delete('/{id}', [AdminUmkmController::class, 'destroy'])->name('destroy');
    });

    Route::controller(AdminProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    Route::get('pendapatan', [AdminPendapatanController::class, 'index'])->name('pendapatan.index');
    Route::post('pendapatan/update-komisi', [AdminPendapatanController::class, 'updateKomisi'])->name('pendapatan.update-komisi');
    Route::resource('pesanan', AdminPesananController::class)->only(['index', 'show']);
    
    // 🚚 Fulfillment & Pengiriman Tracker
    Route::get('pengiriman', [AdminPengirimanController::class, 'index'])->name('pengiriman.index');
    Route::post('pengiriman/{id}/resi', [AdminPengirimanController::class, 'updateResi'])->name('pengiriman.update-resi');

    // 💳 Pencairan Saldo (Payout) Mitra
    Route::get('penarikan', [AdminPenarikanController::class, 'index'])->name('penarikan.index');
    Route::post('penarikan/{id}/approve', [AdminPenarikanController::class, 'approve'])->name('penarikan.approve');
    Route::post('penarikan/{id}/reject', [AdminPenarikanController::class, 'reject'])->name('penarikan.reject');

    // 💬 Moderasi Chat & Anti-Fraud Hub
    Route::get('chat-monitoring', [AdminChatController::class, 'index'])->name('chat.index');
    Route::get('chat-monitoring/{userA}/{userB}', [AdminChatController::class, 'show'])->name('chat.show');

    // ⭐ Moderasi Ulasan & Sentimen
    Route::get('ulasan', [AdminUlasanController::class, 'index'])->name('ulasan.index');
    Route::post('ulasan/{id}/moderate', [AdminUlasanController::class, 'moderate'])->name('ulasan.moderate');

    // 🛒 Analisis Keranjang Terbengkalai
    Route::get('keranjang-analytics', [AdminKeranjangController::class, 'index'])->name('keranjang.index');

    // 📊 Buku Besar & Monitoring Saldo Escrow Platform
    Route::get('ledger', [AdminLedgerController::class, 'index'])->name('ledger.index');

    // 🛡️ Pusat Mediasi Komplain & Garansi Buah Segar
    Route::get('komplain', [AdminKomplainController::class, 'index'])->name('komplain.index');
    Route::get('komplain/{id}', [AdminKomplainController::class, 'show'])->name('komplain.show');
    Route::post('komplain/{id}/process', [AdminKomplainController::class, 'process'])->name('komplain.process');

    // 🛡️ Audit Trail & Log Aktivitas Sistem
    Route::get('activity-log', [AdminActivityLogController::class, 'index'])->name('activity-log.index');

    // 🔔 Real-time Platform Notifications (Navbar Center)
    Route::get('notifications/unread', [AdminNotificationController::class, 'getUnreadJson'])->name('notifications.unread');
    Route::post('notifications/mark-read', [AdminNotificationController::class, 'markAllAsRead'])->name('notifications.mark-read');

    Route::resource('penjual', PenjualController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('pembeli', PembeliController::class)->only(['index', 'edit', 'update', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Penjual Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:penjual'])->prefix('penjual')->name('penjual.')->group(function () {
    Route::get('/dashboard', [DashboardPenjualController::class, 'index'])->name('dashboard');

    Route::post('/produk/{id}/quick-stock', [ProdukPenjualController::class, 'quickStock'])->name('produk.quick-stock');
    Route::post('/produk/{id}/toggle-status', [ProdukPenjualController::class, 'toggleStatus'])->name('produk.toggle-status');
    Route::resource('produk', ProdukPenjualController::class);
    Route::post('/umkm/{id}/toggle-libur', [PenjualUmkmController::class, 'toggleLibur'])->name('umkm.toggle-libur');
    Route::resource('umkm', PenjualUmkmController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    Route::get('/pesanan', [PenjualPesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{order}/buat', [PenjualPesananController::class, 'create'])->name('pesanan.create');
    Route::patch('/pesanan/{order}/update-status', [PenjualPesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    Route::get('/pesanan/{order}/invoice/pdf', [PenjualInvoiceController::class, 'generatePdf'])->name('pesanan.invoice.pdf');
    Route::get('/pesanan/{id}/shipping-label', [PenjualInvoiceController::class, 'shippingLabel'])->name('pesanan.shipping-label');
    Route::get('/invoice/{id}', [PenjualInvoiceController::class, 'show'])->name('invoice.show');
    Route::post('/ulasan/{id}/balas', [PenjualUlasanController::class, 'reply'])->name('ulasan.reply');

    // Pendapatan & Tarik Saldo
    Route::get('/pendapatan', [PendapatanController::class, 'index'])->name('pendapatan.index');
    Route::get('/penarikan', [PenjualPenarikanController::class, 'index'])->name('penarikan.index');
    Route::post('/penarikan', [PenjualPenarikanController::class, 'store'])->name('penarikan.store');
    Route::get('/pendapatan/{id}/detail', [PendapatanController::class, 'show'])->name('pendapatan.detail');
    Route::get('/pendapatan/{id}/export-excel', [PendapatanController::class, 'exportDetailExcel'])->name('pendapatan.detail.export.excel');
    Route::get('/pendapatan/{id}/export-pdf', [PendapatanController::class, 'exportDetailPdf'])->name('pendapatan.detail.export.pdf');
    Route::get('/pendapatan/export/excel', [PendapatanController::class, 'exportSummaryExcel'])->name('pendapatan.export.summary.excel');
    Route::get('/pendapatan/export/pdf', [PendapatanController::class, 'exportSummaryPdf'])->name('pendapatan.export.summary.pdf');

    // Profil Penjual
    Route::controller(PenjualProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
        Route::post('/avatar', 'updateAvatar')->name('avatar');
    });

    // 🔔 Real-time Seller Notification Center
    Route::get('notifications/unread', [PenjualNotificationController::class, 'getUnreadJson'])->name('notifications.unread');
    Route::post('notifications/mark-read', [PenjualNotificationController::class, 'markAllAsRead'])->name('notifications.mark-read');
});

/*
|--------------------------------------------------------------------------
| Pembeli Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pembeli'])->prefix('pembeli')->name('pembeli.')->group(function () {
    Route::get('/dashboard', [DashboardPembeliController::class, 'index'])->name('dashboard');

    Route::controller(ProdukPembeliController::class)->prefix('produk')->name('produk.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::controller(KeranjangController::class)->prefix('keranjang')->name('keranjang.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    Route::get('/order/{produk_id?}/{quantity?}', [OrderController::class, 'showForm'])->name('order');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/status/belum-bayar', [OrderController::class, 'statusBelumBayar'])->name('status.belum-bayar');
    Route::get('/status/dikemas', [PesananController::class, 'statusDikemas'])->name('status.dikemas');
    Route::get('/status/dikirim', [PesananController::class, 'dikirim'])->name('status.dikirim');
    Route::get('/pending/{order_id_midtrans}', [OrderController::class, 'pending'])->name('pending');
    Route::delete('/order/{id}', [OrderController::class, 'batal'])->name('order.batal');
    Route::post('/order/cancel/{order_id}', [OrderController::class, 'cancelExpiredOrder'])->name('order.cancelExpired');

    Route::get('/invoice/{id}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/invoice/{id}/pdf', [InvoiceController::class, 'generatePdf'])->name('invoice.pdf');

    Route::controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', 'index')->name('form');
        Route::post('/store', 'store')->name('store');
        Route::post('/midtrans', 'getMidtransToken')->name('midtrans');
    });

    // 📦 Pesanan Pembeli & Pembatalan
    Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/dikemas', [PesananController::class, 'statusDikemas'])->name('pesanan.status.dikemas');
    Route::get('/pesanan/dikirim', [PesananController::class, 'dikirim'])->name('pesanan.dikirim');
    Route::post('/pesanan/{id}/cancel', [PesananController::class, 'cancelOrder'])->name('pesanan.cancel');
    Route::patch('/pesanan/{id}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    Route::delete('/pesanan/bulk-delete', [PesananController::class, 'bulkDelete'])->name('pesanan.bulkDelete');
    Route::delete('/pesanan/{id}', [PesananController::class, 'destroy'])->name('pesanan.destroy');

    // 📖 Buku Alamat Pengiriman Tersimpan
    Route::get('/alamat', [PembeliAlamatController::class, 'index'])->name('alamat.index');
    Route::post('/alamat', [PembeliAlamatController::class, 'store'])->name('alamat.store');
    Route::put('/alamat/{id}', [PembeliAlamatController::class, 'update'])->name('alamat.update');
    Route::post('/alamat/{id}/set-utama', [PembeliAlamatController::class, 'setUtama'])->name('alamat.set-utama');
    Route::delete('/alamat/{id}', [PembeliAlamatController::class, 'destroy'])->name('alamat.destroy');

    // 🛡️ Garansi Buah Segar & Komplain Pembeli
    Route::get('/komplain', [KomplainPembeliController::class, 'index'])->name('komplain.index');
    Route::get('/komplain/ajukan/{order}', [KomplainPembeliController::class, 'create'])->name('komplain.create');
    Route::post('/komplain/store/{order}', [KomplainPembeliController::class, 'store'])->name('komplain.store');
    Route::get('/komplain/{id}', [KomplainPembeliController::class, 'show'])->name('komplain.show');

    Route::controller(PembeliProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
    });

    Route::get('/rating', [RatingController::class, 'index'])->name('rating.index');
    Route::get('/rating/create', [RatingController::class, 'create'])->name('rating.create');
    Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
