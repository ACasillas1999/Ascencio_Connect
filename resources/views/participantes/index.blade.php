@extends('layouts.app')

@section('title', 'Participantes')
@section('page-title', 'Participantes')

@section('topbar-actions')
    <a href="{{ route('participantes.create') }}" class="btn btn-primary" title="Nuevo Participante">
        <i class="bi bi-plus-lg"></i> <span style="display:inline-block;">Nuevo</span>
    </a>
@endsection


@push('styles')
<style>
    /* ========================================================= */
    /* TARJETAS MÓVILES PREMIUM Y FILTROS EN PARTICIPANTES       */
    /* ========================================================= */
    .mobile-participantes-list {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            display: none !important;
        }
        .mobile-participantes-list {
            display: flex !important;
        }
        /* Filtros apilados en 1 columna en móvil */
        .card-body form {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .card-body form > div {
            width: 100% !important;
            min-width: 100% !important;
            flex: none !important;
        }
    }

    .mobile-part-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.7) 0%, rgba(10, 22, 50, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    [data-theme="light"] .mobile-part-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .mpc-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding-bottom: 10px;
    }

    [data-theme="light"] .mpc-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .mpc-user-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mpc-avatar {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--accent-gold), var(--accent-blue));
        color: #000;
        font-weight: 800; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .mpc-name {
        font-size: 15px;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1.2;
    }

    [data-theme="light"] .mpc-name {
        color: #0f172a !important;
    }

    .mpc-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .mpc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .mpc-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .mpc-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .mpc-val {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-secondary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mpc-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding-top: 10px;
    }

    [data-theme="light"] .mpc-actions {
        border-top: 1px solid #e2e8f0;
    }

    .mpc-btn-primary {
        flex: 1;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        padding: 7px 12px;
    }
</style>
@endpush

@section('content')

<!-- FILTROS PLEGABLES ULTRA-COMPACTOS -->
@php
    $hasActiveFilters = request('search') || request('evento') || request('sucursal');
