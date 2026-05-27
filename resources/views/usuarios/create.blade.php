@extends('layouts.app')

@section('title', 'Crear Usuario')
@section('page-title', 'Crear Usuario')

@section('topbar-actions')
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

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

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
