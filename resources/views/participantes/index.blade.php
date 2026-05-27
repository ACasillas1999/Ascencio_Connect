@extends('layouts.app')

@section('title', 'Participantes')
@section('page-title', 'Participantes')

@section('topbar-actions')
    <a href="{{ route('participantes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Nuevo Participante
    </a>
@endsection

@section('content')

<!-- FILTROS -->
<div class="card" style="margin-bottom:20px">
    <div class="card-body" style="padding:16px 24px">
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
            <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                <a href="{{ route('participantes.index') }}" class="btn btn-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- TABLA -->
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-people" style="color:var(--accent-gold);margin-right:8px"></i>
            Participantes
            <span style="font-size:12px;color:var(--text-muted);font-weight:400;margin-left:8px">({{ $participantes->total() }})</span>
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Evento</th>
                    <th>Sucursal</th>
                    <th>Proveedor</th>
                    <th>Teléfono</th>
                    <th>Puntos</th>
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
                            <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm btn-secondary" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('participantes.edit', $p) }}" class="btn btn-sm btn-secondary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
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
    @if($participantes->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--border-subtle)">
        {{ $participantes->links() }}
    </div>
    @endif
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
