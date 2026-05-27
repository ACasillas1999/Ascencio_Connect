<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table      = 'agenda';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'Salon', 'Fecha', 'Horario',
        'Actividad', 'Puntos_Asistencia',
    ];

    protected $casts = ['Fecha' => 'date'];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }

    public function clases()
    {
        return $this->hasMany(Clase::class, 'ID_Agenda', 'ID');
    }
}
