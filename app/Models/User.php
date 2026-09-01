<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',

    ];
    public function produk()
    {
        return $this->hasMany(Produk::class);
    }
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPenjual()
    {
        return $this->role === 'penjual';
    }

    public function isPembeli()
    {
        return $this->role === 'pembeli';
    }
    public function umkm()
    {
        return $this->hasOne(Umkm::class, 'user_id');
    }

    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function ulasans()
    {
        return $this->hasMany(Ulasan::class, 'users_id');
    }
    public function chats()
    {
        return $this->hasMany(\App\Models\Chat::class);
    }

    public function alamats()
    {
        return $this->hasMany(Alamat::class);
    }

    public function komplains()
    {
        return $this->hasMany(Komplain::class);
    }






    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
