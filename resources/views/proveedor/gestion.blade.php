@extends('layouts.app')

@section('title', 'Gestión de Proveedores')
@section('page-title', 'Proveedores')

@section('content')
<div style="display:grid; grid-template-columns:1fr 350px; gap:24px;">

    <!-- LISTA DE PROVEEDORES -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-building" style="color:var(--accent-gold);margin-right:8px"></i>Cuentas de Proveedores</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Usuario (Nombre del Proveedor)</th>
                        <th>Contraseña</th>
                        <th>Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $prov)
                    <tr>
                        <td style="font-weight:600; font-size:14px;">{{ $prov->username }}</td>
                        <td style="font-family:monospace; color:var(--accent-gold);">{{ $prov->password_visible ?: '********' }}</td>
                        <td><span class="badge badge-primary">{{ $prov->Rol }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="text-align:center; padding:32px; color:var(--text-muted);">
                            <i class="bi bi-building" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            No hay proveedores registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORMULARIO (NUEVO) -->
    <div class="card" style="align-self:start;">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nueva Cuenta</span>
        </div>
        <div class="card-body">
            <form action="{{ route('proveedores.storeUsuario') }}" method="POST">
                @csrf
                
                <div class="form-group" style="margin-bottom:16px;">
                    <label for="username" style="display:block; margin-bottom:6px; font-size:12px;">Nombre de Usuario</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Ej. CocaCola" required>
                    <small style="color:var(--text-muted); font-size:11px;">Este nombre lo usarás para asignarlo a los eventos.</small>
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label for="password" style="display:block; margin-bottom:6px; font-size:12px;">Contraseña</label>
                    <input type="text" id="password" name="password" class="form-control" placeholder="Ej. 123456" required>
                    <small style="color:var(--text-muted); font-size:11px;">Se guardará visible para ti en esta lista.</small>
                </div>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Crear Cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
