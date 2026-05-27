@extends('layouts.app')

@section('title', 'Reporte de Canjes — ' . $evento->name_evento)
@section('page-title', 'Reporte de Canjes')

@section('topbar-actions')
    <a href="{{ route('eventos.canjes.index', $evento) }}" class="btn btn-secondary" style="margin-right:8px;">
        <i class="bi bi-gift"></i> Ir a Canjear
    </a>
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Evento
    </a>
@endsection

@section('content')

<!-- KPIs -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="kpi-card" style="--kpi-color:#d4af37">
        <div class="kpi-icon"><i class="bi bi-gift"></i></div>
        <div>
            <div class="kpi-value">{{ $premios->count() }}</div>
            <div class="kpi-label">Premios del Evento</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#10b981">
        <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
        <div>
            <div class="kpi-value">{{ number_format($totalCanjes) }}</div>
            <div class="kpi-label">Canjes Realizados</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#3b82f6">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div>
            <div class="kpi-value">{{ number_format($totalPremiosEntregados) }}</div>
            <div class="kpi-label">Premios Entregados</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#8b5cf6">
        <div class="kpi-icon"><i class="bi bi-people"></i></div>
        <div>
            <div class="kpi-value">{{ $topParticipantes->count() }}</div>
            <div class="kpi-label">Participantes con Canjes</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">

    <!-- RESUMEN POR PREMIO -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-trophy-fill" style="color:var(--accent-gold); margin-right:8px;"></i>Resumen por Premio</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Premio</th>
                        <th style="text-align:center;">Costo (pts)</th>
                        <th style="text-align:center;">Canjeados</th>
                        <th style="text-align:center;">Stock Restante</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resumenPorPremio as $rp)
                    <tr>
                        <td style="font-weight:600;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <i class="bi bi-gift-fill" style="color:var(--accent-gold);"></i>
                                {{ $rp->nombre }}
                            </div>
                        </td>
                        <td style="text-align:center; color:var(--accent-gold); font-weight:600;">{{ number_format($rp->puntos) }}</td>
                        <td style="text-align:center; font-weight:700;">{{ number_format($rp->total_canjeados) }}</td>
                        <td style="text-align:center;">
                            <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700;
                                {{ $rp->stock > 0 ? 'background:rgba(16,185,129,0.12); color:#10b981;' : 'background:rgba(239,68,68,0.12); color:#ef4444;' }}">
                                {{ $rp->stock > 0 ? $rp->stock : 'AGOTADO' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; padding:24px; color:var(--text-muted);">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TOP PARTICIPANTES -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-bar-chart-fill" style="color:var(--accent-gold); margin-right:8px;"></i>Top Participantes</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Participante</th>
                        <th style="text-align:center;">Premios</th>
                        <th style="text-align:center;">Puntos Usados</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topParticipantes as $i => $tp)
                    <tr>
                        <td>
                            @if($i < 3)
                                <span style="font-size:16px;">{{ ['🥇','🥈','🥉'][$i] }}</span>
                            @else
                                <span style="color:var(--text-muted); font-weight:600;">{{ $i + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <strong style="color:var(--text-primary);">{{ $tp->nombre }}</strong>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $tp->sucursal ?: '—' }}</div>
                        </td>
                        <td style="text-align:center; font-weight:700;">{{ number_format($tp->total_premios) }}</td>
                        <td style="text-align:center;">
                            <span style="color:#ef4444; font-weight:700;">{{ number_format($tp->puntos_gastados) }} pts</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; padding:24px; color:var(--text-muted);">Sin datos</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- HISTORIAL COMPLETO -->
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-clock-history" style="color:var(--accent-gold); margin-right:8px;"></i>Historial Completo de Canjes</span>
        <span class="badge badge-secondary" style="margin-left:8px;">{{ $canjes->total() }} registros</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Participante</th>
                    <th>Sucursal</th>
                    <th>Premio</th>
                    <th style="text-align:center;">Cantidad</th>
                    <th style="text-align:center;">Puntos</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($canjes as $canje)
                <tr>
                    <td style="font-weight:500;">
                        {{ $canje->participante->Nombre ?? 'Eliminado' }}
                        <div style="font-size:11px; color:var(--text-muted);">ID: {{ $canje->ID_Participante }}</div>
                    </td>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $canje->participante->Sucursal ?? '—' }}</td>
                    <td>
                        <span class="badge badge-gold">{{ $canje->premio->NombrePremio ?? 'Premio eliminado' }}</span>
                    </td>
                    <td style="text-align:center; font-weight:700;">{{ $canje->Cantidad }}</td>
                    <td style="text-align:center; color:#ef4444; font-weight:600;">
                        -{{ $canje->premio ? number_format($canje->premio->PuntosNecesarios * $canje->Cantidad) : '?' }}
                    </td>
                    <td style="font-size:12px; color:var(--text-muted);">
                        {{ $canje->Fecha ? $canje->Fecha->format('d/m/Y H:i') : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-muted);">No hay canjes registrados aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($canjes->hasPages())
    <div style="padding:12px 24px; border-top:1px solid var(--border-subtle);">
        {{ $canjes->links() }}
    </div>
    @endif
</div>

@endsection
