<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show($id)
    {
        $userId = Auth::id();
        $order = Order::with('produk')
            ->where('id', $id)
            ->when(Auth::user()->role !== 'admin', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->firstOrFail();

        return view('pembeli.invoice', compact('order'));
    }
}
