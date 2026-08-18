@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Usuarios')

@section('topbar-actions')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary" title="Crear Usuario">
        <i class="bi bi-plus-lg"></i> <span style="display:inline-block;">Nuevo</span>
    </a>
@endsection


@push('styles')
<style>
    /* ========================================================= */
    /* TARJETAS MÓVILES PREMIUM DE USUARIOS                      */
    /* ========================================================= */
    .mobile-usuarios-list {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
        box-sizing: border-box;
        width: 100%;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            display: none !important;
        }
        .mobile-usuarios-list {
            display: flex !important;
        }
    }

    .mobile-usr-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.7) 0%, rgba(10, 22, 50, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    [data-theme="light"] .mobile-usr-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .musr-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .musr-user-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .musr-avatar {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--accent-gold), var(--accent-blue));
        color: #000;
        font-weight: 800; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .musr-username {
        font-size: 15.5px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
    }

    [data-theme="light"] .musr-username {
        color: #0f172a !important;
    }

    .musr-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .musr-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding-top: 10px;
    }

    [data-theme="light"] .musr-actions {
        border-top: 1px solid #e2e8f0;
    }

    .musr-btn-edit {
        flex: 1;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        padding: 7px 12px;
    }
</style>
@endpush

@section('content')

@if(session('success'))
    <div class="alert alert-success" style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Directorio de Usuarios</h2>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Rol</th><th>Estatus</th>
                        <th style="text-align:right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->ID }}</td>
                        <td><strong>{{ $usuario->username }}</strong></td>
                        <td>
                            @php
                                $rolNorm = \App\Helpers\Permisos::normalizar($usuario->Rol);
                                $badgeColor = match($rolNorm) {
                                    'Admin' => 'background: rgba(212,175,55,0.2); color: #d4af37; border: 1px solid rgba(212,175,55,0.3);',
                                    'Gerente' => 'background: rgba(59,130,246,0.2); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3);',
                                    'Vendedor' => 'background: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.3);',
                                    'Proveedor' => 'background: rgba(168,85,247,0.2); color: #a855f7; border: 1px solid rgba(168,85,247,0.3);',
                                    'Evento' => 'background: rgba(249,115,22,0.2); color: #f97316; border: 1px solid rgba(249,115,22,0.3);',
                                    default => 'background: rgba(100,116,139,0.2); color: #94a3b8; border: 1px solid rgba(100,116,139,0.3);',
                                };
                            @endphp
                            <span style="display:inline-block; padding: 3px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; {{ $badgeColor }}">
                                {{ $usuario->Rol }}
                            </span>
                        </td>
                        <td>
                            @if($usuario->Activo ?? 1)
                                <span class="badge" style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-weight:800; font-size:11px; padding:4px 9px; border-radius:6px;">
                                    🟢 Activo
                                </span>
                            @else
                                <span class="badge" style="background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#f87171; font-weight:800; font-size:11px; padding:4px 9px; border-radius:6px;">
                                    🔴 Inactivo
                                </span>
                            @endif
                        </td>
                        <td style="text-align:right; white-space:nowrap;">
                            @if($usuario->username !== 'Admin')
                                <form action="{{ route('usuarios.toggle-activo', $usuario->ID) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ ($usuario->Activo ?? 1) ? 'btn-warning' : 'btn-success' }}" style="font-weight:700; font-size:11.5px;" title="{{ ($usuario->Activo ?? 1) ? 'Desactivar cuenta' : 'Activar cuenta' }}">
                                        <i class="bi {{ ($usuario->Activo ?? 1) ? 'bi-slash-circle' : 'bi-check-circle' }}"></i> {{ ($usuario->Activo ?? 1) ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('usuarios.edit', $usuario->ID) }}" class="btn btn-sm btn-secondary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($usuario->username !== 'Admin')
                                <form action="{{ route('usuarios.destroy', $usuario->ID) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    <!-- VISTA MÓVIL DE TARJETAS DE USUARIOS -->
    <div class="mobile-usuarios-list">
        @foreach($usuarios as $usuario)
        <div class="mobile-usr-card">
            <div class="musr-header">
                <div class="musr-user-wrap">
                    <div class="musr-avatar">{{ strtoupper(substr($usuario->username, 0, 1)) }}</div>
                    <div>
                        <div class="musr-username">{{ $usuario->username }}</div>
                        <div class="musr-sub">ID: #{{ $usuario->ID }}</div>
                    </div>
                </div>
                @php
                    $rolNorm = \App\Helpers\Permisos::normalizar($usuario->Rol);
                    $badgeColor = match($rolNorm) {
                        'Admin' => 'background: rgba(212,175,55,0.2); color: #d4af37; border: 1px solid rgba(212,175,55,0.3);',
                        'Gerente' => 'background: rgba(59,130,246,0.2); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3);',
                        'Vendedor' => 'background: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.3);',
                        'Proveedor' => 'background: rgba(168,85,247,0.2); color: #a855f7; border: 1px solid rgba(168,85,247,0.3);',
                        'Evento' => 'background: rgba(249,115,22,0.2); color: #f97316; border: 1px solid rgba(249,115,22,0.3);',
                        default => 'background: rgba(100,116,139,0.2); color: #94a3b8; border: 1px solid rgba(100,116,139,0.3);',
                    };
                @endphp
                <span style="display:inline-block; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; {{ $badgeColor }}">
                    {{ $usuario->Rol }}
                </span>
            </div>

            <div class="musr-actions">
                @if($usuario->username !== 'Admin')
                    <form action="{{ route('usuarios.toggle-activo', $usuario->ID) }}" method="POST" style="flex:1;">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ ($usuario->Activo ?? 1) ? 'btn-warning' : 'btn-success' }}" style="width:100%; font-weight:700; font-size:12px; padding:7px;">
                            <i class="bi {{ ($usuario->Activo ?? 1) ? 'bi-slash-circle' : 'bi-check-circle' }}"></i> {{ ($usuario->Activo ?? 1) ? 'Desactivar' : 'Activar' }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('usuarios.edit', $usuario->ID) }}" class="btn btn-sm btn-secondary musr-btn-edit">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                @if($usuario->username !== 'Admin')
                    <form action="{{ route('usuarios.destroy', $usuario->ID) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar a este usuario?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    </div>
</div>
@endsection
