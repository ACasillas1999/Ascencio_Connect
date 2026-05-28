@extends('layouts.app')

@section('title', $evento->name_evento)
@section('page-title', $evento->name_evento)

@section('topbar-actions')
    <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-secondary">
        <i class="bi bi-pencil"></i> Editar
    </a>
    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

@push('styles')
<style>
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1050; justify-content: center; align-items: center;
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background: var(--bg-secondary); padding: 24px; border-radius: 12px;
        width: 100%; max-width: 500px; border: 1px solid var(--border-subtle);
        box-shadow: 0 8px 32px rgba(0,0,0,0.5); position: relative;
        animation: scaleIn .2s ease;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-title { font-size: 16px; font-weight: 700; color: var(--accent-gold); }
    .modal-close { background: none; border: none; color: var(--text-muted); font-size: 24px; cursor: pointer; transition: color .2s; }
    .modal-close:hover { color: var(--text-primary); }
    @keyframes scaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* Estilos de las pestañas (Tabs) */
    .tabs-wrapper::-webkit-scrollbar { display: none; }
    .tab-btn {
        background: none; border: none; padding: 12px 16px;
        color: var(--text-muted); font-size: 14px; font-weight: 600;
        cursor: pointer; position: relative; transition: color .2s;
        white-space: nowrap;
    }
    .tab-btn:hover { color: var(--text-primary); }
    .tab-btn.active { color: var(--accent-gold); }
    .tab-btn.active::after {
        content: ''; position: absolute; bottom: -1px; left: 0; right: 0;
        height: 2px; background: var(--accent-gold); border-radius: 2px 2px 0 0;
    }
    .tab-pane {
        animation: fadeIn .3s ease;
    }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Estilos de la Caja de Tiempo (Agenda) */
    .time-box-card:hover {
        background: rgba(255,255,255,0.06) !important;
        border-color: rgba(255,255,255,0.15) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
</style>
@endpush

@if(auth()->check() && auth()->user()->Rol !== 'Evento')
<!-- INFO CARDS -->
@php
    $capacidad = $evento->capacidad > 0 ? $evento->capacidad : 1;
    $porcentajeOcupacion = min(100, round(($evento->participantes_count / $capacidad) * 100));
    $colorAforo = $porcentajeOcupacion >= 100 ? '#ef4444' : ($porcentajeOcupacion > 85 ? '#f59e0b' : '#10b981');
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
    <div class="kpi-card" style="--kpi-color:{{ $colorAforo }}; display:block; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">
            <div class="kpi-icon" style="margin-bottom:0;"><i class="bi bi-people"></i></div>
            <div>
                <div class="kpi-value" style="display:flex; align-items:baseline; gap:4px;">
                    {{ $evento->participantes_count }} 
                    <span style="font-size:14px;color:var(--text-muted);font-weight:600;">/ {{ number_format($evento->capacidad) }}</span>
                </div>
                <div class="kpi-label">Aforo Registrado ({{ $porcentajeOcupacion }}%)</div>
            </div>
        </div>
        <div style="width:100%; height:6px; background:rgba(255,255,255,0.05); border-radius:4px; overflow:hidden; border:1px solid var(--border-subtle);">
            <div style="height:100%; background:var(--kpi-color); width:{{ $porcentajeOcupacion }}%; border-radius:4px; transition:width 1s ease;"></div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#3b82f6">
        <div class="kpi-icon"><i class="bi bi-mortarboard"></i></div>
        <div>
            <div class="kpi-value">{{ $evento->actividades_count }}</div>
            <div class="kpi-label">Actividades</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#c9a227">
        <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
        <div>
            <div class="kpi-value">{{ $evento->agenda_count }}</div>
            <div class="kpi-label">Slots Agenda</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:{{ $evento->estado === 'EN CURSO' ? '#10b981' : '#64748b' }}">
        <div class="kpi-icon"><i class="bi bi-activity"></i></div>
        <div>
            <div style="font-size:14px;font-weight:700;margin-top:4px">
                <span class="badge {{ $evento->badge_color }}">{{ $evento->estado }}</span>
            </div>
            <div class="kpi-label">Estado</div>
        </div>
    </div>
</div>

<!-- TABS NAV -->
<div class="tabs-wrapper" style="display:flex; gap:16px; margin-bottom:24px; border-bottom:1px solid var(--border-subtle); padding-bottom:0; overflow-x:auto; align-items:center;">
    <button class="tab-btn active" onclick="switchTab(this, 'tab-general')">General y Agenda</button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-participantes')">Participantes <span class="badge badge-secondary" style="margin-left:4px">{{ $evento->participantes_count }}</span></button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-actividades')">Actividades</button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-premios')">Proveedores y Premios</button>
    @if($mockGafete)
    <button class="tab-btn" onclick="switchTab(this, 'tab-gafete')"><i class="bi bi-person-vcard" style="margin-right:4px;"></i> Diseño de Gafete</button>
    @endif
    @if($mockHorario)
    <button class="tab-btn" onclick="switchTab(this, 'tab-horario')"><i class="bi bi-clock" style="margin-right:4px;"></i> Diseño de Horario</button>
    @endif

    {{-- Link directo al módulo de canjes --}}
    <a href="{{ route('eventos.canjes.index', $evento) }}" style="margin-left:auto; display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:linear-gradient(135deg, rgba(212,175,55,0.15), rgba(212,175,55,0.05)); border:1px solid rgba(212,175,55,0.3); border-radius:8px; color:var(--accent-gold); font-size:13px; font-weight:700; text-decoration:none; transition:all 0.2s; white-space:nowrap;" onmouseover="this.style.background='linear-gradient(135deg, rgba(212,175,55,0.25), rgba(212,175,55,0.1))'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='linear-gradient(135deg, rgba(212,175,55,0.05))'; this.style.transform='translateY(0)'">
        <i class="bi bi-gift-fill"></i> Canjear Premios
    </a>
</div>
@endif

