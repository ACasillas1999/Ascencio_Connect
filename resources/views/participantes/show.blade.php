@extends('layouts.app')

@section('title', 'Perfil - ' . $participante->Nombre)
@section('page-title', 'Perfil')

@section('topbar-actions')
    <div style="display:flex; align-items:center; gap:4px; flex-shrink:0;">
        @if($participante->Telefono)
        <a href="{{ route('clientes.perfil', $participante->Telefono) }}" class="btn btn-primary btn-sm" title="Ver Perfil Global" style="padding:4px 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
            <i class="bi bi-person-badge"></i> <span class="d-none d-md-inline">Perfil Global</span><span class="d-md-none">Global</span>
        </a>
        @endif
        <a href="{{ route('participantes.edit', $participante) }}" class="btn btn-secondary btn-sm" title="Editar" style="padding:4px 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
            <i class="bi bi-pencil"></i> <span class="d-none d-sm-inline">Editar</span>
        </a>
        <a href="{{ route('participantes.index') }}" class="btn btn-secondary btn-sm" title="Volver" style="padding:4px 8px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:4px; border-radius:6px;">
            <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Volver</span>
        </a>
    </div>
@endsection
@push('styles')
<style>
    /* ========================================================= */
    /* DISEÑO ULTRA-MODERNO DE PERFIL DE PARTICIPANTE           */
    /* ========================================================= */
    .profile-hero-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.85) 0%, rgba(10, 22, 50, 0.95) 100%);
        border: 1px solid rgba(201, 162, 39, 0.25);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        padding: 24px 20px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .profile-hero-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .profile-avatar-circle {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-gold), #f59e0b);
        color: #0f172a;
        font-size: 34px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 14px;
        box-shadow: 0 0 20px rgba(201, 162, 39, 0.35);
        border: 3px solid rgba(255, 255, 255, 0.2);
    }

    .profile-datos-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .profile-dato-tile {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    [data-theme="light"] .profile-dato-tile {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    .profile-dato-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: rgba(201, 162, 39, 0.12);
        color: var(--accent-gold);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        flex-shrink: 0;
    }

    .profile-dato-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
    }

    .profile-dato-val {
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    [data-theme="light"] .profile-dato-val {
        color: #0f172a !important;
    }

    @media (max-width: 768px) {
        .profile-main-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
        .profile-datos-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .card-body {
            padding: 14px !important;
        }
    }

    @media (max-width: 480px) {
        .profile-datos-grid {
            grid-template-columns: 1fr !important;
        }
    }

    /* ========================================================= */
    /* TARJETAS MÓVILES PERFECTAS PARA HISTORIAL (CANJES/PUNTOS) */
    /* ========================================================= */
    .mobile-history-list {
        display: none;
        flex-direction: column;
        gap: 10px;
        padding: 12px;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            display: none !important;
        }
        .mobile-history-list {
            display: flex !important;
        }
    }

    .mhist-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.7) 0%, rgba(10, 22, 50, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
    }

    [data-theme="light"] .mhist-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05) !important;
    }

    .mhist-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        flex: 1;
    }

    .mhist-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .mhist-title {
        font-size: 13.5px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    [data-theme="light"] .mhist-title {
        color: #0f172a !important;
    }

    .mhist-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

</style>
@endpush


@section('content')
<div class="profile-main-grid" style="display:grid; grid-template-columns:320px 1fr; gap:20px;">

    <!-- PERFIL -->
    <div>
        <div class="profile-hero-card" style="margin-bottom:16px;">
            <div class="profile-avatar-circle">
                {{ strtoupper(substr($participante->Nombre, 0, 1)) }}
            </div>
            <div style="font-size:18px; font-weight:800; color:var(--accent-gold); margin-bottom:4px; line-height:1.2;">
                {{ $participante->Nombre }}
            </div>
            @if($participante->Puesto)
                <div style="font-size:12.5px; color:var(--text-muted); font-weight:600;">{{ $participante->Puesto }}</div>
            @endif
            <div style="margin-top:14px;">
                <span class="badge badge-gold" style="font-size:14px; padding:6px 16px; border-radius:20px; font-weight:800; box-shadow:0 4px 12px rgba(201,162,39,0.25);">
                    <i class="bi bi-star-fill" style="margin-right:4px;"></i>{{ number_format($participante->Puntos) }} pts
                </span>
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

            <!-- VISTA MÓVIL DE PUNTOS POR PROVEEDOR -->
            <div class="mobile-history-list">
                @forelse($participante->puntosProveedor->take(10) as $pp)
                <div class="mhist-card">
                    <div class="mhist-left">
                        <div class="mhist-icon" style="background:rgba(201,162,39,0.15); color:var(--accent-gold);">
                            <i class="bi bi-building"></i>
                        </div>
                        <div style="min-width:0; flex:1;">
                            <div class="mhist-title">{{ $pp->usuario }}</div>
                            <div class="mhist-sub">
                                <i class="bi bi-clock"></i> {{ $pp->fecha?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-gold" style="font-size:12px; padding:5px 12px; font-weight:800;">+{{ $pp->puntos }} pts</span>
                    </div>
                </div>
                @empty
                <div style="text-align:center; color:var(--text-muted); padding:20px; font-size:13px;">Sin puntos</div>
                @endforelse
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

            <!-- VISTA MÓVIL DE CANJES DE PREMIOS -->
            <div class="mobile-history-list">
                @forelse($participante->canjes as $canje)
                <div class="mhist-card">
                    <div class="mhist-left">
                        <div class="mhist-icon" style="background:rgba(168,85,247,0.15); color:#a855f7;">
                            <i class="bi bi-gift-fill"></i>
                        </div>
                        <div style="min-width:0; flex:1;">
                            <div class="mhist-title">{{ $canje->premio?->NombrePremio ?: 'Premio' }}</div>
                            <div class="mhist-sub">
                                <i class="bi bi-clock"></i> {{ $canje->Fecha?->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-primary" style="font-size:12px; padding:5px 12px; font-weight:800; background:rgba(59,130,246,0.15); color:#3b82f6; border:1px solid rgba(59,130,246,0.3);">x{{ $canje->Cantidad }}</span>
                    </div>
                </div>
                @empty
                <div style="text-align:center; color:var(--text-muted); padding:20px; font-size:13px;">Sin canjes</div>
                @endforelse
            </div>
        </div>

    </div>
</div>

<!-- Modal de Previsualización -->
<div id="previewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="background:var(--bg-secondary); border-radius:16px; width:100%; max-width:720px; max-height:85vh; display:flex; flex-direction:column; overflow:hidden; border:1px solid rgba(255,255,255,0.12); box-shadow:0 20px 50px rgba(0,0,0,0.6); position:relative;">
        <div style="padding:14px 20px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; background:rgba(10,15,30,0.5);">
            <h3 id="modalTitle" style="margin:0; font-size:15px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-file-earmark-image"></i> Previsualización
            </h3>
            <button onclick="closePreview()" style="background:none; border:none; color:var(--text-secondary); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        <div style="padding:20px; overflow-y:auto; flex:1; display:flex; justify-content:center; align-items:center; background:rgba(5,8,18,0.7); min-height:300px;">
            <img id="modalImage" src="" style="max-width:100%; max-height:65vh; width:auto; height:auto; object-fit:contain; border-radius:8px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
        </div>
        <div style="padding:12px 20px; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; background:rgba(10,15,30,0.5);">
            <div style="display:flex; gap:8px;">
                <a id="modalDownloadBtn" href="" download class="btn btn-sm btn-primary" style="font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" onclick="printModalImage()" class="btn btn-sm btn-secondary" style="font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; background:rgba(212,175,55,0.12); border:1px solid var(--accent-gold); color:var(--accent-gold);">
                    <i class="bi bi-printer-fill"></i> Imprimir Imagen
                </button>
            </div>
            <button onclick="closePreview()" class="btn btn-sm btn-secondary" style="font-size:12px;">Cerrar</button>
        </div>
    </div>
</div>

<script>
    function openPreview(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-file-earmark-image"></i> ' + title;
        document.getElementById('modalDownloadBtn').href = src;
        document.getElementById('previewModal').style.display = 'flex';
    }
    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('modalImage').src = '';
    }
    function printModalImage() {
        const imgSrc = document.getElementById('modalImage').src;
        if (!imgSrc) return;
        const printWin = window.open('', '_blank');
        printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Imprimir Documento</title>
                <style>
                    @page { margin: 0; size: auto; }
                    body { margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #fff; }
                    img { max-width: 100%; max-height: 100vh; object-fit: contain; }
                </style>
            </head>
            <body>
                <img src="${imgSrc}" onload="window.print(); setTimeout(() => window.close(), 500);" />
            </body>
            </html>
        `);
        printWin.document.close();
    }
    window.onclick = function(event) {
        let modal = document.getElementById('previewModal');
        if (event.target == modal) {
            closePreview();
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePreview();
    });
</script>
@endsection
