@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

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
        <h2 class="card-title">Editar Usuario: {{ $usuario->username }}</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('usuarios.update', $usuario->ID) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="username">Nombre de Usuario</label>
                <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $usuario->username) }}" required {{ $usuario->username === 'Admin' ? 'readonly' : '' }}>
                @error('username') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Nueva Contraseña <small style="color:#94a3b8;">(Deja en blanco para no cambiar)</small></label>
                <input type="password" name="password" id="password" class="form-control" placeholder="••••••••">
                @error('password') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Asignar Rol</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    @foreach($roles as $rol)
                        @php
                            $isChecked = \App\Helpers\Permisos::normalizar(old('rol', $usuario->Rol)) === $rol;
                        @endphp
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                            <input type="radio" name="rol" value="{{ $rol }}"
                                {{ $isChecked ? 'checked' : '' }}
                                {{ $usuario->username === 'Admin' && $rol === 'Admin' ? 'checked onclick="return false;"' : '' }}
                                required style="width: 16px; height: 16px; accent-color: var(--accent-gold);">
                            {{ $rol }}
                        </label>
                    @endforeach
                </div>
                @error('rol') <small style="color:#ef4444; font-size:12px;">Selecciona un rol.</small> @enderror
            </div>

            <div class="form-group" id="kiosko-selector" style="display: {{ \App\Helpers\Permisos::normalizar(old('rol', $usuario->Rol)) === 'Kiosko' ? 'block' : 'none' }}; margin-top:15px;">
                <label class="form-label" for="tipo_kiosko">Modo / Tipo de Kiosko</label>
                <select name="tipo_kiosko" id="tipo_kiosko" class="form-control">
                    <option value="hibrido" {{ old('tipo_kiosko', $usuario->tipo_kiosko ?? 'hibrido') == 'hibrido' ? 'selected' : '' }}>🔄 Híbrido (Cámara + Código ID / Teclado)</option>
                    <option value="camara" {{ old('tipo_kiosko', $usuario->tipo_kiosko ?? 'hibrido') == 'camara' ? 'selected' : '' }}>📷 Solo Cámara QR</option>
                    <option value="codigo" {{ old('tipo_kiosko', $usuario->tipo_kiosko ?? 'hibrido') == 'codigo' ? 'selected' : '' }}>🔍 Solo Código ID / Lector USB</option>
                </select>
            </div>

            <div class="form-group" id="evento-selector" style="display: {{ in_array(\App\Helpers\Permisos::normalizar(old('rol', $usuario->Rol)), ['Evento', 'Proveedor', 'Kiosko']) ? 'block' : 'none' }}; margin-top:15px;">
                <label class="form-label" for="ID_Evento">Evento Asignado (Para Rol Evento, Proveedor y Kiosko)</label>
                <select name="ID_Evento" id="ID_Evento" class="form-control">
                    <option value="">Selecciona un evento...</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->ID }}" {{ old('ID_Evento', $usuario->ID_Evento) == $ev->ID ? 'selected' : '' }}>
                            {{ $ev->name_evento }} ({{ $ev->fecha_inicio->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('ID_Evento') <small style="color:#ef4444; font-size:12px;">{{ $message }}</small> @enderror
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Cambios
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

    function checkEventoRequired() {
        let isEvento = false;
        let isKiosko = false;
        radios.forEach(r => {
            if (r.checked && (r.value === 'Evento' || r.value === 'Proveedor' || r.value === 'Kiosko')) isEvento = true;
            if (r.checked && r.value === 'Kiosko') isKiosko = true;
        });
        
        const kioskoSelector = document.getElementById('kiosko-selector');
        if (kioskoSelector) {
            kioskoSelector.style.display = isKiosko ? 'block' : 'none';
        }

        if (isEvento) {
            eventoSelector.style.display = 'block';
            idEventoSelect.setAttribute('required', 'required');
        } else {
            eventoSelector.style.display = 'none';
            idEventoSelect.removeAttribute('required');
            idEventoSelect.value = '';
        }
    }

    radios.forEach(radio => {
        radio.addEventListener('change', checkEventoRequired);
    });
    
    // Initial check on load
    checkEventoRequired();
});
</script>
@endsection
