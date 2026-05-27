@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Usuarios')

@section('topbar-actions')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Crear Usuario
    </a>
@endsection

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
                        <th>Rol</th>
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
                        <td style="text-align:right">
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
    </div>
</div>
@endsection
