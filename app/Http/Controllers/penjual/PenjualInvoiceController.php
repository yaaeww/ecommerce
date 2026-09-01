<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PenjualInvoiceController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();

        // Ambil order dengan produk yang terkait ke UMKM milik penjual saat ini
        $order = Order::with(['produk', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        return view('penjual.invoice.show', compact('order'));
    }

    public function generatePdf($id)
    {
        $user = Auth::user();

        // Ambil order dengan produk dan user yang terkait dengan UMKM penjual saat ini
        $order = Order::with(['produk', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        // Generate PDF dari view, passing variabel order
        $pdf = Pdf::loadView('penjual.pesanan.invoice_pdf', compact('order'));

        return $pdf->download('invoice_'.$order->id.'.pdf');
    }

    public function shippingLabel($id)
    {
        $user = Auth::user();

        $order = Order::with(['produk.umkm.user', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();

        return view('penjual.pesanan.shipping-label', compact('order'));
    }
}
