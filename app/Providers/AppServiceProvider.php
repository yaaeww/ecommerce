<?php

namespace App\Providers;

use App\Facades\Keranjang;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind service untuk Keranjang
        $this->app->singleton('keranjangservice', function ($app) {
            return new \App\Services\KeranjangService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share total item di keranjang untuk semua view
        View::composer('*', function ($view) {
            $totalKeranjang = 0;

            if (Auth::check()) {
                $totalKeranjang = Keranjang::getTotalJumlahByUser(Auth::id());
            }

            $view->with('totalKeranjang', $totalKeranjang);
        });

        // Notifikasi pesanan "dikirim" & chat baru untuk pembeli
        View::composer('layouts.public', function ($view) {
            $notifikasiDikirim = collect(); // default kosong
            $jumlahChatBaru = 0;

            if (Auth::check()) {
                if (Auth::user()->role === 'pembeli') {
                    $notifikasiDikirim = Order::where('user_id', Auth::id())
                        ->where('status_pesanan', 'dikirim')
                        ->latest()
                        ->get();
                }
                $jumlahChatBaru = \App\Models\Chat::where('receiver_id', Auth::id())
                    ->where('is_ai', false)
                    ->where('is_read', false)
                    ->count();
            }

            $view->with([
                'notifikasiDikirim' => $notifikasiDikirim,
                'jumlahChatBaru' => $jumlahChatBaru,
            ]);
        });

        // Notifikasi pesanan untuk penjual
        View::composer('partials.sidebar-penjual', function ($view) {
            $notifPesananComplete = collect();
            $notifStatusPesanan = collect();

            if (Auth::check() && Auth::user()->role === 'penjual') {
                $umkm = Umkm::where('user_id', Auth::id())->first();
                $produkIds = $umkm ? Produk::where('umkm_id', $umkm->id)->pluck('id') : collect();

                $notifPesananComplete = Order::whereIn('produk_id', $produkIds)
                    ->where('status', 'complete')
                    ->where(function ($q) {
                        $q->whereNull('status_pesanan')
                          ->orWhere('status_pesanan', 'menunggu_diproses');
                    })
                    ->latest()
                    ->get();

                $notifStatusPesanan = Order::whereIn('produk_id', $produkIds)
                    ->whereIn('status_pesanan', ['diterima', 'belum_diterima'])
                    ->latest()
                    ->get();
            }

            $view->with([
                'notifPesananComplete' => $notifPesananComplete,
                'notifStatusPesanan' => $notifStatusPesanan,
            ]);
        });
    }
}
