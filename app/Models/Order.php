<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'produk_id',
        'name',
        'alamat',
        'phone',
        'jumlah',
        'total_harga',
        'status',
        'status_pesanan',
        'order_id_midtrans',
        'snap_token',
        'resi_pengiriman',
        'kurir',
        'foto_bukti_pengiriman',
        'tanggal_dikirim',
        'catatan_pengiriman',
        'batal_alasan',
        'batal_at',
        'dikemas_at',
        'dikirim_at',
        'diterima_at',
        'is_escrow_released',
    ];

    protected $casts = [
        'tanggal_dikirim' => 'datetime',
        'batal_at' => 'datetime',
        'dikemas_at' => 'datetime',
        'dikirim_at' => 'datetime',
        'diterima_at' => 'datetime',
        'is_escrow_released' => 'boolean',
        'total_harga' => 'integer',
        'jumlah' => 'integer',
    ];

    public function komplain()
    {
        return $this->hasOne(Komplain::class, 'order_id');
    }

    public function getNoResiAttribute()
    {
        return $this->attributes['resi_pengiriman'] ?? null;
    }

    public function setNoResiAttribute($value)
    {
        $this->attributes['resi_pengiriman'] = $value;
    }

    public function getKurirEkspedisiAttribute()
    {
        return $this->attributes['kurir'] ?? null;
    }

    public function setKurirEkspedisiAttribute($value)
    {
        $this->attributes['kurir'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produks()
    {
        return $this->belongsToMany(
            Produk::class,
            'order_produk',  // nama tabel pivot
            'orders_id',     // foreign key order di pivot
            'produks_id'     // foreign key produk di pivot
        );
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class, 'orders_id');
    }
}
