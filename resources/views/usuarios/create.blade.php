@extends('layouts.app')

@section('title', 'Crear Usuario')
@section('page-title', 'Crear Usuario')

@section('topbar-actions')
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@push('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }
        .form-control {
            font-size: 16px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Nuevo Usuario</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('usuarios.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">Nombre de Usuario</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Ej. jperez" value="{{ old('username') }}" required>
                @error('username') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                @error('password') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Asignar Rol</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    @foreach($roles as $rol)
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                            <input type="radio" name="rol" value="{{ $rol }}" {{ old('rol') === $rol ? 'checked' : '' }} required style="width: 16px; height: 16px; accent-color: var(--accent-gold);">
                            {{ $rol }}
                        </label>
                    @endforeach
                </div>
                @error('rol') <small style="color:#ef4444; font-size:12px;">Selecciona un rol.</small> @enderror
            </div>

            <div class="form-group" id="evento-selector" style="display: {{ in_array(old('rol'), ['Evento', 'Proveedor']) ? 'block' : 'none' }}; margin-top:15px;">
                <label class="form-label" for="ID_Evento">Evento Asignado (Para Rol Evento y Proveedor)</label>
                <select name="ID_Evento" id="ID_Evento" class="form-control">
                    <option value="">Selecciona un evento...</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->ID }}" {{ old('ID_Evento') == $ev->ID ? 'selected' : '' }}>
                            {{ $ev->name_evento }} ({{ $ev->fecha_inicio->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('ID_Evento') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="rol"]');
    const eventoSelector = document.getElementById('evento-selector');
    const idEventoSelect = document.getElementById('ID_Evento');

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'Evento' || this.value === 'Proveedor') {
                eventoSelector.style.display = 'block';
                idEventoSelect.setAttribute('required', 'required');
            } else {
                eventoSelector.style.display = 'none';
                idEventoSelect.removeAttribute('required');
                idEventoSelect.value = '';
            }
        });
    });
});
</script>
@endsection