<!-- TAB 1: GENERAL Y AGENDA -->
<div id="tab-general" class="tab-pane" style="display:block;">
    <div style="display:grid;grid-template-columns:{{ (auth()->check() && auth()->user()->Rol === 'Evento') ? '1fr' : '320px 1fr' }};gap:20px">
        @if(auth()->check() && auth()->user()->Rol !== 'Evento')
        <!-- DETALLES -->
        <div class="card" style="align-self:start">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-info-circle" style="color:var(--accent-gold);margin-right:8px"></i>Detalles</span>
            </div>
            <div class="card-body">
                @php
                    $filas = [
                        ['Inicio',     $evento->fecha_inicio->format('d/m/Y'), 'calendar3'],
                        ['Fin',        $evento->fecha_fin->format('d/m/Y'), 'calendar3-range'],
                        ['Duración',   $evento->duracion, 'clock'],
                        ['Ubicación',  $evento->ubicacion, 'geo-alt'],
                        ['Capacidad',  number_format($evento->capacidad) . ' personas', 'person-check'],
                        ['Puntos',     ucfirst($evento->tipo_puntos), 'star'],
                    ];
                @endphp
                @foreach($filas as [$label, $valor, $icon])
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-subtle)">
                    <i class="bi bi-{{ $icon }}" style="color:var(--accent-gold);font-size:14px;margin-top:2px;min-width:16px"></i>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:700">{{ $label }}</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $valor }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- AGENDA -->
        <div class="card" style="align-self:start;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="card-title"><i class="bi bi-calendar-event" style="color:var(--accent-gold);margin-right:8px"></i>Horarios (Agenda)</span>
                @if(auth()->check() && auth()->user()->Rol !== 'Evento')
                <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-agenda')"><i class="bi bi-plus-lg"></i> Agregar</button>
                @endif
            </div>
            
            @php
                $period = \Carbon\CarbonPeriod::create($evento->fecha_inicio, $evento->fecha_fin);
                $agendaGrouped = $agenda->groupBy(function($item) {
                    return $item->Fecha->format('Y-m-d');
                });
                
                $allDates = collect($period)->map->format('Y-m-d')
                    ->concat($agendaGrouped->keys())
                    ->unique()
                    ->sort()
                    ->values();
            @endphp
            
            <!-- TABS POR FECHA -->
            <div style="display:flex; overflow-x:auto; border-bottom:1px solid var(--border-subtle); padding:10px 20px 0 20px; gap:8px;">
                @foreach($allDates as $index => $dateStr)
                    @php $dateObj = \Carbon\Carbon::parse($dateStr); @endphp
                    <button class="tab-date-btn" data-date="{{ $dateStr }}"
                            onclick="switchAgendaDate('{{ $dateStr }}')"
                            style="padding:8px 16px; border:none; background:none; color:{{ $index === 0 ? 'var(--accent-gold)' : 'var(--text-muted)' }}; border-bottom:{{ $index === 0 ? '2px solid var(--accent-gold)' : '2px solid transparent' }}; cursor:pointer; font-weight:600; font-size:13px; white-space:nowrap; transition:all 0.2s ease;">
                        {{ $dateObj->locale('es')->isoFormat('D MMM, YYYY') }}
                    </button>
                @endforeach
            </div>

            <!-- CONTENIDO DE CADA FECHA -->
            <div class="table-wrapper" style="padding:0; border-top:none; background:transparent;">
                @foreach($allDates as $index => $dateStr)
                    @php 
                        $slots = $agendaGrouped->get($dateStr, collect());
                        $slotsByTime = $slots->sortBy('Horario')->groupBy('Horario');
                    @endphp
                    <div id="agenda-date-{{ $dateStr }}" class="agenda-date-pane" style="display:{{ $index === 0 ? 'block' : 'none' }}; animation: fadeIn 0.3s ease; padding:20px;">
                        @if($slotsByTime->isEmpty())
                            <div style="text-align:center; padding:30px; color:var(--text-muted); background:var(--bg-primary); border-radius:12px; border:1px dashed var(--border-subtle);">
                                <i class="bi bi-calendar-x" style="font-size:24px;display:block;margin-bottom:8px;opacity:0.5;"></i>
                                No hay actividades programadas para este día.
                            </div>
                        @else
                            <div style="display:flex; flex-direction:column; gap:20px;">
                                @foreach($slotsByTime as $horario => $agendaItems)
                                    <div style="display:flex; gap:20px; border-bottom:1px solid rgba(255,255,255,0.05); padding-bottom:20px;">
                                        <!-- COLUMNA HORARIO -->
                                        <div style="width:120px; flex-shrink:0;">
                                            <div style="font-weight:700; color:var(--text-primary); font-size:14px; position:sticky; top:20px;">
                                                <i class="bi bi-clock" style="color:var(--accent-gold); margin-right:6px;"></i>{{ $horario }}
                                            </div>
                                        </div>
                                        
                                        <!-- COLUMNA SALONES -->
                                        <div style="flex-grow:1; display:flex; flex-direction:column; gap:20px;">
                                            @php
                                                $actividadesBySalon = $agendaItems->sortBy('Salon')->groupBy('Salon');
                                            @endphp
                                            
                                            @foreach($actividadesBySalon as $salon => $slotsSalon)
                                                <div style="background:rgba(255,255,255,0.02); padding:15px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                                                    <h5 style="margin:0 0 15px 0; font-size:13px; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                                                        <i class="bi bi-geo-alt-fill"></i> {{ $salon ?: 'Sin Salón asignado' }}
                                                    </h5>
                                                    
                                                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:15px;">
                                                        @foreach($slotsSalon as $slot)
                                                            @php
                                                                $actividadObj = $actividades->firstWhere('Actividad', $slot->Actividad);
                                                                // Contar inscritos en esta clase de agenda específica
                                                                $inscritos = \DB::table('clase')->where('ID_Agenda', $slot->ID)->count();
                                                            @endphp
                                                            <div class="time-box-card" style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:8px; padding:15px; position:relative; transition:all 0.2s ease; {{ $actividadObj ? 'cursor:pointer;' : '' }}"
                                                                 @if($actividadObj) onclick="window.location.href='{{ route('actividades.show', $actividadObj->ID) }}?horario={{ $slot->ID }}'" @endif>
                                                                @if(auth()->check() && auth()->user()->Rol !== 'Evento')
                                                                <!-- BOTONES ACCION -->
                                                                <div style="position:absolute; top:10px; right:10px; display:flex; gap:4px; z-index:2;" onclick="event.stopPropagation()">
                                                                    <!-- BOTON EDITAR -->
                                                                    <button type="button" class="btn btn-sm" 
                                                                        onclick="openEditAgendaModal({{ $slot->ID }}, '{{ $slot->Actividad }}', '{{ $slot->Fecha->format('Y-m-d') }}', '{{ $slot->Horario }}', '{{ $slot->Salon }}')"
                                                                        style="color:var(--text-secondary); background:none; border:none; padding:4px; transition:color 0.2s;"
                                                                        onmouseover="this.style.color='var(--accent-gold)'" onmouseout="this.style.color='var(--text-secondary)'">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </button>

                                                                    <!-- BOTON ELIMINAR -->
                                                                    <form action="{{ route('agenda.destroy', $slot) }}" method="POST" class="delete-form" data-message="¿Eliminar el horario de '{{ $slot->Actividad }}'?">
                                                                        @csrf @method('DELETE')
                                                                        <button type="button" class="btn btn-sm btn-delete" style="color:#ef4444; background:none; border:none; padding:4px;"><i class="bi bi-x-lg"></i></button>
                                                                    </form>
                                                                </div>
                                                                @endif

                                                                <h4 style="margin:0 0 8px; font-size:14px; font-weight:600; color:var(--text-primary); padding-right:50px; line-height:1.3;">
                                                                    {{ $slot->Actividad }}
                                                                </h4>

                                                                @if($actividadObj)
                                                                    <div style="display:flex; align-items:center; justify-content:space-between; margin-top:6px;">
                                                                        <div style="display:flex; align-items:center; gap:6px; font-size:11px; color:var(--text-muted);">
                                                                            <i class="bi bi-people-fill"></i>
                                                                            <span><strong style="color:var(--accent-gold);">{{ $inscritos }}</strong> / {{ $actividadObj->capacidad }}</span>
                                                                        </div>
                                                                        <span style="font-size:10px; color:var(--accent-gold); display:flex; align-items:center; gap:4px; opacity:0.7;">
                                                                            <i class="bi bi-box-arrow-up-right"></i> Asistencia
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->Rol !== 'Evento')
<!-- TAB 2: PARTICIPANTES -->
<div id="tab-participantes" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-people" style="color:var(--accent-gold);margin-right:8px"></i>Participantes</span>
            <a href="{{ route('participantes.index', ['evento' => $evento->ID]) }}" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>RFC</th>
                        <th>Sucursal</th>
                        <th>Proveedor</th>
                        <th>Puntos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $p)
                    <tr>
                        <td style="font-weight:500">{{ $p->Nombre }}</td>
                        <td style="font-size:12px;color:var(--text-muted)">{{ $p->RFC ?: '—' }}</td>
                        <td style="font-size:12px;color:var(--text-secondary)">{{ $p->Sucursal ?: '—' }}</td>
                        <td style="font-size:12px;color:var(--text-secondary)">{{ Str::limit($p->Proveedor, 25) ?: '—' }}</td>
                        <td><span class="badge badge-gold">{{ number_format($p->Puntos) }}</span></td>
                        <td>
                            <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm btn-secondary"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">Sin participantes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($participantes->hasPages())
        <div style="padding:12px 24px;border-top:1px solid var(--border-subtle)">
            {{ $participantes->links() }}
        </div>
        @endif
    </div>
