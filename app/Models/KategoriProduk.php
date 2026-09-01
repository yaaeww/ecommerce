<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'slug', 'gambar', 'parent_id'];
    protected $appends = ['gambar_url'];

    public function getGambarAttribute($value)
    {
        if (!$value) return null;
        $clean = ltrim($value, '/');
        if (str_starts_with($clean, 'storage/')) {
            return substr($clean, 8);
        }
        return $clean;
    }

    /**
     * Accessor untuk mendapatkan URL lengkap gambar kategori.
     */
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) return null;
        
        $clean = ltrim($this->gambar, '/');

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        if (file_exists(public_path('storage/' . $clean))) {
            return asset('storage/' . $clean);
        }

        if (file_exists(public_path('storage/kategori/' . $clean))) {
            return asset('storage/kategori/' . $clean);
        }

        if (file_exists(public_path('aset/' . $clean))) {
            return asset('aset/' . $clean);
        }

        return asset('storage/' . $clean);
    }

    /**
     * Relasi ke produk yang termasuk dalam kategori ini.
     */
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
    


    // Model: KategoriProduk.php
    public function children()
    {
        return $this->hasMany(KategoriProduk::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(KategoriProduk::class, 'parent_id');
    }


    /**
     * Cek apakah kategori ini memiliki sub-kategori.
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }
    public function subkategoris()
    {
        return $this->hasMany(KategoriProduk::class, 'parent_id');
    }
    public function allChildrenIds()
    {
        $ids = collect();

        foreach ($this->children as $child) {
            $ids->push($child->id);
            $ids = $ids->merge($child->allChildrenIds());
        }

        return $ids;
    }
}
