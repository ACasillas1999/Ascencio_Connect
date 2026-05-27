@extends('layouts.app')

@section('title', 'Crear Rol')
@section('page-title', 'Crear Rol')

@section('topbar-actions')
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Nuevo Rol</h2>
    </div>
    <div class="card-body">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="name">Nombre del Rol</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Ej. Moderador" value="{{ old('name') }}" required>
                @error('name') <small class="text-danger" style="color:var(--accent-red)">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Permisos</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 10px;">
                    @foreach($permissions as $permission)
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" style="width: 16px; height: 16px; accent-color: var(--accent-gold);">
                            {{ ucfirst($permission->name) }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div style="text-align: right; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Rol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
