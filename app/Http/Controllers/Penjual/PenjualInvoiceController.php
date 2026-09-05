<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

use Laravel\Sanctum\PersonalAccessToken;

class PenjualInvoiceController extends Controller
{
    /**
     * Resolve user from web session or Sanctum Bearer token from query string
     */
    protected function resolveUser(Request $request)
    {
        if (Auth::check()) {
            return Auth::user();
        }

        if ($request->filled('token')) {
            $token = PersonalAccessToken::findToken($request->query('token'));
            if ($token && $token->tokenable) {
                return $token->tokenable;
            }
        }

        return null;
    }

    public function show($id)
    {
        $user = Auth::user();

        // Ambil order dengan produk yang terkait ke UMKM milik penjual saat ini
        $order = Order::with(['produk.umkm.user', 'produk.diskon', 'user'])
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
        $order = Order::with(['produk.umkm.user', 'user'])
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

    /**
     * Shipping label accessible via query token for mobile seller printing
     */
    public function shippingLabelWithToken(Request $request, $id)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return redirect()->route('login');
        }

        $order = Order::with(['produk.umkm.user', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                if ($user->role !== 'admin') {
                    $query->where('user_id', $user->id);
                }
            })
            ->firstOrFail();

        return view('penjual.pesanan.shipping-label', compact('order'));
    }

    /**
     * Invoice view accessible via query token for mobile seller
     */
    public function invoiceWithToken(Request $request, $id)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return redirect()->route('login');
        }

        $order = Order::with(['produk.umkm.user', 'produk.diskon', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                if ($user->role !== 'admin') {
                    $query->where('user_id', $user->id);
                }
            })
            ->firstOrFail();

        return view('penjual.invoice.show', compact('order'));
    }

    /**
     * Invoice PDF download accessible via query token for mobile seller
     */
    public function generatePdfWithToken(Request $request, $id)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return redirect()->route('login');
        }

        $order = Order::with(['produk.umkm.user', 'user'])
            ->where('id', $id)
            ->whereHas('produk.umkm', function ($query) use ($user) {
                if ($user->role !== 'admin') {
                    $query->where('user_id', $user->id);
                }
            })
            ->firstOrFail();

        $pdf = Pdf::loadView('penjual.pesanan.invoice_pdf', compact('order'));

        return $pdf->download('invoice_'.$order->id.'.pdf');
    }
}
