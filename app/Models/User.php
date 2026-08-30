<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        if ($this->role === 'admin' && session('preview_role') === 'tim_bpk') {
            return false;
        }
        return $this->role === 'admin';
    }

    public function isTimBpk(): bool
    {
        if ($this->role === 'admin' && session('preview_role') === 'tim_bpk') {
            return true;
        }
        return $this->role === 'tim_bpk';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Admin Pengelola',
            'tim_bpk' => 'Tim Pemeriksa',
            default => $this->role,
        };
    }

    public function suratDibuat()
    {
        return $this->hasMany(Surat::class, 'created_by');
    }

    public function permintaanDitangani()
    {
        return $this->hasMany(PermintaanData::class, 'penanggung_jawab');
    }

    public function dokumenDiupload()
    {
        return $this->hasMany(Dokumen::class, 'uploaded_by');
    }

    public function pemeriksaan()
    {
        return $this->belongsToMany(Pemeriksaan::class, 'pemeriksaan_user');
    }
}
