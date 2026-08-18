<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $table      = 'evento';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'name_evento', 'duracion', 'estado',
        'fecha_inicio', 'fecha_fin', 'ubicacion',
        'capacidad', 'tipo_puntos',
        'machote_gafete', 'machote_horario',
        'enviar_whatsapp_auto', 'clases_obligatorias',
        'wa_template_name',
        'gafete_qr_x', 'gafete_qr_y', 'gafete_qr_size', 'gafete_nombre_x', 'gafete_nombre_y', 'gafete_font_size',
        'gafete_id_x', 'gafete_id_y', 'gafete_id_font_size', 'gafete_color_nombre', 'gafete_color_id', 'gafete_font_family',
        'horario_nombre_x', 'horario_nombre_y', 'horario_id_x', 'horario_id_y', 'horario_lista_x', 'horario_lista_y', 'horario_lista_w', 'horario_lista_h', 
        'horario_font_size', 'horario_id_font_size', 'horario_lista_font_size', 'horario_color_nombre', 'horario_color_id', 'horario_color_lista', 'horario_font_family'
    ];

    protected $casts = [
        'fecha_inicio'         => 'date',
        'fecha_fin'            => 'date',
        'enviar_whatsapp_auto' => 'boolean',
        'clases_obligatorias'  => 'boolean',
    ];

    public function participantes()
    {
        return $this->hasMany(Participante::class, 'ID_Evento', 'ID');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'ID_Evento', 'ID');
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'ID_Evento', 'ID');
    }

    public function premios()
    {
        return $this->hasMany(PremioEvento::class, 'ID_Evento', 'ID');
    }

    public function proveedores()
    {
        return $this->hasMany(ProveedorEvento::class, 'ID_Evento', 'ID');
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->estado) {
            'EN CURSO'   => 'badge-success',
            'FINALIZADO' => 'badge-secondary',
            default      => 'badge-warning',
        };
    }
}
