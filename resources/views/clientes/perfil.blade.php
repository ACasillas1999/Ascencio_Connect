@extends('layouts.app')

@section('title', 'Perfil de Cliente')
@section('page-title', 'Perfil de Cliente')

@section('topbar-actions')
    <a href="{{ route('participantes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')
<div style="max-width:1000px; margin: 0 auto; display:grid; grid-template-columns:1fr 3fr; gap:24px;">
    <!-- Columna Izquierda: Info de Perfil -->
    <div style="display:flex; flex-direction:column; gap:24px;">
        <div class="card" style="text-align:center; padding:24px;">
            <div style="width:80px; height:80px; border-radius:50%; background:var(--accent-gold); color:#000; display:flex; align-items:center; justify-content:center; font-size:32px; font-weight:bold; margin:0 auto 16px;">
                {{ substr($cliente->Nombre, 0, 1) }}
            </div>
            <h3 style="margin:0 0 4px; font-size:20px; color:var(--text-primary);">{{ $cliente->Nombre }}</h3>
            <p style="margin:0 0 16px; font-size:14px; color:var(--text-secondary);">
                <i class="bi bi-telephone"></i> {{ $telefono }}
            </p>
            
            <div style="display:flex; flex-direction:column; gap:8px; text-align:left; font-size:13px; color:var(--text-secondary); background:var(--bg-primary); padding:16px; border-radius:8px;">
                @if($cliente->RFC)<div><strong>RFC:</strong> {{ $cliente->RFC }}</div>@endif
                @if($cliente->Puesto)<div><strong>Puesto:</strong> {{ $cliente->Puesto }}</div>@endif
                @if($cliente->Sucursal)<div><strong>Sucursal:</strong> {{ $cliente->Sucursal }}</div>@endif
                @if($cliente->Vendedor)<div><strong>Vendedor:</strong> {{ $cliente->Vendedor }}</div>@endif
                @if($cliente->Proveedor)<div><strong>Proveedor:</strong> {{ $cliente->Proveedor }}</div>@endif
            </div>
        </div>

        <div class="card" style="padding:20px;">
            <h4 style="font-size:14px; color:var(--text-muted); margin-bottom:12px; text-transform:uppercase; letter-spacing:1px;">Métricas Globales</h4>
            
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; border-bottom:1px solid var(--border-subtle); padding-bottom:12px;">
                <div style="font-size:14px; color:var(--text-secondary);">Total Eventos</div>
                <div style="font-size:24px; font-weight:bold; color:var(--text-primary);">{{ $eventosAsistidos }}</div>
            </div>
            
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:14px; color:var(--text-secondary);">Puntos Acumulados</div>
                <div style="font-size:24px; font-weight:bold; color:var(--accent-gold);">{{ number_format($totalPuntos) }}</div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Historial de Eventos -->
    <div>
        <h4 style="margin:0 0 20px; font-size:18px; color:var(--text-primary);">Historial de Asistencia</h4>
        
        <div style="display:flex; flex-direction:column; gap:16px;">
            @foreach($participantes as $p)
            <div class="card" style="padding:0; overflow:hidden; border-left:4px solid var(--accent-gold);">
                <div style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center; background:var(--bg-secondary);">
                    <div>
                        <h5 style="margin:0 0 4px; font-size:16px; color:var(--text-primary);">
                            {{ $p->evento ? $p->evento->name_evento : 'Evento Eliminado' }}
                        </h5>
                        <div style="font-size:12px; color:var(--text-muted);">
                            <i class="bi bi-calendar"></i> 
                            {{ $p->evento ? $p->evento->fecha_inicio->format('d M, Y') : '' }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Puntos Obtenidos</div>
                        <span class="badge badge-gold" style="font-size:14px;">+{{ number_format($p->Puntos) }}</span>
                    </div>
                </div>
                
                @if($p->clases->count() > 0 || $p->canjes->count() > 0)
                <div style="padding:16px 20px; border-top:1px solid var(--border-subtle); background:var(--bg-primary);">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        
                        <!-- Actividades -->
                        @if($p->clases->count() > 0)
                        <div>
                            <div style="font-size:12px; font-weight:bold; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;">Actividades</div>
                            <ul style="margin:0; padding-left:16px; font-size:13px; color:var(--text-secondary);">
                                @foreach($p->clases as $c)
                                    @if($c->agenda)
                                    <li>{{ $c->agenda->Actividad }} ({{ $c->agenda->Fecha }} {{ $c->agenda->Horario }})</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Canjes -->
                        @if($p->canjes->count() > 0)
                        <div>
                            <div style="font-size:12px; font-weight:bold; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;">Premios Canjeados</div>
                            <ul style="margin:0; padding-left:16px; font-size:13px; color:var(--text-secondary);">
                                @foreach($p->canjes as $canje)
                                    @if($canje->premio)
                                    <li>{{ $canje->premio->NombrePremio }} <span style="color:#ef4444; font-size:11px;">(-{{ $canje->PuntosGastados }} pts)</span></li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        
                    </div>
                </div>
                @endif
                
                <div style="padding:12px 20px; background:var(--bg-secondary); border-top:1px solid var(--border-subtle); text-align:right;">
                    <a href="{{ route('participantes.show', $p->ID) }}" class="btn btn-sm btn-secondary" style="font-size:12px;">Ver detalles del registro</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
