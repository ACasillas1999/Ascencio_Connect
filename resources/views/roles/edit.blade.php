@extends('layouts.app')

@section('title', 'Configurar Rol: ' . $roleName)
@section('page-title', 'Configurar Rol')

@section('topbar-actions')
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

@php
    $roleColor = match($roleName) {
        'Admin' => '#d4af37', 'Gerente' => '#3b82f6', 'Vendedor' => '#10b981',
        'Proveedor' => '#a855f7', 'Evento' => '#f97316', default => '#94a3b8',
    };
@endphp

{{-- Permisos --}}
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">
            <span style="color: {{ $roleColor }};">●</span>
            Permisos del rol: {{ $roleName }}
        </h2>
    </div>
    <div class="card-body">
        @if($roleName === 'Admin')
            <div style="text-align: center; padding: 30px; color: var(--text-secondary, #94a3b8);">
                <i class="bi bi-shield-fill-check" style="font-size: 40px; color: #d4af37; display: block; margin-bottom: 10px;"></i>
                <p style="font-size: 14px;">El rol <strong>Admin</strong> tiene acceso total a todos los módulos del sistema.</p>
                <p style="font-size: 12px; opacity: 0.7;">Esto no puede ser modificado por seguridad.</p>
            </div>
        @else
            <form action="{{ route('roles.update', $roleName) }}" method="POST">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 1fr; gap: 0;">
                    @foreach($modulos as $key => $modulo)
                        @php
                            $activo = isset($permisos[$key]) ? $permisos[$key] : 0;
                        @endphp
                        <label style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--border, rgba(30,41,59,0.5)); cursor: pointer; transition: background 0.15s;"
                               onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="bi {{ $modulo['icon'] }}" style="font-size: 18px; color: var(--text-secondary, #94a3b8); width: 24px; text-align: center;"></i>
                                <div>
                                    <div style="font-weight: 600; font-size: 13.5px; color: var(--text-primary, #e2e8f0);">{{ $modulo['label'] }}</div>
                                    <div style="font-size: 11.5px; color: var(--text-secondary, #64748b);">{{ $modulo['desc'] }}</div>
                                </div>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" name="modulos[]" value="{{ $key }}" id="mod_{{ $key }}" {{ $activo ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </div>
                        </label>
                    @endforeach
                </div>

                <div style="text-align: right; padding: 20px 16px 4px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Permisos
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- Usuarios con este rol --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Usuarios con rol "{{ $roleName }}" ({{ $usuarios->count() }})</h2>
    </div>
    <div class="card-body">
        @if($usuarios->isEmpty())
            <p style="text-align:center; color: var(--text-secondary, #94a3b8); padding: 30px 0;">
                <i class="bi bi-person-x" style="font-size: 28px; display:block; margin-bottom:6px; opacity:0.5;"></i>
                No hay usuarios con este rol.
            </p>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th style="text-align:right">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->ID }}</td>
                            <td><strong>{{ $usuario->username }}</strong></td>
                            <td style="text-align:right">
                                <a href="{{ route('usuarios.edit', $usuario->ID) }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(100,116,139,0.3);
        border-radius: 24px;
        transition: 0.3s;
    }
    .toggle-slider::before {
        content: '';
        position: absolute;
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #94a3b8;
        border-radius: 50%;
        transition: 0.3s;
    }
    .toggle-switch input:checked + .toggle-slider {
        background: rgba(16,185,129,0.4);
    }
    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(20px);
        background: #10b981;
    }
</style>
@endpush
