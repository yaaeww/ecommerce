<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BantuanController extends Controller
{
    /**
     * Halaman Kebijakan Garansi 100%
     */
    public function garansi()
    {
        return view('bantuan.garansi');
    }

    /**
     * Halaman Panduan Mitra Petani & UMKM
     */
    public function panduanMitra()
    {
        return view('bantuan.panduan-mitra');
    }

    /**
     * Halaman Syarat & Ketentuan Layanan
     */
    public function syaratKetentuan()
    {
        return view('bantuan.syarat-ketentuan');
    }

    /**
     * Halaman Kebijakan Privasi Data
     */
    public function kebijakanPrivasi()
    {
        return view('bantuan.kebijakan-privasi');
    }
}
