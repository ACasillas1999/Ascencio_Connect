@extends('layouts.app')

@section('title', 'Estadísticas del Evento - ' . $evento->name_evento)
@section('page-title', 'Estadísticas del Evento')

@section('content')
<div style="max-width:1400px; margin:0 auto; padding-bottom:40px;">
    
    <!-- ENCABEZADO SUPERIOR DEL EVENTO -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:16px; background:var(--bg-secondary); padding:20px 24px; border-radius:14px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                <span class="badge {{ $evento->badge_color }}" style="font-size:12px; padding:4px 10px;">{{ $evento->estado }}</span>
                <h1 style="font-size:22px; font-weight:800; color:var(--text-primary); margin:0; line-height:1.2;">
                    {{ $evento->name_evento }}
                </h1>
            </div>
            <div style="color:var(--text-secondary); font-size:13px; display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
                <span><i class="bi bi-geo-alt-fill" style="color:var(--accent-gold);"></i> {{ $evento->ubicacion }}</span>
                <span><i class="bi bi-calendar3" style="color:var(--accent-gold);"></i> {{ $evento->fecha_inicio->format('d/m/Y') }} al {{ $evento->fecha_fin->format('d/m/Y') }} ({{ $evento->duracion }})</span>
            </div>
        </div>

        <div style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <!-- BOTÓN DESCARGA EXCEL -->
            <a href="{{ route('eventos.estadisticas.export', $evento) }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:8px; font-weight:800; font-size:13px; padding:10px 18px; border-radius:8px; background:linear-gradient(135deg, #10b981 0%, #059669 100%); color:#ffffff; border:none; box-shadow:0 4px 14px rgba(16,185,129,0.3); text-decoration:none;">
                <i class="bi bi-file-earmark-excel-fill" style="font-size:16px;"></i> Descargar Reporte Excel
            </a>

            <!-- BOTÓN VOLVER -->
            <a href="{{ route('eventos.show', [$evento, 'active_tab' => 'tab-general']) }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:8px; font-weight:700; font-size:13px; padding:10px 18px; border-radius:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);">
                <i class="bi bi-arrow-left"></i> Volver al Evento
            </a>
        </div>
    </div>

    <!-- PESTAÑAS DINÁMICAS POR DÍA (GENERAL + DÍA 1, DÍA 2... HASTA 1 SEMANA) -->
    <div style="display:flex; overflow-x:auto; background:var(--bg-secondary); border-radius:12px; padding:6px; margin-bottom:24px; border:1px solid rgba(255,255,255,0.08); gap:8px; scrollbar-width:thin;">
        
        <!-- PESTAÑA RESUMEN GENERAL -->
        <button type="button" class="tab-est-btn active" onclick="switchEstadisticaTab(this, 'tab-est-general')" style="padding:10px 20px; font-weight:800; font-size:13px; border-radius:8px; border:none; background:linear-gradient(135deg, var(--accent-gold) 0%, #b8860b 100%); color:#000; cursor:pointer; white-space:nowrap; transition:all 0.2s ease; box-shadow:0 4px 12px rgba(212,175,55,0.25);">
            <i class="bi bi-bar-chart-line-fill" style="margin-right:6px;"></i> Resumen General (Todos los Días)
        </button>

        <!-- PESTAÑAS DINÁMICAS POR CADA DÍA DEL EVENTO -->
        @foreach($todasLasFechas as $index => $fStr)
            @php
                $fObj = \Carbon\Carbon::parse($fStr);
                $dInfo = $statsPorDia[$fStr] ?? null;
            @endphp
            <button type="button" class="tab-est-btn" onclick="switchEstadisticaTab(this, 'tab-est-dia-{{ $fStr }}')" style="padding:10px 18px; font-weight:600; font-size:13px; border-radius:8px; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.03); color:var(--text-secondary); cursor:pointer; white-space:nowrap; transition:all 0.2s ease; display:inline-flex; align-items:center;">
                <i class="bi bi-calendar-event-fill" style="margin-right:6px; color:var(--accent-gold);"></i> 
                Día {{ $index + 1 }}: {{ $fObj->locale('es')->isoFormat('D MMM') }}
                @if($dInfo)
                    <span class="badge" style="margin-left:8px; background:rgba(212,175,55,0.15); color:var(--accent-gold); border:1px solid rgba(212,175,55,0.3); font-size:11px; padding:2px 7px;">
                        {{ $dInfo['asistentes_unicos'] }} asist.
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    <!-- PESTAÑA 1: RESUMEN GENERAL Y GLOBAL COMPLETO -->
    <div id="tab-est-general" class="tab-pane" style="display:block;">
        
        <!-- KPIS PRINCIPALES DE ASISTENCIA GLOBAL -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:24px;">
            
            <!-- KPI 1: AUDIENCIA REAL ÚNICA DEL EVENTO -->
            <div style="background:var(--bg-secondary); border-radius:14px; padding:22px; border:1px solid rgba(34,197,94,0.3); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:#4ade80;">
                        <i class="bi bi-bullseye" style="margin-right:4px;"></i> Audiencia Única (Personas Distintas)
                    </span>
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.4); display:flex; align-items:center; justify-content:center; color:#4ade80;">
                        <i class="bi bi-person-check-fill" style="font-size:20px;"></i>
                    </div>
                </div>
                <div style="font-size:28px; font-weight:900; color:#4ade80; margin-bottom:4px;">
                    {{ number_format($totalAsistieron) }} <span style="font-size:14px; font-weight:600; color:var(--text-muted);">/ {{ number_format($totalInscritos) }} inscritos</span>
                </div>
                <div style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:99px; overflow:hidden; margin:10px 0 8px 0;">
                    <div style="width:{{ $porcentajeAsistencia }}%; height:100%; background:linear-gradient(90deg, #22c55e 0%, #10b981 100%); border-radius:99px;"></div>
                </div>
                <div style="font-size:11.5px; color:var(--text-muted); line-height:1.3;">
                    Personas únicas que asistieron (sin duplicar asistencias multisesión).
                </div>
            </div>

            <!-- KPI 2: ASISTENCIA DÍA 1 -->
            <div style="background:var(--bg-secondary); border-radius:14px; padding:22px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">
                        <i class="bi bi-calendar-check-fill" style="color:var(--accent-gold); margin-right:4px;"></i> Asistencia Día 1 {{ $dia1Str ? '(' . \Carbon\Carbon::parse($dia1Str)->format('d/m') . ')' : '' }}
                    </span>
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold);">
                        <i class="bi bi-calendar-check" style="font-size:20px;"></i>
                    </div>
                </div>
                <div style="font-size:28px; font-weight:900; color:var(--accent-gold); margin-bottom:4px;">
                    {{ number_format($countDia1) }} <span style="font-size:14px; font-weight:600; color:var(--text-muted);">asistentes</span>
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                    Del total inscritos: <strong>{{ $totalInscritos > 0 ? round(($countDia1 / $totalInscritos) * 100, 1) : 0 }}%</strong>
                </div>
            </div>

            <!-- KPI 3: ASISTENCIA DÍA 2 -->
            <div style="background:var(--bg-secondary); border-radius:14px; padding:22px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">
                        <i class="bi bi-calendar2-check-fill" style="color:#38bdf8; margin-right:4px;"></i> Asistencia Día 2 {{ $dia2Str ? '(' . \Carbon\Carbon::parse($dia2Str)->format('d/m') . ')' : '' }}
                    </span>
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); display:flex; align-items:center; justify-content:center; color:#38bdf8;">
                        <i class="bi bi-calendar2-check" style="font-size:20px;"></i>
                    </div>
                </div>
                <div style="font-size:28px; font-weight:900; color:#38bdf8; margin-bottom:4px;">
                    {{ number_format($countDia2) }} <span style="font-size:14px; font-weight:600; color:var(--text-muted);">asistentes</span>
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                    Del total inscritos: <strong>{{ $totalInscritos > 0 ? round(($countDia2 / $totalInscritos) * 100, 1) : 0 }}%</strong>
                </div>
            </div>

            <!-- KPI 4: ASISTIERON AMBOS DÍAS -->
            <div style="background:var(--bg-secondary); border-radius:14px; padding:22px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">
                        <i class="bi bi-people-fill" style="color:#c084fc; margin-right:4px;"></i> Asistieron Ambos Días
                    </span>
                    <div style="width:40px; height:40px; border-radius:10px; background:rgba(168,85,247,0.12); border:1px solid rgba(168,85,247,0.3); display:flex; align-items:center; justify-content:center; color:#c084fc;">
                        <i class="bi bi-award-fill" style="font-size:20px;"></i>
                    </div>
                </div>
                <div style="font-size:28px; font-weight:900; color:#c084fc; margin-bottom:4px;">
                    {{ number_format($ambosDias) }} <span style="font-size:14px; font-weight:600; color:var(--text-muted);">personas</span>
                </div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:8px;">
                    Fidelidad total del evento: <strong>{{ $totalAsistieron > 0 ? round(($ambosDias / $totalAsistieron) * 100, 1) : 0 }}%</strong>
                </div>
            </div>

        </div>

        <!-- PANEL 1 GLOBAL: GRÁFICO + TABLA DETALLADA DE AFLUENCIA HORARIA -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-graph-up-arrow" style="color:var(--accent-gold);"></i> 1. Afluencia Horaria de Asistencia (Gráfico + Tabla Desglosada)
                </span>
                <span style="font-size:12px; color:var(--text-muted);">Curva Horaria Acumulada</span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div style="position:relative; height:240px; margin-bottom:20px;">
                    <canvas id="chartAfluenciaGlobal"></canvas>
                </div>

                <!-- TABLA DESCRIPTIVA DE AFLUENCIA HORARIA GLOBAL -->
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary);">Franja Horaria</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistentes Únicos</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Total Escaneos</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">% del Pico Máximo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxGlobalHora = $asistenciaPorHora->max('asistentes_unicos') ?: 1;
                            @endphp
                            @forelse($asistenciaPorHora as $ah)
                            @php
                                $pctHora = round(($ah->asistentes_unicos / $maxGlobalHora) * 100, 1);
                            @endphp
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                <td style="padding:10px 16px; font-weight:700; color:var(--accent-gold); font-size:13px;"><i class="bi bi-clock-fill" style="margin-right:6px;"></i> {{ $ah->hora_raw }}:00 hrs</td>
                                <td style="padding:10px 16px; text-align:center; font-weight:800; font-size:13.5px; color:var(--text-primary);">{{ number_format($ah->asistentes_unicos) }} pers.</td>
                                <td style="padding:10px 16px; text-align:center; font-size:13px; color:var(--text-secondary);">{{ number_format($ah->total_escaneos) }} escaneos</td>
                                <td style="padding:10px 16px; text-align:right; font-weight:700; color:#38bdf8;">{{ $pctHora }}%</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center; padding:20px; color:var(--text-muted);">Sin datos de horario.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- PANEL 2 Y 3: CONCURRENCIA POR SALÓN Y PUESTOS (GRÁFICO + TABLAS) -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
            
            <!-- PANEL 2: SALONES (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-pie-chart-fill" style="color:var(--accent-gold);"></i> 2. Asistencia por Salón / Aula
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:200px; margin-bottom:16px;">
                        <canvas id="chartSalonesGlobal"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Salón / Aula</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistencias</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">% Ocupación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalSalAsist = $salonesGlobal->sum('total_asistencias') ?: 1; @endphp
                                @forelse($salonesGlobal as $sg)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-door-open-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $sg->salon }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:center; font-weight:800; color:#38bdf8;">{{ number_format($sg->total_asistencias) }}</td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:700; color:var(--accent-gold);">{{ round(($sg->total_asistencias / $totalSalAsist) * 100, 1) }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de salón.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PANEL 3: PUESTOS / ROLES (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-person-workspace" style="color:var(--accent-gold);"></i> 3. Puestos Más Frecuentes
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:200px; margin-bottom:16px;">
                        <canvas id="chartPuestosGlobal"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Puesto / Rol</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistentes Únicos</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">% Audiencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asistenciaPorPuesto as $pu)
                                @php $pctPu = $totalAsistieron > 0 ? round(($pu->total_asistentes / $totalAsistieron) * 100, 1) : 0; @endphp
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-briefcase-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $pu->puesto }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:center; font-weight:800; color:#4ade80;">{{ number_format($pu->total_asistentes) }}</td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:700; color:#38bdf8;">{{ $pctPu }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de puestos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- PANEL 4 Y 5: SUCURSALES Y CANJES PREMIOS -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
            
            <!-- PANEL 4: SUCURSALES (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-building-fill" style="color:var(--accent-gold);"></i> 4. Asistencia por Sucursal
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:180px; margin-bottom:16px;">
                        <canvas id="chartSucursalesGlobal"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Sucursal</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistentes</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">% Aforo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asistenciaPorSucursal as $suc)
                                @php $pctS = $totalAsistieron > 0 ? round(($suc->total_asistentes / $totalAsistieron) * 100, 1) : 0; @endphp
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-geo-alt-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $suc->sucursal ?: 'Sin Sucursal' }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:center; font-weight:800; color:var(--accent-gold);">{{ number_format($suc->total_asistentes) }}</td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:700; color:#4ade80;">{{ $pctS }}%</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de sucursal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PANEL 5: CANJES Y PREMIOS (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-gift-fill" style="color:var(--accent-gold);"></i> 5. Canjes de Premios y Tómbola
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:180px; margin-bottom:16px;">
                        <canvas id="chartPremiosGlobal"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Premio</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Canjeados</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Stock Restante</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topPremiosCanjeados as $pr)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-award-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $pr->premio }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:center; font-weight:800; color:#4ade80;">{{ number_format($pr->total_canjeados) }} canjeados</td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:700; color:var(--accent-gold);">{{ number_format($pr->stock_actual) }} piezas</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" style="text-align:center; padding:16px; color:var(--text-muted);">Sin canjes registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- SECCIÓN: RANKING DE REGISTRADORES / VENDEDORES (CONVERSIÓN DETALLADA) -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-person-badge-fill" style="color:var(--accent-gold);"></i> 6. Conversión y Registro por Vendedor / Registrador
                </span>
                <span style="font-size:12px; color:var(--text-muted);">Efectividad Real de Asistencia</span>
            </div>
            <div class="table-wrapper" style="max-height:350px; overflow-y:auto;">
                <table>
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary);">Vendedor / Registrador</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Total Inscritos</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistieron Realmente</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Ausentes</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Efectividad Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rankingVendedores as $index => $v)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <td style="padding:14px 20px; font-weight:700; font-size:13.5px; color:var(--text-primary);">
                                <span style="color:var(--accent-gold); font-weight:900; margin-right:8px;">#{{ $index + 1 }}</span>
                                {{ $v->vendedor_nombre }}
                            </td>
                            <td style="padding:14px 20px; text-align:center; font-size:14px; font-weight:800; color:var(--text-primary);">
                                {{ number_format($v->total_registrados) }} pers.
                            </td>
                            <td style="padding:14px 20px; text-align:center;">
                                <span class="badge" style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-size:13px; padding:5px 12px; font-weight:800;">
                                    <i class="bi bi-person-check-fill" style="margin-right:4px;"></i> {{ number_format($v->total_asistieron) }} fueron
                                </span>
                            </td>
                            <td style="padding:14px 20px; text-align:center; font-size:13px; color:var(--text-secondary);">
                                {{ number_format($v->ausentes) }} ausentes
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                                    <div style="width:80px; height:6px; background:rgba(255,255,255,0.1); border-radius:99px; overflow:hidden;">
                                        <div style="width:{{ $v->pct_asistencia }}%; height:100%; background:{{ $v->pct_asistencia >= 75 ? '#22c55e' : ($v->pct_asistencia >= 40 ? '#f97316' : '#ef4444') }}; border-radius:99px;"></div>
                                    </div>
                                    <strong style="font-size:13px; color:{{ $v->pct_asistencia >= 75 ? '#4ade80' : ($v->pct_asistencia >= 40 ? '#fb923c' : '#f87171') }};">{{ $v->pct_asistencia }}%</strong>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">No hay información de registradores o vendedores.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN: RANKING DE PROVEEDORES QUE REPARTIERON MÁS PUNTOS -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-star-fill" style="color:var(--accent-gold);"></i> 7. Proveedores que Repartieron Más Puntos
                </span>
                <span style="font-size:12px; color:var(--text-muted);">Ranking por Total de Puntos Otorgados</span>
            </div>
            <div class="table-wrapper" style="max-height:350px; overflow-y:auto;">
                <table>
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary);">Proveedor</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Participantes Atendidos</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Operaciones de Puntos</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Puntos Repartidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProveedoresPuntos as $index => $p)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <td style="padding:14px 20px; font-weight:700; font-size:13.5px; color:var(--text-primary);">
                                <span style="color:var(--accent-gold); font-weight:900; margin-right:8px;">#{{ $index + 1 }}</span>
                                {{ $p->proveedor }}
                            </td>
                            <td style="padding:14px 20px; text-align:center; font-size:13px; font-weight:600; color:var(--text-primary);">
                                <i class="bi bi-person-check" style="margin-right:4px;"></i> {{ number_format($p->participantes_atendidos) }} personas
                            </td>
                            <td style="padding:14px 20px; text-align:center; font-size:13px; color:var(--text-secondary);">
                                {{ number_format($p->num_transacciones) }} transacciones
                            </td>
                            <td style="padding:14px 20px; text-align:right;">
                                <span class="badge badge-gold" style="font-size:14px; padding:6px 14px; font-weight:800;">
                                    <i class="bi bi-star-fill" style="margin-right:4px;"></i> {{ number_format($p->total_puntos) }} pts
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted);">Aún no se han otorgado puntos por proveedores en este evento.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- PESTAÑAS INDIVIDUALES Y DINÁMICAS POR CADA DÍA DEL EVENTO (CON GRÁFICO Y SU TABLA DETALLADA) -->
    @foreach($statsPorDia as $fStr => $dStats)
    @php
        $fKey = str_replace('-', '_', $fStr);
    @endphp
    <div id="tab-est-dia-{{ $fStr }}" class="tab-pane" style="display:none;">
        
        <!-- HEADER DEL DÍA SELECCIONADO CON KPIS COMPLETOS -->
        <div style="background:var(--bg-secondary); border-radius:14px; padding:20px 24px; border:1px solid rgba(255,255,255,0.08); margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div>
                <div style="font-size:11.5px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:var(--accent-gold);">
                    Día {{ $dStats['numero_dia'] }} del Evento
                </div>
                <h2 style="font-size:20px; font-weight:900; color:var(--text-primary); margin:4px 0 0 0; text-transform:capitalize;">
                    <i class="bi bi-calendar-event-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $dStats['fecha_formateada'] }}
                </h2>
            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div style="background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); border-radius:10px; padding:10px 16px; text-align:center;">
                    <div style="font-size:10px; color:var(--text-muted); font-weight:800; text-transform:uppercase;">ASISTENTES ÚNICOS DÍA</div>
                    <div style="font-size:20px; font-weight:900; color:#4ade80; margin-top:2px;">{{ number_format($dStats['asistentes_unicos']) }} pers.</div>
                </div>

                <div style="background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.3); border-radius:10px; padding:10px 16px; text-align:center;">
                    <div style="font-size:10px; color:var(--text-muted); font-weight:800; text-transform:uppercase;">PUNTOS OTORGADOS</div>
                    <div style="font-size:20px; font-weight:900; color:var(--accent-gold); margin-top:2px;">
                        <i class="bi bi-star-fill" style="margin-right:2px;"></i> {{ number_format($dStats['total_puntos_dia']) }} pts
                    </div>
                </div>

                <div style="background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); border-radius:10px; padding:10px 16px; text-align:center;">
                    <div style="font-size:10px; color:var(--text-muted); font-weight:800; text-transform:uppercase;">SESIONES / ACTIVIDADES</div>
                    <div style="font-size:20px; font-weight:900; color:#38bdf8; margin-top:2px;">{{ number_format($dStats['actividades']->count()) }}</div>
                </div>
            </div>
        </div>

        <!-- 1. AFLUENCIA HORARIA DEL DÍA: GRÁFICO + TABLA COMPLETA -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header" style="padding:16px 20px;">
                <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-graph-up" style="color:var(--accent-gold);"></i> 1. Afluencia Horaria del Día (Gráfico + Tabla Descriptiva)
                </span>
            </div>
            <div class="card-body" style="padding:20px;">
                <div style="position:relative; height:220px; margin-bottom:16px;">
                    <canvas id="chartAfluencia_{{ $fKey }}"></canvas>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary);">Horario</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistentes Únicos</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Total Escaneos</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Concurrencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dStats['afluencia_horaria'] as $ahD)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                <td style="padding:10px 16px; font-weight:700; color:var(--accent-gold); font-size:13px;"><i class="bi bi-clock-fill" style="margin-right:6px;"></i> {{ $ahD->hora_raw }}:00 hrs</td>
                                <td style="padding:10px 16px; text-align:center; font-weight:800; color:var(--text-primary);">{{ number_format($ahD->asistentes_unicos) }} pers.</td>
                                <td style="padding:10px 16px; text-align:center; font-size:13px; color:var(--text-secondary);">{{ number_format($ahD->total_escaneos) }} escaneos</td>
                                <td style="padding:10px 16px; text-align:right;"><span class="badge badge-gold" style="padding:4px 10px;">{{ $ahD->asistentes_unicos }} activos</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" style="text-align:center; padding:20px; color:var(--text-muted);">Sin registros en este día.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- FILA DE 2 GRÁFICOS CON TABLAS (SALONES Y PUESTOS DEL DÍA) -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
            
            <!-- 2. SALONES DEL DÍA (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-pie-chart-fill" style="color:var(--accent-gold);"></i> 2. Asistencia por Salón el Día {{ $dStats['numero_dia'] }}
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:200px; margin-bottom:16px;">
                        <canvas id="chartSalones_{{ $fKey }}"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Salón / Aula</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Asistencias Totales</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dStats['salones'] as $sD)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-door-open-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $sD->salon }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:800; color:#38bdf8;">{{ number_format($sD->total_asistencias) }} pers.</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de salón.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. PUESTOS DEL DÍA (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-person-workspace" style="color:var(--accent-gold);"></i> 3. Puestos y Cargos en el Día {{ $dStats['numero_dia'] }}
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:200px; margin-bottom:16px;">
                        <canvas id="chartPuestos_{{ $fKey }}"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:220px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Puesto / Rol</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Asistentes Únicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dStats['puestos'] as $pD)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-briefcase-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $pD->puesto }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:800; color:#4ade80;">{{ number_format($pD->total_asistentes) }} pers.</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de puesto.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- FILA DE 2 GRÁFICOS Y TABLAS (SUCURSALES Y PROVEEDORES DEL DÍA) -->
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:24px;">
            
            <!-- 4. SUCURSALES DEL DÍA (GRÁFICO + TABLA) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-building-fill" style="color:var(--accent-gold);"></i> 4. Asistencia por Sucursal en el Día
                    </span>
                </div>
                <div class="card-body" style="padding:20px;">
                    <div style="position:relative; height:180px; margin-bottom:16px;">
                        <canvas id="chartSucursales_{{ $fKey }}"></canvas>
                    </div>
                    <div class="table-wrapper" style="max-height:200px; overflow-y:auto;">
                        <table>
                            <thead>
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Sucursal</th>
                                    <th style="padding:8px 12px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Asistentes Únicos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dStats['sucursales'] as $sucD)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                    <td style="padding:8px 12px; font-weight:700; font-size:12.5px; color:var(--text-primary);">
                                        <i class="bi bi-geo-alt-fill" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $sucD->sucursal ?: 'Sin Sucursal' }}
                                    </td>
                                    <td style="padding:8px 12px; text-align:right; font-weight:800; color:var(--accent-gold);">{{ number_format($sucD->total_asistentes) }} pers.</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" style="text-align:center; padding:16px; color:var(--text-muted);">Sin datos de sucursal.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. PROVEEDORES DEL DÍA (TABLA DETALLADA CON PARTICIPANTES) -->
            <div class="card" style="margin-bottom:0;">
                <div class="card-header" style="padding:16px 20px;">
                    <span class="card-title" style="font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-star-fill" style="color:var(--accent-gold);"></i> 5. Puntos Otorgados por Proveedor
                    </span>
                </div>
                <div class="table-wrapper" style="max-height:350px; overflow-y:auto;">
                    <table>
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary);">Proveedor</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Personas Atendidas</th>
                                <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Puntos Otorgados</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dStats['puntos_proveedores'] as $pDia)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                                <td style="padding:10px 16px; font-weight:700; font-size:13px; color:var(--text-primary);">
                                    <i class="bi bi-shop" style="color:var(--accent-gold); margin-right:6px;"></i> {{ $pDia->proveedor }}
                                </td>
                                <td style="padding:10px 16px; text-align:center; font-size:12.5px; color:var(--text-secondary);">
                                    <i class="bi bi-person-check-fill" style="margin-right:4px;"></i> {{ number_format($pDia->participantes_atendidos) }} pers.
                                </td>
                                <td style="padding:10px 16px; text-align:right;">
                                    <span class="badge badge-gold" style="font-size:12px; padding:4px 10px;">
                                        <i class="bi bi-star-fill" style="margin-right:2px;"></i> {{ number_format($pDia->total_puntos) }} pts
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="text-align:center; padding:24px; color:var(--text-muted);">Sin puntos otorgados en este día.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- 6. REGISTRADORES Y VENDEDORES CONVERSIÓN EN ESTE DÍA -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header" style="padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:14.5px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-person-badge-fill" style="color:var(--accent-gold);"></i> 6. Conversión de Registradores / Vendedores en este Día
                </span>
                <span style="font-size:12px; color:var(--text-muted);">Inscritos Registrados vs Asistieron Realmente el Día</span>
            </div>
            <div class="table-wrapper" style="max-height:300px; overflow-y:auto;">
                <table>
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary);">Registrador / Vendedor</th>
                            <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Total Inscritos</th>
                            <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Asistieron este Día</th>
                            <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:center;">Ausentes este Día</th>
                            <th style="padding:10px 16px; font-size:11.5px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Efectividad en el Día</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dStats['vendedores'] as $vD)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <td style="padding:12px 16px; font-weight:700; font-size:13px; color:var(--text-primary);">
                                <i class="bi bi-person-circle" style="color:var(--accent-gold); margin-right:6px;"></i>
                                {{ $vD->vendedor }}
                            </td>
                            <td style="padding:12px 16px; text-align:center; font-weight:800; font-size:13.5px; color:var(--text-primary);">
                                {{ number_format($vD->total_registrados) }} pers.
                            </td>
                            <td style="padding:12px 16px; text-align:center;">
                                <span class="badge" style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-size:12.5px; padding:4px 10px; font-weight:800;">
                                    <i class="bi bi-person-check-fill" style="margin-right:4px;"></i> {{ number_format($vD->total_asistieron) }} fueron
                                </span>
                            </td>
                            <td style="padding:12px 16px; text-align:center; font-size:12.5px; color:var(--text-secondary);">
                                {{ number_format($vD->ausentes) }} ausentes
                            </td>
                            <td style="padding:12px 16px; text-align:right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:8px;">
                                    <div style="width:70px; height:6px; background:rgba(255,255,255,0.1); border-radius:99px; overflow:hidden;">
                                        <div style="width:{{ $vD->pct_asistencia }}%; height:100%; background:{{ $vD->pct_asistencia >= 75 ? '#22c55e' : ($vD->pct_asistencia >= 40 ? '#f97316' : '#ef4444') }}; border-radius:99px;"></div>
                                    </div>
                                    <strong style="font-size:12.5px; color:{{ $vD->pct_asistencia >= 75 ? '#4ade80' : ($vD->pct_asistencia >= 40 ? '#fb923c' : '#f87171') }};">{{ $vD->pct_asistencia }}%</strong>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center; padding:24px; color:var(--text-muted);">Sin datos de vendedores en este día.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 7. CONCURRENCIA Y AFORO DETALLADO POR ACTIVIDAD EN EL DÍA -->
        <div class="card">
            <div class="card-header" style="padding:16px 20px;">
                <span class="card-title" style="font-size:15px; font-weight:700; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-calendar-check" style="color:var(--accent-gold);"></i> 7. Concurrencia y Aforo por Actividad / Sesión en el Día {{ $dStats['numero_dia'] }}
                </span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary);">Actividad</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary);">Horario</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary);">Salón</th>
                            <th style="padding:12px 20px; font-size:12px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Aforo Asistente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dStats['actividades'] as $actD)
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                            <td style="padding:12px 20px; font-weight:700; font-size:13.5px; color:var(--text-primary);">{{ $actD->actividad }}</td>
                            <td style="padding:12px 20px; font-size:13px; color:var(--accent-gold); font-weight:600;"><i class="bi bi-clock-fill" style="margin-right:4px;"></i> {{ $actD->horario }}</td>
                            <td style="padding:12px 20px; font-size:13px; color:var(--text-secondary);">{{ $actD->salon ?: 'Sin Salón' }}</td>
                            <td style="padding:12px 20px; text-align:right;">
                                <span class="badge" style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-size:13px; padding:4px 12px; font-weight:800;">
                                    <i class="bi bi-person-check-fill" style="margin-right:4px;"></i> {{ number_format($actD->total_asistieron) }} asistieron
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center; padding:32px; color:var(--text-muted);">Sin actividades agendadas en esta fecha.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endforeach

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchEstadisticaTab(btn, tabId) {
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-est-btn').forEach(el => {
            el.classList.remove('active');
            el.style.background = 'rgba(255,255,255,0.03)';
            el.style.color = 'var(--text-secondary)';
            el.style.border = '1px solid rgba(255,255,255,0.08)';
            el.style.boxShadow = 'none';
            el.style.fontWeight = '600';
        });

        const target = document.getElementById(tabId);
        if (target) {
            target.style.display = 'block';
        }

        btn.classList.add('active');
        btn.style.background = 'linear-gradient(135deg, var(--accent-gold) 0%, #b8860b 100%)';
        btn.style.color = '#000000';
        btn.style.border = 'none';
        btn.style.boxShadow = '0 4px 12px rgba(212,175,55,0.25)';
        btn.style.fontWeight = '800';

        window.dispatchEvent(new Event('resize'));
    }

    document.addEventListener('DOMContentLoaded', function() {
        const palette = ['#d4af37', '#38bdf8', '#4ade80', '#c084fc', '#f97316', '#ec4899', '#6366f1', '#eab308'];

        // CHARTS PESTAÑA RESUMEN GENERAL GLOBAL
        const ctxGlobal = document.getElementById('chartAfluenciaGlobal');
        if (ctxGlobal) {
            new Chart(ctxGlobal, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($asistenciaPorHora->pluck('hora_raw')->map(fn($h) => $h.':00 hrs')) !!},
                    datasets: [{
                        label: 'Asistentes Únicos',
                        data: {!! json_encode($asistenciaPorHora->pluck('asistentes_unicos')) !!},
                        backgroundColor: 'rgba(212,175,55,0.4)',
                        borderColor: '#d4af37',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { labels: { color: '#f8fafc', font: { weight: 'bold' } } } }
                }
            });
        }

        const ctxSalonesG = document.getElementById('chartSalonesGlobal');
        if (ctxSalonesG) {
            new Chart(ctxSalonesG, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($salonesGlobal->pluck('salon')) !!},
                    datasets: [{
                        data: {!! json_encode($salonesGlobal->pluck('total_asistencias')) !!},
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: '#f8fafc', font: { size: 10 } } } }
                }
            });
        }

        const ctxPuestosG = document.getElementById('chartPuestosGlobal');
        if (ctxPuestosG) {
            new Chart(ctxPuestosG, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($asistenciaPorPuesto->pluck('puesto')) !!},
                    datasets: [{
                        label: 'Asistentes Únicos',
                        data: {!! json_encode($asistenciaPorPuesto->pluck('total_asistentes')) !!},
                        backgroundColor: 'rgba(74, 222, 128, 0.4)',
                        borderColor: '#4ade80',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        y: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const ctxSucursalesG = document.getElementById('chartSucursalesGlobal');
        if (ctxSucursalesG) {
            new Chart(ctxSucursalesG, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($asistenciaPorSucursal->pluck('sucursal')->map(fn($s) => $s ?: 'Sin Sucursal')) !!},
                    datasets: [{
                        label: 'Asistentes',
                        data: {!! json_encode($asistenciaPorSucursal->pluck('total_asistentes')) !!},
                        backgroundColor: 'rgba(212, 175, 55, 0.4)',
                        borderColor: '#d4af37',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const ctxPremiosG = document.getElementById('chartPremiosGlobal');
        if (ctxPremiosG) {
            new Chart(ctxPremiosG, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($topPremiosCanjeados->pluck('premio')) !!},
                    datasets: [{
                        data: {!! json_encode($topPremiosCanjeados->pluck('total_canjeados')) !!},
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: '#f8fafc', font: { size: 10 } } } }
                }
            });
        }

        // CHARTS INDIVIDUALES PARA CADA DÍA
        @foreach($statsPorDia as $fStr => $dStats)
        @php
            $fKey = str_replace('-', '_', $fStr);
        @endphp

        const ctxAfluencia_{{ $fKey }} = document.getElementById('chartAfluencia_{{ $fKey }}');
        if (ctxAfluencia_{{ $fKey }}) {
            new Chart(ctxAfluencia_{{ $fKey }}, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dStats['afluencia_horaria']->pluck('hora_raw')->map(fn($h) => $h.':00 hrs')) !!},
                    datasets: [{
                        label: 'Asistentes el Día',
                        data: {!! json_encode($dStats['afluencia_horaria']->pluck('asistentes_unicos')) !!},
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56,189,248,0.15)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { labels: { color: '#f8fafc', font: { weight: 'bold' } } } }
                }
            });
        }

        const ctxSalones_{{ $fKey }} = document.getElementById('chartSalones_{{ $fKey }}');
        if (ctxSalones_{{ $fKey }}) {
            new Chart(ctxSalones_{{ $fKey }}, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($dStats['salones']->pluck('salon')) !!},
                    datasets: [{
                        data: {!! json_encode($dStats['salones']->pluck('total_asistencias')) !!},
                        backgroundColor: palette
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'right', labels: { color: '#f8fafc', font: { size: 11 } } } }
                }
            });
        }

        const ctxPuestos_{{ $fKey }} = document.getElementById('chartPuestos_{{ $fKey }}');
        if (ctxPuestos_{{ $fKey }}) {
            new Chart(ctxPuestos_{{ $fKey }}, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dStats['puestos']->pluck('puesto')) !!},
                    datasets: [{
                        label: 'Asistentes Únicos',
                        data: {!! json_encode($dStats['puestos']->pluck('total_asistentes')) !!},
                        backgroundColor: 'rgba(74, 222, 128, 0.4)',
                        borderColor: '#4ade80',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        y: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        const ctxSucursales_{{ $fKey }} = document.getElementById('chartSucursales_{{ $fKey }}');
        if (ctxSucursales_{{ $fKey }}) {
            new Chart(ctxSucursales_{{ $fKey }}, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dStats['sucursales']->pluck('sucursal')->map(fn($s) => $s ?: 'Sin Sucursal')) !!},
                    datasets: [{
                        label: 'Asistentes',
                        data: {!! json_encode($dStats['sucursales']->pluck('total_asistentes')) !!},
                        backgroundColor: 'rgba(212, 175, 55, 0.4)',
                        borderColor: '#d4af37',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                        x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        @endforeach
    });
</script>
@endpush
@endsection
