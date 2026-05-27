<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table      = 'actividades';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'Actividad', 'Descripcion',
        'capacidad', 'Exclusiva', 'Puntos_Default',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }

    /**
     * Obtiene el nombre de la columna para Route Model Binding.
     */
    public function getRouteKeyName()
    {
        return 'ID';
    }
}
