<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Keranjang extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'keranjangservice';
    }
}
