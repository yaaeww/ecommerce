<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function show($id)
    {
        $userId = Auth::id();
        $order = Order::with(['produk.umkm', 'produk.diskon', 'user', 'komplain'])
            ->where('id', $id)
            ->when(Auth::user()->role !== 'admin', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->firstOrFail();

        return view('pembeli.invoice', compact('order'));
    }

    public function generatePdf($id)
    {
        $userId = Auth::id();
        $order = Order::with(['produk.umkm', 'produk.diskon', 'user', 'komplain'])
            ->where('id', $id)
            ->when(Auth::user()->role !== 'admin', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('penjual.pesanan.invoice_pdf', compact('order'));

        return $pdf->download('Invoice-JuraganPelem-' . ($order->order_id_midtrans ?: $order->id) . '.pdf');
    }
}
