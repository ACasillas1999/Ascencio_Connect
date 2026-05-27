<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuntosProveedor extends Model
{
    protected $table      = 'puntos_proveedor';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'id_participante', 'id_evento', 'usuario', 'puntos', 'fecha',
    ];

    protected $casts = ['fecha' => 'datetime'];

    public function participante()
    {
        return $this->belongsTo(Participante::class, 'id_participante', 'ID');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'ID');
    }
}
