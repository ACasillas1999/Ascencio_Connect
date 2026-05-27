<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProveedorEvento extends Model
{
    protected $table      = 'proveedor_evento';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'NombreProveedor', 'Puntos', 'Activo',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }
}
