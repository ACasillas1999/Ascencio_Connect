<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participante extends Model
{
    protected $table      = 'participante';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'Sucursal', 'Vendedor', 'Nombre', 'RFC',
        'Proveedor', 'QR_Code', 'Telefono', 'Ruta_Gafete',
        'Ruta_Horario', 'Puntos', 'Puesto',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }

    public function clases()
    {
        return $this->hasMany(Clase::class, 'ID_Participante', 'ID');
    }

    public function canjes()
    {
        return $this->hasMany(Canje::class, 'ID_Participante', 'ID');
    }

    public function puntosProveedor()
    {
        return $this->hasMany(PuntosProveedor::class, 'id_participante', 'ID');
    }
}
