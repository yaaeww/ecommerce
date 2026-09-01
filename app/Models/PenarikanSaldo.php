<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenarikanSaldo extends Model
{
    use HasFactory;

    protected $table = 'penarikan_saldos';

    protected $fillable = [
        'umkm_id',
        'jumlah',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'status',
        'bukti_transfer',
        'catatan_admin',
        'processed_at',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
