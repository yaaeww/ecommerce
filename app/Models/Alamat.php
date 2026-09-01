<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $table = 'alamats';

    protected $fillable = [
        'user_id',
        'label',
        'nama_penerima',
        'no_hp',
        'provinsi',
        'kota_kabupaten',
        'kecamatan',
        'kode_pos',
        'alamat_lengkap',
        'patokan',
        'is_utama'
    ];

    protected $casts = [
        'is_utama' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedAlamatAttribute()
    {
        $parts = [
            $this->alamat_lengkap,
            $this->kecamatan ? "Kec. {$this->kecamatan}" : null,
            $this->kota_kabupaten,
            $this->provinsi,
            $this->kode_pos ? "({$this->kode_pos})" : null,
        ];

        return implode(', ', array_filter($parts));
    }
}
