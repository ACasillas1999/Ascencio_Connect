<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremioEvento extends Model
{
    protected $table      = 'premios_evento';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_Evento', 'NombrePremio', 'PuntosNecesarios', 'Disponible',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'ID_Evento', 'ID');
    }

    public function canjes()
    {
        return $this->hasMany(Canje::class, 'ID_Premio', 'ID');
    }
}
