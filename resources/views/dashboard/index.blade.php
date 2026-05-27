@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- KPI GRID -->
<div class="kpi-grid" style="margin-bottom:24px">
    <div class="kpi-card" style="--kpi-color:#10b981">
        <div class="kpi-icon"><i class="bi bi-calendar-check"></i></div>
        <div>
            <div class="kpi-value">{{ $stats['eventos_activos'] }}</div>
            <div class="kpi-label">Eventos Activos</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#3b82f6">
        <div class="kpi-icon"><i class="bi bi-calendar-event"></i></div>
        <div>
            <div class="kpi-value">{{ $stats['eventos_total'] }}</div>
            <div class="kpi-label">Total Eventos</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#c9a227">
        <div class="kpi-icon"><i class="bi bi-people"></i></div>
        <div>
            <div class="kpi-value">{{ number_format($stats['participantes']) }}</div>
            <div class="kpi-label">Participantes Totales</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#f97316">
        <div class="kpi-icon"><i class="bi bi-star"></i></div>
        <div>
            <div class="kpi-value">{{ number_format($stats['puntos_otorgados']) }}</div>
            <div class="kpi-label">Puntos Otorgados</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#a855f7">
        <div class="kpi-icon"><i class="bi bi-gift"></i></div>
        <div>
            <div class="kpi-value">{{ number_format($stats['canjes']) }}</div>
            <div class="kpi-label">Premios Canjeados</div>
        </div>
    </div>
</div>

<!-- BOTTOM GRID -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <!-- Últimos Eventos -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-clock-history" style="color:var(--accent-gold);margin-right:8px"></i>Últimos Eventos</span>
            <a href="{{ route('eventos.index') }}" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Evento</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Cap.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ultimosEventos as $evento)
                    <tr>
                        <td>
                            <a href="{{ route('eventos.show', $evento) }}" style="color:var(--accent-gold);text-decoration:none;font-weight:500">
                                {{ Str::limit($evento->name_evento, 35) }}
                            </a>
                        </td>
                        <td style="color:var(--text-secondary)">{{ $evento->fecha_inicio->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $evento->badge_color }}">{{ $evento->estado }}</span>
                        </td>
                        <td style="color:var(--text-secondary)">{{ number_format($evento->capacidad) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Participantes por Evento -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-bar-chart" style="color:var(--accent-blue);margin-right:8px"></i>Participantes por Evento</span>
        </div>
        <div class="card-body" style="padding:16px 24px">
            @foreach($participantesPorEvento as $evento)
            @php
                $max = $participantesPorEvento->max('participantes_count') ?: 1;
                $pct = round(($evento->participantes_count / $max) * 100);
            @endphp
            <div style="margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px">
                    <span style="font-size:12px;color:var(--text-secondary);font-weight:500">{{ Str::limit($evento->name_evento, 30) }}</span>
                    <span style="font-size:12px;font-weight:700;color:var(--accent-gold)">{{ number_format($evento->participantes_count) }}</span>
                </div>
                <div style="height:6px;background:rgba(255,255,255,.06);border-radius:99px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,var(--accent-gold),var(--accent-blue));border-radius:99px;transition:width .6s ease"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection
