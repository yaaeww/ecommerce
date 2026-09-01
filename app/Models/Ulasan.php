<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';  // sesuaikan dengan nama tabel kamu

    protected $fillable = [
        'users_id',
        'produks_id',
        'orders_id',
        'bintang',
        'ulasan',
        'status_moderasi',
        'catatan_moderasi',
        'balasan_penjual',
        'balasan_penjual_at',
    ];

    protected $casts = [
        'balasan_penjual_at' => 'datetime',
        'bintang' => 'integer',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produks_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'orders_id');
    }
}
