@extends('layouts.app')

@section('title', $participante->Nombre)
@section('page-title', 'Perfil de Participante')

@section('topbar-actions')
    @if($participante->Telefono)
    <a href="{{ route('clientes.perfil', $participante->Telefono) }}" class="btn btn-primary" style="margin-right:8px;">
        <i class="bi bi-person-badge"></i> Ver Perfil Global
    </a>
    @endif
    <a href="{{ route('participantes.edit', $participante) }}" class="btn btn-secondary">
        <i class="bi bi-pencil"></i> Editar
    </a>
    <a href="{{ route('participantes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div style="display:grid;grid-template-columns:300px 1fr;gap:20px">

    <!-- PERFIL -->
    <div>
        <div class="card" style="margin-bottom:16px">
            <div class="card-body" style="text-align:center;padding:32px 24px">
                <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--accent-gold),var(--accent-blue));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:30px;font-weight:800;color:#000;margin:0 auto 16px">
                    {{ strtoupper(substr($participante->Nombre, 0, 1)) }}
                </div>
                <div style="font-size:17px;font-weight:700;margin-bottom:4px">{{ $participante->Nombre }}</div>
                @if($participante->Puesto)
                    <div style="font-size:12px;color:var(--text-muted)">{{ $participante->Puesto }}</div>
                @endif
                <div style="margin-top:16px">
                    <span class="badge badge-gold" style="font-size:15px;padding:6px 18px">
                        <i class="bi bi-star-fill" style="margin-right:4px"></i>{{ number_format($participante->Puntos) }} pts
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Datos</span></div>
            <div class="card-body">
                @php
                    $datos = [
                        ['RFC',       $participante->RFC,      'card-text'],
                        ['Teléfono',  $participante->Telefono, 'phone'],
                        ['Sucursal',  $participante->Sucursal, 'building'],
                        ['Vendedor',  $participante->Vendedor, 'person'],
                        ['Proveedor', $participante->Proveedor,'truck'],
                        ['Evento',    $participante->evento?->name_evento, 'calendar-event'],
                    ];
                @endphp
                @foreach($datos as [$label,$val,$icon])
                @if($val)
                <div style="display:flex;gap:10px;padding:8px 0;border-bottom:1px solid var(--border-subtle);font-size:13px">
                    <i class="bi bi-{{ $icon }}" style="color:var(--accent-gold);min-width:16px;margin-top:2px"></i>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:700">{{ $label }}</div>
                        <div style="margin-top:1px">{{ $val }}</div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <!-- DOCUMENTOS -->
        <div class="card" style="margin-top:16px">
            <div class="card-header"><span class="card-title">Documentos</span></div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:16px; align-items:center;">
                @if($participante->Ruta_Gafete)
                    <div style="width:100%;">
                        <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:700; margin-bottom:6px; text-align:center;">Gafete</div>
                        <div style="text-align:center;">
                            <img src="{{ asset('storage/' . $participante->Ruta_Gafete) }}" style="width:100%; max-width:180px; border-radius:8px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.3);" onclick="openPreview('{{ asset('storage/' . $participante->Ruta_Gafete) }}', 'Gafete de {{ $participante->Nombre }}')">
                        </div>
                    </div>
                @endif
                
                @if($participante->Ruta_Horario)
                    <div style="width:100%;">
                        <div style="font-size:10px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); font-weight:700; margin-bottom:6px; text-align:center;">Horario</div>
                        <div style="text-align:center;">
                            <img src="{{ asset('storage/' . $participante->Ruta_Horario) }}" style="width:100%; max-width:180px; border-radius:8px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.3);" onclick="openPreview('{{ asset('storage/' . $participante->Ruta_Horario) }}', 'Horario de {{ $participante->Nombre }}')">
                        </div>
                    </div>
                @endif
                
                @if(!$participante->Ruta_Gafete && !$participante->Ruta_Horario)
                    <div style="color:var(--text-muted); font-size:13px; text-align:center; padding:10px;">Sin documentos generados.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- HISTORIAL -->
    <div style="display:flex;flex-direction:column;gap:16px">

        <!-- Asistencia -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-check2-circle" style="color:var(--accent-green);margin-right:8px"></i>Asistencia a Clases</span>
                <span class="badge badge-primary">{{ $participante->clases->count() }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Actividad</th><th>Salon</th><th>Fecha</th><th>Asistió</th></tr></thead>
                    <tbody>
                        @forelse($participante->clases->take(10) as $clase)
                        <tr>
                            <td style="font-size:12.5px">{{ $clase->agenda?->Actividad }}</td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $clase->agenda?->Salon }}</td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $clase->agenda?->Fecha?->format('d/m/Y') }}</td>
                            <td>
                                @if($clase->Asistio)
                                    <span class="badge badge-success"><i class="bi bi-check-lg"></i> Sí</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:20px">Sin registros</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Puntos -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-star" style="color:var(--accent-gold);margin-right:8px"></i>Puntos por Proveedor</span>
                <span class="badge badge-gold">Total: {{ number_format($participante->puntosProveedor->sum('puntos')) }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Proveedor</th><th>Puntos</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @forelse($participante->puntosProveedor->take(10) as $pp)
                        <tr>
                            <td style="font-size:12.5px;font-weight:500">{{ $pp->usuario }}</td>
                            <td><span class="badge badge-gold">+{{ $pp->puntos }}</span></td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $pp->fecha?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Sin puntos</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Canjes -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-gift" style="color:#a855f7;margin-right:8px"></i>Canjes de Premios</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Premio</th><th>Cantidad</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @forelse($participante->canjes as $canje)
                        <tr>
                            <td style="font-size:12.5px">{{ $canje->premio?->NombrePremio }}</td>
                            <td><span class="badge badge-primary">x{{ $canje->Cantidad }}</span></td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $canje->Fecha?->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:20px">Sin canjes</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal de Previsualización -->
<div id="previewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; justify-content:center; align-items:center;">
    <div style="background:var(--bg-secondary); padding:20px; border-radius:12px; max-width:90%; max-height:90%; overflow:auto; position:relative; border:1px solid var(--border);">
        <button onclick="closePreview()" style="position:absolute; top:10px; right:15px; background:none; border:none; color:var(--text-primary); font-size:28px; cursor:pointer;">&times;</button>
        <h3 id="modalTitle" style="margin-bottom:15px; font-size:16px; color:var(--accent-gold);">Previsualización</h3>
        <div style="text-align:center;">
            <img id="modalImage" src="" style="max-width:100%; height:auto; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.5);">
        </div>
    </div>
</div>

<script>
    function openPreview(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('previewModal').style.display = 'flex';
    }
    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('modalImage').src = '';
    }
    // Cerrar al hacer clic fuera
    window.onclick = function(event) {
        let modal = document.getElementById('previewModal');
        if (event.target == modal) {
            closePreview();
        }
    }
</script>
@endsection
