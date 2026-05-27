<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Canje extends Model
{
    protected $table      = 'canjes';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'ID_Participante', 'ID_Premio', 'Cantidad', 'Fecha',
    ];

    protected $casts = ['Fecha' => 'datetime'];

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'ID_Participante', 'ID');
    }

    public function premio()
    {
        return $this->belongsTo(PremioEvento::class, 'ID_Premio', 'ID');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }
}
