<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Set nama tabel eksplisit agar tidak error
    protected $table = 'produks';

    protected $fillable = [
        'nama',
        'harga',
        'harga_coret',
        'berat_gram',
        'deskripsi',
        'gambar',
        'user_id',
        'stok',
        'is_active',
        'rating',
        'umkm_id',
        'kategori_produk_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'harga' => 'float',
        'harga_coret' => 'float',
        'berat_gram' => 'integer',
        'stok' => 'integer',
        'rating' => 'float',
    ];

    public function getDiskonPersenAttribute()
    {
        if ($this->harga_coret && $this->harga_coret > $this->harga) {
            return round((($this->harga_coret - $this->harga) / $this->harga_coret) * 100);
        }
        return 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // =======================
    // RELASI
    // =======================

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }

    public function kategoriProduk()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }
    public function kategori()
    {
        return $this->belongsTo(KategoriProduk::class, 'kategori_produk_id');
    }

    public function penjual()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class); // Jika kamu memang butuh dua nama relasi berbeda
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function orderItems()
    {
        return $this->hasMany(Order::class); // Ini sama dengan `order()`, bisa digabung
    }

    public function diskon()
    {
        return $this->hasOne(Diskon::class, 'produks_id');
    }
    public function ulasan()
    {
        return $this->hasMany(Ulasan::class, 'produks_id');
    }
    // =======================
    // ACCESSORS & SCOPES
    // =======================

    public function getGambarAttribute($value)
    {
        if (!$value) return null;
        $clean = ltrim($value, '/');
        if (str_starts_with($clean, 'storage/')) {
            return substr($clean, 8);
        }
        return $clean;
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getHargaSetelahDiskonAttribute()
    {
        if ($this->diskon && now()->between($this->diskon->tanggal_mulai, $this->diskon->tanggal_berakhir)) {
            return round($this->harga - ($this->harga * $this->diskon->persen_diskon / 100));
        }

        return $this->harga;
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true);
    }

    // =======================
    // METODE TAMBAHAN (ADMIN)
    // =======================

    public function adminIndex()
    {
        $produks = self::with(['penjual', 'kategori'])
            ->latest()
            ->get();

        return view('admin.dashboard', compact('produks'));
    }
}