</div>

<!-- TAB 3: ACTIVIDADES -->
<div id="tab-actividades" class="tab-pane" style="display:none;">
    <div class="card" style="align-self:start;">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span class="card-title"><i class="bi bi-tags" style="color:var(--accent-gold);margin-right:8px"></i>Catálogo de Actividades</span>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-actividad')"><i class="bi bi-plus-lg"></i> Agregar</button>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Capacidad</th>
                        <th>Puntos</th>
                        <th>Exclusiva</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actividades as $act)
                    <tr>
                        <td style="font-weight:500">
                            <a href="{{ route('actividades.show', $act->ID) }}" style="color:var(--accent-gold); text-decoration:none;">
                                {{ $act->Actividad }}
                            </a>
                        </td>
                        <td style="font-size:12px;">{{ $act->capacidad }}</td>
                        <td style="font-size:12px;"><span class="badge badge-gold">{{ $act->Puntos_Default }}</span></td>
                        <td style="font-size:12px;">{{ $act->Exclusiva ? 'Sí' : 'No' }}</td>
                        <td>
                            <form action="{{ route('actividades.destroy', $act) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar la actividad '{{ $act->Actividad }}'?">
                                @csrf @method('DELETE')
                                <button type="button" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444;"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text-muted)">No hay actividades registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 4: PROVEEDORES Y PREMIOS -->
