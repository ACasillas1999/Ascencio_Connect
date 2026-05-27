@extends('layouts.app')

@section('title', 'Apariencia CSS')
@section('page-title', 'Apariencia CSS y Personalización')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title"><i class="bi bi-palette"></i> Personalizar Tema Global</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('apariencia.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row" style="display: flex; gap: 40px; flex-wrap: wrap;">
                
                <!-- SECCIÓN: TEMA GLOBAL -->
                <div style="flex: 1; min-width: 300px;">
                    <h6 style="color: var(--accent-gold); margin-bottom: 15px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Colores del Sistema (Dashboard)</h6>
                    
                    <div class="form-group">
                        <label class="form-label">Color Acento Primario (Gold)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" name="tema_gold" class="form-control" style="width: 60px; height: 40px; padding: 2px;" value="{{ old('tema_gold', $config['tema_gold']) }}">
                            <span style="font-size: 13px; color: var(--text-muted);">Color principal para botones, bordes e íconos.</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Color Acento Secundario (Blue)</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="color" name="tema_blue" class="form-control" style="width: 60px; height: 40px; padding: 2px;" value="{{ old('tema_blue', $config['tema_blue']) }}">
                            <span style="font-size: 13px; color: var(--text-muted);">Color de soporte para degradados y etiquetas.</span>
                        </div>
                    </div>

                    <h6 style="color: var(--accent-gold); margin-top: 25px; margin-bottom: 15px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Fondos del Sistema</h6>

                    <div class="form-group" style="display: flex; gap: 15px;">
                        <div style="flex:1">
                            <label class="form-label">Fondo Principal</label>
                            <input type="color" name="bg_primary" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('bg_primary', $config['bg_primary']) }}">
                        </div>
                        <div style="flex:1">
                            <label class="form-label">Fondo Secundario (Topbar)</label>
                            <input type="color" name="bg_secondary" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('bg_secondary', $config['bg_secondary']) }}">
                        </div>
                    </div>

                    <div class="form-group" style="display: flex; gap: 15px;">
                        <div style="flex:1">
                            <label class="form-label">Fondo Barra Lateral</label>
                            <input type="color" name="bg_sidebar" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('bg_sidebar', $config['bg_sidebar']) }}">
                        </div>
                        <div style="flex:1">
                            <label class="form-label">Color de Texto Principal</label>
                            <input type="color" name="text_primary" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('text_primary', $config['text_primary']) }}">
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN: LOGIN -->
                <div style="flex: 1; min-width: 300px;">
                    <h6 style="color: var(--accent-gold); margin-bottom: 15px; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Fondo del Inicio de Sesión</h6>
                    
                    <div class="form-group">
                        <label class="form-label">Logo Frontal</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div style="margin-top: 10px;">
                            <img src="{{ asset($config['logo_path']) }}" alt="Logo actual" style="max-height: 60px; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.5));">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tipo de Fondo</label>
                        <select name="fondo_login" class="form-control">
                            <option value="arbol" {{ $config['fondo_login'] === 'arbol' ? 'selected' : '' }}>Árbol de la Energía Animado</option>
                            <option value="solo_logo" {{ $config['fondo_login'] === 'solo_logo' ? 'selected' : '' }}>Solo el Logo (Limpio)</option>
                            <option value="particulas" {{ $config['fondo_login'] === 'particulas' ? 'selected' : '' }}>Partículas Flotantes Simples</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; gap: 15px;">
                        <div style="flex:1">
                            <label class="form-label">Gradient Izquierdo</label>
                            <input type="text" name="fade_gradient_start" class="form-control" value="{{ old('fade_gradient_start', $config['fade_gradient_start']) }}" placeholder="ej. rgba(234, 90, 12, 0.63)">
                        </div>
                        <div style="flex:1">
                            <label class="form-label">Gradient Derecho</label>
                            <input type="text" name="fade_gradient_end" class="form-control" value="{{ old('fade_gradient_end', $config['fade_gradient_end']) }}" placeholder="ej. rgba(2, 6, 23, 1)">
                        </div>
                    </div>

                    <div class="form-group" style="display: flex; gap: 15px;">
                        <div style="flex:1">
                            <label class="form-label">Color Árbol 1</label>
                            <input type="color" name="color_primario" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('color_primario', $config['color_primario']) }}">
                        </div>
                        <div style="flex:1">
                            <label class="form-label">Color Árbol 2</label>
                            <input type="color" name="color_secundario" class="form-control" style="width: 100%; height: 40px; padding: 2px;" value="{{ old('color_secundario', $config['color_secundario']) }}">
                        </div>
                    </div>
                </div>

            </div>

            <div style="margin-top: 30px; text-align: right; border-top: 1px solid var(--border-subtle); padding-top: 20px;">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Configuración</button>
            </div>
        </form>
    </div>
</div>
@endsection
