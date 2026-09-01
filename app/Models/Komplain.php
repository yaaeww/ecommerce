<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komplain extends Model
{
    use HasFactory;

    protected $table = 'komplains';

    protected $fillable = [
        'order_id',
        'user_id',
        'tipe_komplain',
        'deskripsi',
        'foto_bukti',
        'video_unboxing',
        'solusi_diminta',
        'status',
        'catatan_admin',
        'nominal_refund',
        'diproses_at',
        'selesai_at'
    ];

    protected $casts = [
        'diproses_at' => 'datetime',
        'selesai_at' => 'datetime',
        'nominal_refund' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getLabelTipeAttribute()
    {
        return [
            'buah_busuk' => 'Buah Busuk / Lewat Matang',
            'kardus_rusak' => 'Kardus Rusak / Pecah',
            'berat_kurang' => 'Timbangan / Berat Kurang',
            'tidak_sesuai' => 'Varietas Tidak Sesuai',
            'lainnya' => 'Masalah Lainnya',
        ][$this->tipe_komplain] ?? 'Komplain Buah';
    }

    public function getBadgeColorAttribute()
    {
        return [
            'diajukan' => 'bg-amber-50 text-amber-700 border-amber-200',
            'diproses' => 'bg-blue-50 text-blue-700 border-blue-200',
            'disetujui' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'ditolak' => 'bg-rose-50 text-rose-700 border-rose-200',
            'selesai' => 'bg-slate-100 text-slate-700 border-slate-200',
        ][$this->status] ?? 'bg-slate-100 text-slate-700 border-slate-200';
    }
}
