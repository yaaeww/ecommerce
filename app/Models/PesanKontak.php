<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanKontak extends Model
{
    use HasFactory;

    protected $table = 'pesan_kontaks';

    protected $fillable = [
        'user_id',
        'nama',
        'email',
        'no_telepon',
        'kategori',
        'subjek',
        'pesan',
        'status',
        'balasan_admin',
        'dibalas_oleh',
        'dibalas_pada',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'dibalas_pada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function adminBalas()
    {
        return $this->belongsTo(User::class, 'dibalas_oleh');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('status', 'belum_dibaca');
    }

    public function scopeByStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByCategory($query, $kategori)
    {
        if ($kategori && $kategori !== 'all') {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    // Helper Accessors
    public function getKategoriLabelAttribute()
    {
        return match ($this->kategori) {
            'kerjasama_umkm' => '🤝 Kerjasama Mitra UMKM',
            'partai_besar' => '📦 Pesanan Partai Besar / B2B',
            'kendala_transaksi' => '⚠️ Kendala Transaksi / Pembayaran',
            'masukan' => '💡 Masukan & Saran',
            default => '💬 Pertanyaan Umum',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'belum_dibaca' => [
                'label' => 'Belum Dibaca',
                'bg' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot' => 'bg-rose-500',
            ],
            'dibaca' => [
                'label' => 'Sudah Dibaca',
                'bg' => 'bg-amber-50 text-amber-700 border-amber-200',
                'dot' => 'bg-amber-500',
            ],
            'dibalas' => [
                'label' => 'Sudah Dibalas',
                'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                'dot' => 'bg-emerald-500',
            ],
            'diarsipkan' => [
                'label' => 'Diarsipkan',
                'bg' => 'bg-slate-50 text-slate-600 border-slate-200',
                'dot' => 'bg-slate-400',
            ],
            default => [
                'label' => $this->status,
                'bg' => 'bg-slate-50 text-slate-600 border-slate-200',
                'dot' => 'bg-slate-400',
            ],
        };
    }
}
