<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table      = 'ubicaciones';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'Nombre', 'Direccion', 'Salones',
        'Capacidad_por_salon', 'capacidad_total',
    ];
}
