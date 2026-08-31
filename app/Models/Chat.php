<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'umkm_id',
        'message',
        'is_ai',
        'is_read',
    ];

    // Accessor untuk membaca pesan terdekripsi
    public function getMessageAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            // Jika decrypt gagal, kembalikan value asli (untuk data lama)
            return $value;
        }
    }

    // Mutator untuk menyimpan pesan terenkripsi
    public function setMessageAttribute($value)
    {
        $this->attributes['message'] = Crypt::encryptString($value);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id');
    }
}
