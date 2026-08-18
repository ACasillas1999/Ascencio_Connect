<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model
{
    protected $table      = 'salones';
    protected $primaryKey = 'ID';
    public $timestamps    = true;

    protected $fillable = [
        'ubicacion_id',
        'Nombre',
    ];

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id', 'ID');
    }
}