@endphp
<div class="card" style="margin-bottom:20px; overflow:hidden;">
    <!-- HEADER BAR : CLIC PARA PLEGAR / DESPLEGAR -->
    <div onclick="toggleFilterCollapse()" style="padding:12px 18px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; background:rgba(255,255,255,0.02); user-select:none;">
        <span style="font-size:13.5px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
            <i class="bi bi-funnel-fill"></i> Filtros de Búsqueda
            @if($hasActiveFilters)
                <span class="badge badge-gold" style="font-size:10px; padding:2px 8px;">Activos</span>
            @endif
        </span>
        <div style="display:flex; align-items:center; gap:8px; color:var(--text-muted); font-size:12px; font-weight:600;">
            <span>{{ $hasActiveFilters ? 'Ocultar' : 'Mostrar / Plegar' }}</span>
            <i id="filter-chevron" class="bi bi-chevron-down" style="transition:transform 0.3s ease; transform: {{ $hasActiveFilters ? 'rotate(180deg)' : 'rotate(0deg)' }};"></i>
        </div>
    </div>
    
    <!-- BODY PLEGABLE -->
    <div id="filter-collapse-body" style="display: {{ $hasActiveFilters ? 'block' : 'none' }}; border-top:1px solid var(--border-subtle); padding:16px 20px; transition:all 0.3s ease;">
        <form method="GET" action="{{ route('participantes.index') }}" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap">
            <div style="flex:2;min-width:200px">
                <label class="form-label" for="search">Buscar</label>
                <div style="position:relative">
                    <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted)"></i>
                    <input id="search" name="search" type="text" class="form-control" style="padding-left:36px"
                           placeholder="Nombre, RFC, teléfono..." value="{{ request('search') }}">
                </div>
            </div>
            <div style="flex:1;min-width:160px">
                <label class="form-label" for="evento">Evento</label>
                <select id="evento" name="evento" class="form-control">
                    <option value="">Todos los eventos</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->ID }}" {{ request('evento') == $ev->ID ? 'selected' : '' }}>
                            {{ Str::limit($ev->name_evento, 40) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:150px">
                <label class="form-label" for="sucursal">Sucursal</label>
                <select id="sucursal" name="sucursal" class="form-control">
                    <option value="">Todas las sucursales</option>
                    @foreach(['DIMEGSA','DEASA','AIESA','SEGSA','FESA','TAPATIA','GABSA','ILUMINACION','VALLARTA','QUERETARO','CODI'] as $s)
                        <option value="{{ $s }}" {{ request('sucursal') == $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('participantes.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleFilterCollapse() {
        const body = document.getElementById('filter-collapse-body');
        const chevron = document.getElementById('filter-chevron');
        if (!body || !chevron) return;
        
        if (body.style.display === 'none') {
            body.style.display = 'block';
            chevron.style.transform = 'rotate(180deg)';
        } else {
            body.style.display = 'none';
            chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>

<!-- TABLA -->
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-people" style="color:var(--accent-gold);margin-right:8px"></i>
            Participantes
            <span style="font-size:12px;color:var(--text-muted);font-weight:400;margin-left:8px">({{ $participantes->total() }})</span>
        </span>
    </div>
    @php
    $currentSort = strtolower(request('sort', 'id'));
    $currentDir = strtolower(request('direction', 'desc'));

    $renderSortHeader = function($key, $label) use ($currentSort, $currentDir) {
        $isActive = ($currentSort === $key);
        $nextDir = ($isActive && $currentDir === 'asc') ? 'desc' : 'asc';
        
        $query = array_merge(request()->query(), [
            'sort' => $key,
            'direction' => $nextDir
        ]);
        
        $url = route('participantes.index', $query);

        $iconClass = 'bi bi-arrow-down-up';
        if ($isActive) {
            $iconClass = ($currentDir === 'asc') ? 'bi bi-sort-up-alt' : 'bi bi-sort-down';
        }

        $activeStyle = $isActive 
            ? 'color: var(--accent-gold); font-weight: 800;' 
            : 'color: var(--text-primary); opacity: 0.85;';

        return '<a href="' . e($url) . '" class="sort-header-link" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px; ' . $activeStyle . '" title="Ordenar por ' . e($label) . '">'
            . e($label)
            . ' <i class="' . $iconClass . '" style="font-size:12px; opacity:' . ($isActive ? '1' : '0.4') . ';"></i></a>';
    };
@endphp

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>{!! $renderSortHeader('id', '#') !!}</th>
                    <th>{!! $renderSortHeader('nombre', 'Nombre') !!}</th>
                    <th>{!! $renderSortHeader('evento', 'Evento') !!}</th>
                    <th>{!! $renderSortHeader('sucursal', 'Sucursal') !!}</th>
                    <th>{!! $renderSortHeader('proveedor', 'Proveedor') !!}</th>
                    <th>{!! $renderSortHeader('telefono', 'Teléfono') !!}</th>
                    <th>{!! $renderSortHeader('puntos', 'Puntos') !!}</th>
                    <th>Documentos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($participantes as $p)
                <tr>
                    <td style="color:var(--text-muted);font-size:12px">{{ $p->ID }}</td>
                    <td>
                        <div style="font-weight:500">{{ $p->Nombre }}</div>
                        @if($p->RFC)
                            <div style="font-size:11px;color:var(--text-muted)">RFC: {{ $p->RFC }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--text-secondary)">
                        {{ $p->evento ? Str::limit($p->evento->name_evento, 30) : '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--text-secondary)">{{ $p->Sucursal ?: '—' }}</td>
                    <td style="font-size:12px;color:var(--text-secondary)">{{ Str::limit($p->Proveedor, 22) ?: '—' }}</td>
                    <td style="font-size:12px;color:var(--text-secondary)">{{ $p->Telefono ?: '—' }}</td>
                    <td><span class="badge badge-gold">{{ number_format($p->Puntos) }}</span></td>
                    <td>
                        <div style="display:flex;gap:6px">
                            @if($p->Ruta_Gafete)
                                <button class="btn btn-sm btn-secondary" onclick="openPreview('{{ asset('storage/' . $p->Ruta_Gafete) }}', 'Gafete de {{ $p->Nombre }}')" title="Ver Gafete">
                                    <i class="bi bi-person-vcard"></i>
                                </button>
                            @else
                                <span style="color:var(--text-muted);font-size:11px">Sin Gafete</span>
                            @endif
                            
                            @if($p->Ruta_Horario)
                                <button class="btn btn-sm btn-secondary" onclick="openPreview('{{ asset('storage/' . $p->Ruta_Horario) }}', 'Horario de {{ $p->Nombre }}')" title="Ver Horario">
                                    <i class="bi bi-calendar3"></i>
                                </button>
                            @else
                                <span style="color:var(--text-muted);font-size:11px">Sin Horario</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            @if($p->Telefono)
                            <a href="{{ route('clientes.perfil', $p->Telefono) }}" class="btn btn-sm btn-secondary" title="Ver Perfil Global">
                                <i class="bi bi-person-badge" style="color:var(--accent-gold);"></i>
                            </a>
                            @endif
                            <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm btn-secondary" title="Ver Detalles en Evento">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(auth()->check() && auth()->user()->esAdmin())
                            <a href="{{ route('participantes.edit', $p) }}" class="btn btn-sm btn-secondary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('participantes.destroy', $p) }}" method="POST" class="delete-form" data-message="¿Deseas eliminar al participante '{{ $p->Nombre }}'?" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-secondary" style="color:#ef4444;" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        No se encontraron participantes
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- VISTA MÓVIL DE TARJETAS DE PARTICIPANTES -->
    <div class="mobile-participantes-list">
        @forelse($participantes as $p)
        <div class="mobile-part-card">
            <div class="mpc-header">
                <div class="mpc-user-wrap">
                    <div class="mpc-avatar">{{ strtoupper(substr($p->Nombre, 0, 1)) }}</div>
                    <div>
                        <div class="mpc-name">{{ $p->Nombre }}</div>
                        @if($p->RFC)
                            <div class="mpc-sub">RFC: {{ $p->RFC }}</div>
                        @endif
                    </div>
                </div>
                <span class="badge badge-gold">{{ number_format($p->Puntos) }} pts</span>
            </div>

            <div class="mpc-grid">
                <div class="mpc-item">
                    <span class="mpc-label"><i class="bi bi-calendar-event"></i> Evento</span>
                    <span class="mpc-val" title="{{ $p->evento ? $p->evento->name_evento : '' }}">
                        {{ $p->evento ? Str::limit($p->evento->name_evento, 22) : '—' }}
                    </span>
                </div>

                <div class="mpc-item">
                    <span class="mpc-label"><i class="bi bi-geo-alt"></i> Sucursal</span>
                    <span class="mpc-val">{{ $p->Sucursal ?: '—' }}</span>
                </div>

                <div class="mpc-item">
                    <span class="mpc-label"><i class="bi bi-building"></i> Proveedor</span>
                    <span class="mpc-val" title="{{ $p->Proveedor }}">{{ Str::limit($p->Proveedor, 20) ?: '—' }}</span>
                </div>

                <div class="mpc-item">
                    <span class="mpc-label"><i class="bi bi-telephone"></i> Teléfono</span>
                    <span class="mpc-val">{{ $p->Telefono ?: '—' }}</span>
                </div>
            </div>

            <div class="mpc-actions">
                @if($p->Telefono)
                    <a href="{{ route('clientes.perfil', $p->Telefono) }}" class="btn btn-sm btn-secondary" title="Ver Perfil Global">
                        <i class="bi bi-person-badge" style="color:var(--accent-gold);"></i> Perfil
                    </a>
                @endif
                <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm btn-primary mpc-btn-primary">
                    <i class="bi bi-eye"></i> Detalle
                </a>
                @if(auth()->check() && auth()->user()->esAdmin())
                <a href="{{ route('participantes.edit', $p) }}" class="btn btn-sm btn-secondary" title="Editar">
                    <i class="bi bi-pencil"></i>
                </a>
                <form action="{{ route('participantes.destroy', $p) }}" method="POST" class="delete-form" data-message="¿Deseas eliminar al participante '{{ $p->Nombre }}'?" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-secondary" style="color:#ef4444;" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
                @endif
                @if($p->Ruta_Gafete)
                    <button class="btn btn-sm btn-secondary" onclick="openPreview('{{ asset('storage/' . $p->Ruta_Gafete) }}', 'Gafete de {{ $p->Nombre }}')" title="Gafete">
                        <i class="bi bi-person-vcard"></i>
                    </button>
                @endif
                @if($p->Ruta_Horario)
                    <button class="btn btn-sm btn-secondary" onclick="openPreview('{{ asset('storage/' . $p->Ruta_Horario) }}', 'Horario de {{ $p->Nombre }}')" title="Horario">
                        <i class="bi bi-calendar3"></i>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px"></i>
            No se encontraron participantes
        </div>
        @endforelse
    </div>

    @if($participantes->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--border-subtle)">
        {{ $participantes->links() }}
    </div>
    @endif
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
