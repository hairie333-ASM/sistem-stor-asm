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
        'staff_id',
        'password',
        'role_id',
        'department',
        'position',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (!$this->role) return false;
        if ($this->role->name === 'admin') return true;
        
        if (is_array($roles)) {
            return in_array($this->role->name, $roles);
        }
        return $this->role->name === $roles;
    }

    public function isRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    public function isPemohon(): bool
    {
        return $this->isRole('pemohon');
    }

    public function isPegawaiPelulus(): bool
    {
        return $this->isRole('pegawai_pelulus');
    }

    public function isPegawaiPenerima(): bool
    {
        return $this->isRole('pegawai_penerima');
    }

    public function isPegawaiStor(): bool
    {
        return $this->isRole('pegawai_stor');
    }

    public function isPemverifikasi(): bool
    {
        return $this->isRole('pemverifikasi');
    }

    public function isUrusSetiaPelupusan(): bool
    {
        return $this->isRole('urus_setia_pelupusan');
    }

    public function isUrusSetiaKehilangan(): bool
    {
        return $this->isRole('urus_setia_kehilangan');
    }

    public function isKetuaJabatan(): bool
    {
        return $this->isRole('ketua_jabatan');
    }

    public function isAdmin(): bool
    {
        return $this->isRole('admin');
    }

    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class, 'requester_id');
    }
}
