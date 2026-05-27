@extends('layouts.app')

@section('title', 'Editar: '.$evento->name_evento)
@section('page-title', 'Editar Evento')

@section('topbar-actions')
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Cancelar
    </a>
@endsection

@section('content')
<div style="max-width:700px">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pencil" style="color:var(--accent-gold);margin-right:8px"></i>{{ $evento->name_evento }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('eventos.update', $evento) }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="name_evento">Nombre del Evento *</label>
                        <input id="name_evento" name="name_evento" type="text" class="form-control"
                               value="{{ old('name_evento', $evento->name_evento) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="fecha_inicio">Fecha Inicio *</label>
                        <input id="fecha_inicio" name="fecha_inicio" type="date" class="form-control"
                               value="{{ old('fecha_inicio', $evento->fecha_inicio->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="fecha_fin">Fecha Fin *</label>
                        <input id="fecha_fin" name="fecha_fin" type="date" class="form-control"
                               value="{{ old('fecha_fin', $evento->fecha_fin->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="duracion">Duración</label>
                        <input id="duracion" name="duracion" type="text" class="form-control"
                               value="{{ old('duracion', $evento->duracion) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="capacidad">Capacidad *</label>
                        <input id="capacidad" name="capacidad" type="number" min="1" class="form-control"
                               value="{{ old('capacidad', $evento->capacidad) }}" required>
                    </div>

                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="ubicacion">Ubicación / Venue *</label>
                        <select id="ubicacion" name="ubicacion" class="form-control" required>
                            <option value="">Seleccione una ubicación</option>
                            @foreach($ubicaciones as $ub)
                                <option value="{{ $ub->Nombre }}" {{ old('ubicacion', $evento->ubicacion) === $ub->Nombre ? 'selected' : '' }}>{{ $ub->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="estado">Estado *</label>
                        <select id="estado" name="estado" class="form-control" required>
                            @foreach(['PRÓXIMO','EN CURSO','FINALIZADO'] as $opt)
                            <option value="{{ $opt }}" {{ old('estado', $evento->estado) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="tipo_puntos">Sistema de Puntos</label>
                        <select id="tipo_puntos" name="tipo_puntos" class="form-control">
                            @foreach(['ninguno','individual','grupal'] as $opt)
                            <option value="{{ $opt }}" {{ old('tipo_puntos', $evento->tipo_puntos) === $opt ? 'selected' : '' }}>{{ ucfirst($opt) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top:20px; border-top:1px solid #334155; padding-top:16px;">
                    <h5 style="color:var(--accent-gold); margin-bottom:12px;"><i class="bi bi-gear"></i> Configuración Adicional</h5>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                        
                        <div class="form-group">
                            <label class="form-label" for="machote_gafete">Machote Gafete (JPG/PNG)</label>
                            <input id="machote_gafete" name="machote_gafete" type="file" class="form-control" accept="image/*">
                            @if($evento->machote_gafete)
                                <small style="display:block;margin-top:4px;">Actual: <a href="{{ Storage::url($evento->machote_gafete) }}" target="_blank">Ver imagen</a></small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="machote_horario">Machote Horario (JPG/PNG)</label>
                            <input id="machote_horario" name="machote_horario" type="file" class="form-control" accept="image/*">
                            @if($evento->machote_horario)
                                <small style="display:block;margin-top:4px;">Actual: <a href="{{ Storage::url($evento->machote_horario) }}" target="_blank">Ver imagen</a></small>
                            @endif
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <input type="checkbox" id="enviar_whatsapp_auto" name="enviar_whatsapp_auto" value="1" {{ old('enviar_whatsapp_auto', $evento->enviar_whatsapp_auto) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent-gold);">
                            <label for="enviar_whatsapp_auto" style="margin:0;cursor:pointer;">Enviar Gafete y Horario por WhatsApp al registrar</label>
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <input type="checkbox" id="clases_obligatorias" name="clases_obligatorias" value="1" {{ old('clases_obligatorias', $evento->clases_obligatorias) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent-gold);">
                            <label for="clases_obligatorias" style="margin:0;cursor:pointer;">Registro a Clases/Actividades es Obligatorio</label>
                        </div>

                        <div class="form-group" style="grid-column:1/-1;">
                            <label class="form-label" for="wa_template_name">Plantilla de WhatsApp (opcional)</label>
                            <input id="wa_template_name" name="wa_template_name" type="text" class="form-control"
                                   value="{{ old('wa_template_name', $evento->wa_template_name ?? 'ascencio_day_len_2026') }}" placeholder="Ej: ascencio_day_len_2026">
                            <small style="color:var(--text-muted); display:block; margin-top:4px;">Nombre exacto de la plantilla en Meta Cloud API.</small>
                        </div>

                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Actualizar
                    </button>
                    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