<div id="tab-premios" class="tab-pane" style="display:none;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        
        <!-- PROVEEDORES -->
        <div class="card" style="align-self:start;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="card-title"><i class="bi bi-briefcase" style="color:var(--accent-gold);margin-right:8px"></i>Proveedores del Evento</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Proveedor</th>
                            <th>Puntos por Escaneo</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $prov)
                        <tr>
                            <td style="font-weight:500">{{ $prov->NombreProveedor }}</td>
                            <td>
                                <form action="{{ route('proveedores.update', $prov->ID) }}" method="POST" style="display:flex; gap:8px; align-items:center; margin:0;">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="Puntos" value="{{ $prov->Puntos }}" min="0" required class="form-control" style="width: 80px; padding: 4px; font-size: 13px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary); border-radius: 4px;">
                                    <button type="submit" class="btn btn-sm btn-secondary" style="padding: 4px 8px; border-radius: 4px;" title="Guardar Puntos">
                                        <i class="bi bi-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <span class="badge {{ $prov->Activo ? 'badge-success' : 'badge-secondary' }}">
                                    {{ $prov->Activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('proveedores.destroy', $prov->ID) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar al proveedor '{{ $prov->NombreProveedor }}' de este evento?">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444;"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-muted)">No hay proveedores asignados a este evento.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PREMIOS -->
        <div class="card" style="align-self:start;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="card-title"><i class="bi bi-gift" style="color:var(--accent-gold);margin-right:8px"></i>Premios del Evento</span>
                <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-premio')"><i class="bi bi-plus-lg"></i> Agregar Premio</button>
            </div>
            <div class="table-responsive">
                <table class="table" style="margin-bottom:0">
                    <thead>
                        <tr>
                            <th>Premio</th>
                            <th>Puntos</th>
                            <th>Disponible</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($premios as $premio)
                        <tr>
                            <td style="font-weight:500">{{ $premio->NombrePremio }}</td>
                            <td><span class="badge badge-gold">{{ $premio->PuntosNecesarios }}</span></td>
                            <td>{{ $premio->Disponible }}</td>
                            <td>
                                <form action="{{ route('premios.destroy', $premio) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar el premio '{{ $premio->NombrePremio }}'?">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444;"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text-muted)">No hay premios registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@if($mockGafete)
<!-- TAB 5: GAFETE -->
<div id="tab-gafete" class="tab-pane" style="display:none;">
    <div class="card" style="align-self:start; max-width:1000px; margin:0 auto;">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-person-vcard" style="color:var(--accent-gold);margin-right:8px"></i>Diseño y Configuración de Gafete</span>
        </div>
        <div class="card-body">
            <div style="display:flex; flex-wrap:wrap; gap:24px; justify-content:center; align-items:flex-start;">
                
                <!-- Columna Izquierda: Configuración -->
                <div style="flex:1; min-width:300px; max-width:450px; text-align:center;">
                    <h4 style="font-size:14px; margin-bottom:12px; color:var(--text-primary);">1. Ajuste de Posiciones</h4>
                    <div id="badge-editor-container" style="position:relative; display:inline-block; max-width:260px; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                        <img id="badge-template-img" src="{{ asset('storage/' . $evento->machote_gafete) }}" style="width:100%; display:block;">
                        
                        <!-- Draggable QR -->
                        <div id="draggable-qr" style="position:absolute; width:25%; aspect-ratio:1; border:2px solid var(--accent-gold); background:rgba(212,175,55,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                            <i class="bi bi-qr-code" style="color:var(--accent-gold); font-size:20px;"></i>
                        </div>
                        
                        <!-- Draggable Name -->
                        <div id="draggable-name" style="position:absolute; width:50%; height:30px; border:2px solid #00bc8c; background:rgba(0,188,140,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                            <span id="preview-nombre" style="color:{{ $evento->gafete_color_nombre ?? '#000000' }}; font-family:{{ $evento->gafete_font_family === 'Nexa' ? 'sans-serif' : ($evento->gafete_font_family === 'Courier' ? 'monospace' : ($evento->gafete_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:11px; font-weight:bold;">Nombre</span>
                        </div>

                        <!-- Draggable ID -->
                        <div id="draggable-id" style="position:absolute; width:30%; height:20px; border:2px solid #3b82f6; background:rgba(59,130,246,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                            <span id="preview-id" style="color:{{ $evento->gafete_color_id ?? '#000000' }}; font-family:{{ $evento->gafete_font_family === 'Nexa' ? 'sans-serif' : ($evento->gafete_font_family === 'Courier' ? 'monospace' : ($evento->gafete_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:9px; font-weight:bold;">ID: 1234</span>
                        </div>
                    </div>
                    <small style="color:var(--text-muted); display:block; margin-top:8px;">Arrastra el cuadro amarillo (QR), verde (Nombre) y azul (ID) para ubicarlos.</small>
                    
                    <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data" style="margin-top:15px; text-align:left; max-width:500px; margin-left:auto; margin-right:auto; background:var(--bg-secondary); padding:15px; border-radius:8px; border:1px solid var(--border);">
                        @csrf @method('PUT')
                        <!-- Campos ocultos -->
                        <input type="hidden" name="name_evento" value="{{ $evento->name_evento }}">
                        <input type="hidden" name="duracion" value="{{ $evento->duracion }}">
                        <input type="hidden" name="estado" value="{{ $evento->estado }}">
                        <input type="hidden" name="fecha_inicio" value="{{ $evento->fecha_inicio->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_fin" value="{{ $evento->fecha_fin->format('Y-m-d') }}">
                        <input type="hidden" name="ubicacion" value="{{ $evento->ubicacion }}">
                        <input type="hidden" name="capacidad" value="{{ $evento->capacidad }}">
                        <input type="hidden" name="tipo_puntos" value="{{ $evento->tipo_puntos }}">

                        <div style="margin-bottom:12px;">
                            <label style="font-size:11px; font-weight:bold; color:var(--text-primary);">Cambiar Machote (Fondo)</label>
                            <input type="file" name="machote_gafete" accept="image/*" class="form-control" style="font-size:12px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary);">
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Color de Nombre</label>
                                <input type="color" name="gafete_color_nombre" id="input-color-nombre" value="{{ $evento->gafete_color_nombre ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Color de ID</label>
                                <input type="color" name="gafete_color_id" id="input-color-id" value="{{ $evento->gafete_color_id ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Fuente</label>
                                <select name="gafete_font_family" id="input-font-family" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                                    <option value="Nexa" {{ ($evento->gafete_font_family ?? '') == 'Nexa' ? 'selected' : '' }}>Nexa</option>
                                    <option value="Arial" {{ ($evento->gafete_font_family ?? '') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                    <option value="Courier" {{ ($evento->gafete_font_family ?? '') == 'Courier' ? 'selected' : '' }}>Courier</option>
                                    <option value="Times New Roman" {{ ($evento->gafete_font_family ?? '') == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Tamaño Nombre</label>
                                <input type="number" name="gafete_font_size" value="{{ $evento->gafete_font_size ?? 60 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Tamaño ID</label>
                                <input type="number" name="gafete_id_font_size" value="{{ $evento->gafete_id_font_size ?? 40 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px;">
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">QR X</label><input type="number" name="gafete_qr_x" value="{{ $evento->gafete_qr_x ?? 1755 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">QR Y</label><input type="number" name="gafete_qr_y" value="{{ $evento->gafete_qr_y ?? 280 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Nombre X</label><input type="number" name="gafete_nombre_x" value="{{ $evento->gafete_nombre_x ?? 202 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Nombre Y</label><input type="number" name="gafete_nombre_y" value="{{ $evento->gafete_nombre_y ?? 1050 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">ID X</label><input type="number" name="gafete_id_x" value="{{ $evento->gafete_id_x ?? 202 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">ID Y</label><input type="number" name="gafete_id_y" value="{{ $evento->gafete_id_y ?? 1200 }}" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-sm btn-primary" style="width:100%; margin-top:10px; font-size:13px; font-weight:bold; letter-spacing:0.5px;">Actualizar Gafete</button>
                    </form>
                </div>

                <!-- Columna Derecha: Vista Previa Real -->
                <div style="flex:1; min-width:300px; max-width:450px; text-align:center;">
                    <h4 style="font-size:14px; margin-bottom:12px; color:var(--text-primary);">2. Vista Previa Real (Resultado Final)</h4>
                    <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.4); border:2px solid var(--border); background:var(--bg-secondary); padding:4px;">
                        <img id="real-preview-image" src="{{ asset('storage/' . $mockGafete) }}?t={{ time() }}" style="width:100%; display:block; border-radius:4px;" alt="Vista previa real del gafete generado">
                        <!-- Spinner superpuesto -->
                        <div id="preview-spinner" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
                            <div class="spinner-border" style="color:var(--accent-gold);" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:16px; padding:12px; background:rgba(59, 130, 246, 0.1); border-left:4px solid #3b82f6; border-radius:4px; text-align:left;">
                        <p style="margin:0; font-size:12px; color:var(--text-secondary); line-height:1.5;">
                            <i class="bi bi-info-circle-fill" style="color:#3b82f6; margin-right:4px;"></i>
                            <strong>Nota:</strong> Esta vista se actualiza automáticamente al arrastrar los componentes o cambiar colores/fuentes. Para guardar permanentemente, haz clic en <strong>Actualizar Gafete</strong>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

@if($mockHorario)
<!-- TAB 6: HORARIO -->
<div id="tab-horario" class="tab-pane" style="display:none;">
    <div class="card" style="align-self:start; max-width:1000px; margin:0 auto;">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-clock" style="color:var(--accent-gold);margin-right:8px"></i>Diseño y Configuración de Horario</span>
        </div>
        <div class="card-body">
            <div style="display:flex; flex-wrap:wrap; gap:24px; justify-content:center; align-items:flex-start;">
                
                <!-- Columna Izquierda: Configuración -->
                <div style="flex:1; min-width:300px; max-width:450px; text-align:center;">
                    <h4 style="font-size:14px; margin-bottom:12px; color:var(--text-primary);">1. Ajuste de Posiciones</h4>
                    <div id="horario-editor-container" style="position:relative; display:inline-block; max-width:260px; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                        <img id="horario-template-img" src="{{ asset('storage/' . $evento->machote_horario) }}" style="width:100%; display:block;">
                        
                        <!-- Draggable Name -->
                        <div id="draggable-horario-name" style="position:absolute; width:50%; height:30px; border:2px solid #00bc8c; background:rgba(0,188,140,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                            <span id="preview-horario-nombre" style="color:{{ $evento->horario_color_nombre ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:11px; font-weight:bold;">Nombre</span>
                        </div>

                        <!-- Draggable ID -->
                        <div id="draggable-horario-id" style="position:absolute; width:30%; height:20px; border:2px solid #3b82f6; background:rgba(59,130,246,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                            <span id="preview-horario-id" style="color:{{ $evento->horario_color_id ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:9px; font-weight:bold;">ID: 1234</span>
                        </div>
                        
                        <!-- Draggable Lista -->
                        <div id="draggable-horario-lista" style="position:absolute; width:60%; height:100px; border:2px dashed #f59e0b; background:rgba(245,158,11,0.2); cursor:move; display:flex; flex-direction:column; align-items:flex-start; padding:4px; top:0; left:0;">
                            <span style="color:{{ $evento->horario_color_lista ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:8px; font-weight:bold;">09:00 - Registro</span>
                            <span style="color:{{ $evento->horario_color_lista ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:8px; font-weight:bold;">10:00 - Conferencia 1</span>
                        </div>
                    </div>
                    <small style="color:var(--text-muted); display:block; margin-top:8px;">Arrastra verde (Nombre), azul (ID) y naranja (Lista de Horarios) para ubicarlos.</small>
                    
                    <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data" style="margin-top:15px; text-align:left; max-width:500px; margin-left:auto; margin-right:auto; background:var(--bg-secondary); padding:15px; border-radius:8px; border:1px solid var(--border);">
                        @csrf @method('PUT')
                        <!-- Campos ocultos requeridos por la validación -->
                        <input type="hidden" name="name_evento" value="{{ $evento->name_evento }}">
                        <input type="hidden" name="duracion" value="{{ $evento->duracion }}">
                        <input type="hidden" name="estado" value="{{ $evento->estado }}">
                        <input type="hidden" name="fecha_inicio" value="{{ $evento->fecha_inicio->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_fin" value="{{ $evento->fecha_fin->format('Y-m-d') }}">
                        <input type="hidden" name="ubicacion" value="{{ $evento->ubicacion }}">
                        <input type="hidden" name="capacidad" value="{{ $evento->capacidad }}">
                        <input type="hidden" name="tipo_puntos" value="{{ $evento->tipo_puntos }}">

                        <div style="margin-bottom:12px;">
                            <label style="font-size:11px; font-weight:bold; color:var(--text-primary);">Cambiar Machote Horario</label>
                            <input type="file" name="machote_horario" accept="image/*" class="form-control" style="font-size:12px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary);">
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Color de Nombre</label>
                                <input type="color" name="horario_color_nombre" id="input-horario-color-nombre" value="{{ $evento->horario_color_nombre ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Color de ID</label>
                                <input type="color" name="horario_color_id" id="input-horario-color-id" value="{{ $evento->horario_color_id ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Color de Lista</label>
                                <input type="color" name="horario_color_lista" id="input-horario-color-lista" value="{{ $evento->horario_color_lista ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                            </div>
                            <div class="form-group">
                                <label style="font-size:11px; color:var(--text-primary);">Fuente</label>
                                <select name="horario_font_family" id="input-horario-font-family" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                                    <option value="Nexa" {{ ($evento->horario_font_family ?? '') == 'Nexa' ? 'selected' : '' }}>Nexa</option>
                                    <option value="Arial" {{ ($evento->horario_font_family ?? '') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                    <option value="Courier" {{ ($evento->horario_font_family ?? '') == 'Courier' ? 'selected' : '' }}>Courier</option>
                                    <option value="Times New Roman" {{ ($evento->horario_font_family ?? '') == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                                </select>
                            </div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Tamaño Nombre</label><input type="number" name="horario_font_size" value="{{ $evento->horario_font_size ?? 40 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Tamaño ID</label><input type="number" name="horario_id_font_size" value="{{ $evento->horario_id_font_size ?? 30 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Tam. Lista</label><input type="number" name="horario_lista_font_size" value="{{ $evento->horario_lista_font_size ?? 24 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:8px;">
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Nombre X</label><input type="number" name="horario_nombre_x" value="{{ $evento->horario_nombre_x ?? 202 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Nombre Y</label><input type="number" name="horario_nombre_y" value="{{ $evento->horario_nombre_y ?? 150 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">ID X</label><input type="number" name="horario_id_x" value="{{ $evento->horario_id_x ?? 202 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">ID Y</label><input type="number" name="horario_id_y" value="{{ $evento->horario_id_y ?? 250 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Lista X</label><input type="number" name="horario_lista_x" value="{{ $evento->horario_lista_x ?? 100 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Lista Y</label><input type="number" name="horario_lista_y" value="{{ $evento->horario_lista_y ?? 350 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Lista Ancho</label><input type="number" name="horario_lista_w" value="{{ $evento->horario_lista_w ?? 800 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                            <div class="form-group"><label style="font-size:11px; color:var(--text-primary);">Lista Alto</label><input type="number" name="horario_lista_h" value="{{ $evento->horario_lista_h ?? 1000 }}" class="form-control" style="padding:4px 8px; font-size:12px;"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-sm btn-primary" style="width:100%; margin-top:10px; font-size:13px; font-weight:bold; letter-spacing:0.5px;">Actualizar Horario</button>
                    </form>
                </div>

                <!-- Columna Derecha: Vista Previa Real -->
                <div style="flex:1; min-width:300px; max-width:450px; text-align:center;">
                    <h4 style="font-size:14px; margin-bottom:12px; color:var(--text-primary);">2. Vista Previa Real (Resultado Final)</h4>
                    <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.4); border:2px solid var(--border); background:var(--bg-secondary); padding:4px;">
                        <img id="real-preview-horario-image" src="{{ asset('storage/' . $mockHorario) }}?t={{ time() }}" style="width:100%; display:block; border-radius:4px;" alt="Vista previa real del horario generado">
                        <!-- Spinner superpuesto -->
                        <div id="preview-horario-spinner" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
                            <div class="spinner-border" style="color:var(--accent-gold);" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:16px; padding:12px; background:rgba(59, 130, 246, 0.1); border-left:4px solid #3b82f6; border-radius:4px; text-align:left;">
                        <p style="margin:0; font-size:12px; color:var(--text-secondary); line-height:1.5;">
                            <i class="bi bi-info-circle-fill" style="color:#3b82f6; margin-right:4px;"></i>
                            <strong>Nota:</strong> Esta vista se actualiza automáticamente al arrastrar los componentes o cambiar colores/fuentes. Para guardar permanentemente, haz clic en <strong>Actualizar Horario</strong>. El ancho y alto de la lista deben ajustarse manualmente.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

@endif

<!-- Modales (Nuevos) -->
<!-- Modal Actividad -->
<div id="modal-actividad" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Agregar Actividad</h3>
            <button class="modal-close" onclick="closeModal('modal-actividad')">&times;</button>
        </div>
        <form method="POST" action="{{ route('eventos.actividades.store', $evento) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Nombre de la Actividad *</label>
                    <input name="Actividad" type="text" class="form-control" required placeholder="Ej: Conferencia Inicial">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Descripción *</label>
                    <textarea name="Descripcion" class="form-control" rows="2" required placeholder="Breve descripción de la actividad"></textarea>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Capacidad *</label>
                    <input name="capacidad" type="number" class="form-control" required value="100">
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Puntos Default</label>
                    <input name="Puntos_Default" type="number" class="form-control" value="0">
                </div>
                <div style="grid-column:1/-1; display:flex; align-items:center; gap:8px; margin-top:8px;">
                    <input type="checkbox" name="Exclusiva" value="1" style="accent-color:var(--accent-gold); width:16px; height:16px;">
                    <label style="font-size:13px;margin:0;color:var(--text-primary);">¿Es Exclusiva?</label>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-actividad')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Agenda -->
<div id="modal-agenda" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Agregar Horario a la Agenda</h3>
            <button class="modal-close" onclick="closeModal('modal-agenda')">&times;</button>
        </div>
        <form method="POST" action="{{ route('eventos.agenda.store', $evento) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Actividad *</label>
                    <select name="Actividad" class="form-control" required>
                        <option value="">Selecciona una actividad...</option>
                        @foreach($actividades as $act)
                            <option value="{{ $act->Actividad }}">{{ $act->Actividad }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Fecha *</label>
                    <input name="Fecha" type="date" class="form-control" 
                           min="{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d') }}" 
                           max="{{ \Carbon\Carbon::parse($evento->fecha_fin)->format('Y-m-d') }}" 
                           required>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Horario (HH:MM-HH:MM) *</label>
                    <input name="Horario" type="text" class="form-control" required placeholder="09:00-10:00" pattern="^\d{2}:\d{2}-\d{2}:\d{2}$">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Salón / Ubicación *</label>
                    <input name="Salon" type="text" class="form-control" list="salones-lista" required placeholder="Ej: Salón Principal">
                    <datalist id="salones-lista">
                        @foreach($agenda->pluck('Salon')->filter()->unique() as $salonExistente)
                            <option value="{{ $salonExistente }}"></option>
                        @endforeach
                        @for($i = 1; $i <= $numSalones; $i++)
                            <option value="Salón {{ $i }}"></option>
                        @endfor
                    </datalist>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-agenda')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Agenda -->
<div id="modal-agenda-edit" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Editar Horario de Agenda</h3>
            <button class="modal-close" onclick="closeModal('modal-agenda-edit')">&times;</button>
        </div>
        <form id="form-agenda-edit" method="POST" action="{{ route('agenda.update', 999999) }}">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Actividad *</label>
                    <select id="edit_agenda_actividad" name="Actividad" class="form-control" required>
                        <option value="">Selecciona una actividad...</option>
                        @foreach($actividades as $act)
                            <option value="{{ $act->Actividad }}">{{ $act->Actividad }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Fecha *</label>
                    <input id="edit_agenda_fecha" name="Fecha" type="date" class="form-control" 
                           min="{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d') }}" 
                           max="{{ \Carbon\Carbon::parse($evento->fecha_fin)->format('Y-m-d') }}" 
                           required>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Horario (HH:MM-HH:MM) *</label>
                    <input id="edit_agenda_horario" name="Horario" type="text" class="form-control" required placeholder="09:00-10:00" pattern="^\d{2}:\d{2}-\d{2}:\d{2}$">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label" style="font-size:12px">Salón / Ubicación *</label>
                    <input id="edit_agenda_salon" name="Salon" type="text" class="form-control" list="salones-lista" required placeholder="Ej: Salón Principal">
                </div>
            </div>
            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-agenda-edit')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Proveedor -->
<div id="modal-proveedor" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Asignar Proveedor</h3>
            <button class="modal-close" onclick="closeModal('modal-proveedor')">&times;</button>
        </div>
        <form method="POST" action="{{ route('eventos.proveedores.store', $evento) }}">
            @csrf
            <div style="display:grid;gap:12px;">
                <div>
                    <label class="form-label" style="font-size:12px">Nombre del Proveedor *</label>
                    <input name="NombreProveedor" type="text" class="form-control" required placeholder="Ej: Nombre de Usuario del Proveedor">
                    <small style="color:var(--text-muted); display:block; margin-top:4px;">Debe coincidir con el usuario con el que iniciará sesión.</small>
                </div>
                <div>
                    <label class="form-label" style="font-size:12px">Puntos que Otorgará *</label>
                    <input name="Puntos" type="number" class="form-control" required value="10" min="0">
                </div>
            </div>
            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-proveedor')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Premio -->
<div id="modal-premio" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Agregar Premio</h3>
            <button class="modal-close" onclick="closeModal('modal-premio')">&times;</button>
        </div>
        <form method="POST" action="{{ route('eventos.premios.store', $evento) }}">
            @csrf
            <div style="display:grid;gap:12px;">
                <div>
                    <label class="form-label" style="font-size:12px">Nombre del Premio *</label>
                    <input name="NombrePremio" type="text" class="form-control" required placeholder="Ej: Gorra Conmemorativa">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div>
                        <label class="form-label" style="font-size:12px">Puntos Necesarios *</label>
                        <input name="PuntosNecesarios" type="number" class="form-control" required min="1" value="100">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:12px">Stock Disponible *</label>
                        <input name="Disponible" type="number" class="form-control" required min="0" value="10">
                    </div>
                </div>
            </div>
            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-premio')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
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

    // --- Control de Pestañas (Tabs) ---
    function switchTab(btn, tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';
    }

    // --- Drag and Drop Editor ---
    const container = document.getElementById('badge-editor-container');
    const img = document.getElementById('badge-template-img');
    const qr = document.getElementById('draggable-qr');
    const nameLabel = document.getElementById('draggable-name');
    const idLabel = document.getElementById('draggable-id');

    const qrXInput = document.getElementsByName('gafete_qr_x')[0];
    const qrYInput = document.getElementsByName('gafete_qr_y')[0];
    const nameXInput = document.getElementsByName('gafete_nombre_x')[0];
    const nameYInput = document.getElementsByName('gafete_nombre_y')[0];
    const idXInput = document.getElementsByName('gafete_id_x')[0];
    const idYInput = document.getElementsByName('gafete_id_y')[0];

    // Live preview elements
    const colorNombreInput = document.getElementById('input-color-nombre');
    const colorIdInput = document.getElementById('input-color-id');
    const fontSelector = document.getElementById('input-font-family');
    const previewNombre = document.getElementById('preview-nombre');
    const previewId = document.getElementById('preview-id');
    const previewSpinner = document.getElementById('preview-spinner');

    let updateTimeout;
    function updateRealPreview() {
        const form = document.querySelector('#tab-gafete form');
        const formData = new FormData(form);
        formData.delete('machote_gafete'); // No subir imagen en cada AJAX
        
        if(previewSpinner) previewSpinner.style.display = 'flex';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            const realPreviewImg = document.getElementById('real-preview-image');
            if(realPreviewImg) {
                const currentSrc = realPreviewImg.src.split('?')[0];
                const newImg = new Image();
                newImg.onload = function() {
                    realPreviewImg.src = newImg.src;
                    if(previewSpinner) previewSpinner.style.display = 'none';
                };
                newImg.src = currentSrc + '?t=' + new Date().getTime();
            } else {
                if(previewSpinner) previewSpinner.style.display = 'none';
            }
        }).catch(() => {
            if(previewSpinner) previewSpinner.style.display = 'none';
        });
    }

    if (colorNombreInput && previewNombre) {
        colorNombreInput.addEventListener('input', (e) => {
            previewNombre.style.color = e.target.value;
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(updateRealPreview, 500); // Debounce for color picker
        });
        colorIdInput.addEventListener('input', (e) => {
            previewId.style.color = e.target.value;
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(updateRealPreview, 500);
        });
        fontSelector.addEventListener('change', (e) => {
            let font = e.target.value === 'Nexa' ? 'sans-serif' : (e.target.value === 'Courier' ? 'monospace' : (e.target.value === 'Times New Roman' ? 'serif' : 'Arial'));
            previewNombre.style.fontFamily = font;
            previewId.style.fontFamily = font;
            updateRealPreview();
        });
    }

    if (img) {
        function setupEditor() {
            const originalWidth = img.naturalWidth || 2000;
            const displayWidth = img.clientWidth;
            const scale = displayWidth / originalWidth;

            // Posiciones iniciales (escaladas)
            const qrX = parseFloat(qrXInput.value) || 0;
            const qrY = parseFloat(qrYInput.value) || 0;
            const nameX = parseFloat(nameXInput.value) || 0;
            const nameY = parseFloat(nameYInput.value) || 0;
            const idX = parseFloat(idXInput ? idXInput.value : 0) || 0;
            const idY = parseFloat(idYInput ? idYInput.value : 0) || 0;

            qr.style.left = (qrX * scale) + 'px';
            qr.style.top = (qrY * scale) + 'px';

            nameLabel.style.left = (nameX * scale) + 'px';
            nameLabel.style.top = (nameY * scale) + 'px';

            if (idLabel) {
                idLabel.style.left = (idX * scale) + 'px';
                idLabel.style.top = (idY * scale) + 'px';
            }
        }

        if (img.complete) {
            setupEditor();
        } else {
            img.onload = setupEditor;
        }

        function makeDraggable(element, onDrag) {
            let isDragging = false;
            let startX, startY;

            element.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - element.offsetLeft;
                startY = e.clientY - element.offsetTop;
                element.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                let left = e.clientX - startX;
                let top = e.clientY - startY;

                const containerRect = container.getBoundingClientRect();
                const elementRect = element.getBoundingClientRect();

                if (left < 0) left = 0;
                if (top < 0) top = 0;
                if (left + elementRect.width > containerRect.width) left = containerRect.width - elementRect.width;
                if (top + elementRect.height > containerRect.height) top = containerRect.height - elementRect.height;

                element.style.left = left + 'px';
                element.style.top = top + 'px';

                onDrag(left, top);
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    element.style.cursor = 'move';
                    updateRealPreview();
                }
            });
        }

        makeDraggable(qr, function(left, top) {
            const scale = img.naturalWidth / img.clientWidth;
            qrXInput.value = Math.round(left * scale);
            qrYInput.value = Math.round(top * scale);
        });

        makeDraggable(nameLabel, function(left, top) {
            const scale = img.naturalWidth / img.clientWidth;
            nameXInput.value = Math.round(left * scale);
            nameYInput.value = Math.round(top * scale);
        });

        if (idLabel) {
            makeDraggable(idLabel, function(left, top) {
                const scale = img.naturalWidth / img.clientWidth;
                idXInput.value = Math.round(left * scale);
                idYInput.value = Math.round(top * scale);
            });
        }
    }

    // --- Drag and Drop Editor Horario ---
    const hContainer = document.getElementById('horario-editor-container');
    const hImg = document.getElementById('horario-template-img');
    const hNameLabel = document.getElementById('draggable-horario-name');
    const hIdLabel = document.getElementById('draggable-horario-id');
    const hListaLabel = document.getElementById('draggable-horario-lista');

    const hNameXInput = document.getElementsByName('horario_nombre_x')[0];
    const hNameYInput = document.getElementsByName('horario_nombre_y')[0];
    const hIdXInput = document.getElementsByName('horario_id_x')[0];
    const hIdYInput = document.getElementsByName('horario_id_y')[0];
    const hListaXInput = document.getElementsByName('horario_lista_x')[0];
    const hListaYInput = document.getElementsByName('horario_lista_y')[0];
    const hListaWInput = document.getElementsByName('horario_lista_w')[0];
    const hListaHInput = document.getElementsByName('horario_lista_h')[0];

    const hColorNombreInput = document.getElementById('input-horario-color-nombre');
    const hColorIdInput = document.getElementById('input-horario-color-id');
    const hColorListaInput = document.getElementById('input-horario-color-lista');
    const hFontSelector = document.getElementById('input-horario-font-family');
    const hPreviewNombre = document.getElementById('preview-horario-nombre');
    const hPreviewId = document.getElementById('preview-horario-id');
    const hPreviewLista = document.getElementById('draggable-horario-lista');
    const hPreviewSpinner = document.getElementById('preview-horario-spinner');

    let hUpdateTimeout;
    function updateRealPreviewHorario() {
        const form = document.querySelector('#tab-horario form');
        const formData = new FormData(form);
        formData.delete('machote_horario');
        
        if(hPreviewSpinner) hPreviewSpinner.style.display = 'flex';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            const realPreviewImg = document.getElementById('real-preview-horario-image');
            if(realPreviewImg) {
                const currentSrc = realPreviewImg.src.split('?')[0];
                const newImg = new Image();
                newImg.onload = function() {
                    realPreviewImg.src = newImg.src;
                    if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
                };
                newImg.src = currentSrc + '?t=' + new Date().getTime();
            } else {
                if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
            }
        }).catch(() => {
            if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
        });
    }

    if (hColorNombreInput && hPreviewNombre) {
        hColorNombreInput.addEventListener('input', (e) => {
            hPreviewNombre.style.color = e.target.value;
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hColorIdInput.addEventListener('input', (e) => {
            hPreviewId.style.color = e.target.value;
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hColorListaInput.addEventListener('input', (e) => {
            // Actualizar spans hijos
            hPreviewLista.querySelectorAll('span').forEach(span => {
                span.style.color = e.target.value;
            });
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hFontSelector.addEventListener('change', (e) => {
            let font = e.target.value === 'Nexa' ? 'sans-serif' : (e.target.value === 'Courier' ? 'monospace' : (e.target.value === 'Times New Roman' ? 'serif' : 'Arial'));
            hPreviewNombre.style.fontFamily = font;
            hPreviewId.style.fontFamily = font;
            hPreviewLista.querySelectorAll('span').forEach(span => {
                span.style.fontFamily = font;
            });
            updateRealPreviewHorario();
        });
        
        // Listeners manuales para los inputs de W y H para recargar preview
        hListaWInput.addEventListener('input', () => {
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 800);
        });
        hListaHInput.addEventListener('input', () => {
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 800);
        });
    }

    if (hImg) {
        function setupEditorHorario() {
            const originalWidth = hImg.naturalWidth || 2000;
            const displayWidth = hImg.clientWidth;
            const scale = displayWidth / originalWidth;

            const nameX = parseFloat(hNameXInput.value) || 0;
            const nameY = parseFloat(hNameYInput.value) || 0;
            const idX = parseFloat(hIdXInput.value) || 0;
            const idY = parseFloat(hIdYInput.value) || 0;
            const listaX = parseFloat(hListaXInput.value) || 0;
            const listaY = parseFloat(hListaYInput.value) || 0;
            const listaW = parseFloat(hListaWInput.value) || 800;
            const listaH = parseFloat(hListaHInput.value) || 1000;

            hNameLabel.style.left = (nameX * scale) + 'px';
            hNameLabel.style.top = (nameY * scale) + 'px';

            if (hIdLabel) {
                hIdLabel.style.left = (idX * scale) + 'px';
                hIdLabel.style.top = (idY * scale) + 'px';
            }
            if (hListaLabel) {
                hListaLabel.style.left = (listaX * scale) + 'px';
                hListaLabel.style.top = (listaY * scale) + 'px';
                hListaLabel.style.width = (listaW * scale) + 'px';
                hListaLabel.style.height = (listaH * scale) + 'px';
            }
        }

        if (hImg.complete) {
            setupEditorHorario();
        } else {
            hImg.onload = setupEditorHorario;
        }

        function makeDraggableH(element, onDrag, containerRef) {
            let isDragging = false;
            let startX, startY;

            element.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - element.offsetLeft;
                startY = e.clientY - element.offsetTop;
                element.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                let left = e.clientX - startX;
                let top = e.clientY - startY;

                const containerRect = containerRef.getBoundingClientRect();
                const elementRect = element.getBoundingClientRect();

                if (left < 0) left = 0;
                if (top < 0) top = 0;
                if (left + elementRect.width > containerRect.width) left = containerRect.width - elementRect.width;
                if (top + elementRect.height > containerRect.height) top = containerRect.height - elementRect.height;

                element.style.left = left + 'px';
                element.style.top = top + 'px';

                onDrag(left, top);
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    element.style.cursor = 'move';
                    updateRealPreviewHorario();
                }
            });
        }

        makeDraggableH(hNameLabel, function(left, top) {
            const scale = hImg.naturalWidth / hImg.clientWidth;
            hNameXInput.value = Math.round(left * scale);
            hNameYInput.value = Math.round(top * scale);
        }, hContainer);

        if (hIdLabel) {
            makeDraggableH(hIdLabel, function(left, top) {
                const scale = hImg.naturalWidth / hImg.clientWidth;
                hIdXInput.value = Math.round(left * scale);
                hIdYInput.value = Math.round(top * scale);
            }, hContainer);
        }
        
        if (hListaLabel) {
            makeDraggableH(hListaLabel, function(left, top) {
                const scale = hImg.naturalWidth / hImg.clientWidth;
                hListaXInput.value = Math.round(left * scale);
                hListaYInput.value = Math.round(top * scale);
            }, hContainer);
        }
    }

    // Cambiar Fecha de Agenda
    function switchAgendaDate(dateStr) {
        // Ocultar todas las tablas
        document.querySelectorAll('.agenda-date-pane').forEach(pane => pane.style.display = 'none');
        // Quitar active a todos los botones
        document.querySelectorAll('.tab-date-btn').forEach(btn => {
            btn.style.color = 'var(--text-muted)';
            btn.style.borderBottom = '2px solid transparent';
            btn.classList.remove('active');
        });

        // Mostrar el seleccionado
        const targetPane = document.getElementById('agenda-date-' + dateStr);
        if (targetPane) targetPane.style.display = 'block';

        // Estilizar el botón clickeado
        const targetBtn = document.querySelector(`.tab-date-btn[data-date="${dateStr}"]`);
        if (targetBtn) {
            targetBtn.style.color = 'var(--accent-gold)';
            targetBtn.style.borderBottom = '2px solid var(--accent-gold)';
            targetBtn.classList.add('active');
        }
    }

    // Modal Edit Agenda Helper
    function openEditAgendaModal(id, actividad, fecha, horario, salon) {
        let baseUrl = "{{ route('agenda.update', 999999) }}";
        document.getElementById('form-agenda-edit').action = baseUrl.replace('999999', id);
        
        document.getElementById('edit_agenda_actividad').value = actividad;
        document.getElementById('edit_agenda_fecha').value = fecha;
        document.getElementById('edit_agenda_horario').value = horario;
        document.getElementById('edit_agenda_salon').value = salon || '';
        
        openModal('modal-agenda-edit');
    }

    // Modal Helpers
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    // Cerrar al hacer click fuera en los nuevos modales
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
        }
    });

    // Sweet Alert para botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const message = form.getAttribute('data-message') || '¿Estás seguro de realizar esta acción?';
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: 'var(--bg-secondary)',
                color: 'var(--text-primary)'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@endsection
