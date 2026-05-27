<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clase extends Model
{
    protected $table      = 'clase';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Agenda', 'ID_Participante', 'Asistio',
        'Asistencia_Fecha', 'Asistencia_Usuario', 'Tipo_Inscripcion',
    ];

    protected $casts = [
        'Asistio'          => 'boolean',
        'Asistencia_Fecha' => 'datetime',
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'ID_Agenda', 'ID');
    }

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'ID_Participante', 'ID');
    }
}
