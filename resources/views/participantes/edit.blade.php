@extends('layouts.app')

@section('title', 'Editar Participante')
@section('page-title', 'Editar Participante')

@section('topbar-actions')
    <a href="{{ route('participantes.show', $participante) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Cancelar
    </a>
@endsection

@push('styles')
<style>
    select.form-control option {
        background-color: var(--bg-card, #1e293b);
        color: var(--text-primary, #f8fafc);
    }
</style>
@endpush

@section('content')
<div style="max-width:700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pencil" style="color:var(--accent-gold);margin-right:8px"></i>{{ $participante->Nombre }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('participantes.update', $participante) }}">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="Nombre">Nombre completo *</label>
                        <input id="Nombre" name="Nombre" type="text" class="form-control"
                               value="{{ old('Nombre', $participante->Nombre) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="RFC">RFC</label>
                        <input id="RFC" name="RFC" type="text" class="form-control" maxlength="20"
                               value="{{ old('RFC', $participante->RFC) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Telefono">Teléfono</label>
                        <input id="Telefono" name="Telefono" type="text" class="form-control" maxlength="15"
                               value="{{ old('Telefono', $participante->Telefono) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Sucursal">Sucursal</label>
                        <input id="Sucursal" name="Sucursal" type="text" class="form-control"
                               value="{{ old('Sucursal', $participante->Sucursal) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Vendedor">Vendedor</label>
                        <input id="Vendedor" name="Vendedor" type="text" class="form-control"
                               value="{{ old('Vendedor', $participante->Vendedor) }}">
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="Proveedor">Proveedor / Empresa</label>
                        <input id="Proveedor" name="Proveedor" type="text" class="form-control"
                               value="{{ old('Proveedor', $participante->Proveedor) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Puesto">Puesto</label>
                        @php $p = old('Puesto', $participante->Puesto); @endphp
                        <select id="Puesto" name="Puesto" class="form-control">
                            <option value="" {{ $p == '' ? 'selected' : '' }}>Selecciona un puesto</option>
                            <option value="Ingeniero" {{ $p == 'Ingeniero' ? 'selected' : '' }}>Ingeniero</option>
                            <option value="Electricista" {{ $p == 'Electricista' ? 'selected' : '' }}>Electricista</option>
                            <option value="Ayudante" {{ $p == 'Ayudante' ? 'selected' : '' }}>Ayudante</option>
                            <option value="Supervisor" {{ $p == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Compras" {{ $p == 'Compras' ? 'selected' : '' }}>Compras</option>
                            <option value="Mantenimiento" {{ $p == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="Jefe de Área" {{ $p == 'Jefe de Área' ? 'selected' : '' }}>Jefe de Área</option>
                            <option value="Otro" {{ $p == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Puntos">Puntos</label>
                        <input id="Puntos" name="Puntos" type="number" min="0" class="form-control"
                               value="{{ old('Puntos', $participante->Puntos) }}">
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
                    <a href="{{ route('participantes.show', $participante) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
