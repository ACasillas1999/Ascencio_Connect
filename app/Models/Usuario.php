<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table      = 'usuarios';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'username',
        'password',
        'Rol',
        'password_visible',
        'Activo',
        'remember_token',
        'tipo_kiosko',
        'ID_Evento'
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }

    protected $hidden = [
        'password',
        'password_visible',
        'Activo',
        'remember_token',
        'tipo_kiosko',
    ];

    /* ---------- helpers ---------- */

    public function esAdmin(): bool
    {
        return \App\Helpers\Permisos::normalizar($this->Rol) === 'Admin';
    }

    public function esGerente(): bool
    {
        return in_array($this->Rol, ['Admin', 'Gerente']);
    }

    public function esVendedor(): bool
    {
        return $this->Rol === 'Vendedor';
    }

    public function esProveedor(): bool
    {
        return $this->Rol === 'proveedor';
    }

    public function esEvento(): bool
    {
        return $this->Rol === 'Evento';
    }

    /* Alias requerido por Laravel Auth */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthIdentifierName()
    {
        return 'ID';
    }
}
