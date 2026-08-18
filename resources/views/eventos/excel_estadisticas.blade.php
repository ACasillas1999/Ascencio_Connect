<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Estadístico - {{ $evento->name_evento }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1e293b; margin: 0; padding: 0; }
        table.master-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        
        .main-title { background-color: #1e1b4b; color: #ffffff; font-size: 16px; font-weight: bold; padding: 14px; text-align: left; }
        .meta-label { background-color: #f1f5f9; color: #475569; font-weight: bold; padding: 8px; }
        .meta-val { background-color: #ffffff; color: #0f172a; font-weight: bold; padding: 8px; }
        
        .section-header { background-color: #0f172a; color: #f8fafc; font-size: 12px; font-weight: bold; padding: 10px; border: 1px solid #0f172a; }
        .day-header { background-color: #d4af37; color: #000000; font-size: 12px; font-weight: bold; padding: 10px; border: 1px solid #b8860b; }
        .sub-header { background-color: #334155; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px; border: 1px solid #334155; }
        
        th { background-color: #e2e8f0; color: #0f172a; font-weight: bold; padding: 8px; border: 1px solid #cbd5e1; font-size: 11px; text-align: left; }
        td { padding: 8px; border: 1px solid #cbd5e1; font-size: 11px; vertical-align: middle; }
        
        tr.even td { background-color: #f8fafc; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .c-green { color: #15803d; font-weight: bold; }
        .c-gold { color: #b8860b; font-weight: bold; }
        .c-blue { color: #0284c7; font-weight: bold; }
        .c-red { color: #b91c1c; font-weight: bold; }
        
        .spacer-row td { border: none !important; background: #ffffff !important; height: 16px; }
    </style>
</head>
<body>

<table class="master-table">
    <colgroup>
        <col style="width: 7%;">
        <col style="width: 35%;">
        <col style="width: 14.5%;">
        <col style="width: 14.5%;">
        <col style="width: 14.5%;">
        <col style="width: 14.5%;">
    </colgroup>

    <!-- ENCABEZADO REPORTE -->
    <tr>
        <td colspan="6" class="main-title">REPORTE INTEGRAL DE ESTADÍSTICAS Y ASISTENCIA DEL EVENTO</td>
    </tr>
    <tr>
        <td class="meta-label">Evento:</td>
        <td colspan="2" class="meta-val">{{ $evento->name_evento }}</td>
        <td class="meta-label">Estado:</td>
        <td colspan="2" class="meta-val" style="color:#b8860b;">{{ $evento->estado }}</td>
    </tr>
    <tr>
        <td class="meta-label">Ubicación:</td>
        <td colspan="2" class="meta-val">{{ $evento->ubicacion }}</td>
        <td class="meta-label">Fechas:</td>
        <td colspan="2" class="meta-val">{{ $evento->fecha_inicio->format('d/m/Y') }} al {{ $evento->fecha_fin->format('d/m/Y') }} ({{ $evento->duracion }})</td>
    </tr>
    <tr>
        <td class="meta-label">Generado:</td>
        <td colspan="5" class="meta-val">{{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }} hrs</td>
    </tr>

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 1: RESUMEN EJECUTIVO Y AUDIENCIA ÚNICA GLOBAL -->
    <tr>
        <td colspan="6" class="section-header">1. RESUMEN EJECUTIVO Y AUDIENCIA ÚNICA GLOBAL</td>
    </tr>
    <tr>
        <th colspan="2">Métrica de Audiencia</th>
        <th class="text-center">Total Registrados</th>
        <th class="text-center">Asistieron Realmente</th>
        <th class="text-center">Ausentes</th>
        <th class="text-right">Efectividad Asistencia (%)</th>
    </tr>
    <tr>
        <td colspan="2" class="font-bold c-green">Audiencia Única (Personas Distintas)</td>
        <td class="text-center font-bold">{{ number_format($totalInscritos) }}</td>
        <td class="text-center font-bold c-green">{{ number_format($totalAsistieron) }}</td>
        <td class="text-center font-bold c-red">{{ number_format($totalSinAsistencia) }}</td>
        <td class="text-right font-bold c-green">{{ $porcentajeAsistencia }}%</td>
    </tr>

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 2: CONVERSIÓN POR REGISTRADOR / VENDEDOR -->
    <tr>
        <td colspan="6" class="section-header">2. CONVERSIÓN Y REGISTRO POR VENDEDOR / REGISTRADOR</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th>Vendedor / Registrador</th>
        <th class="text-center">Inscritos Registrados</th>
        <th class="text-center">Asistieron Realmente</th>
        <th class="text-center">Ausentes</th>
        <th class="text-right">Efectividad (%)</th>
    </tr>
    @forelse($rankingVendedores as $idx => $v)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td class="font-bold">{{ $v->vendedor_nombre }}</td>
        <td class="text-center font-bold">{{ number_format($v->total_registrados) }} pers.</td>
        <td class="text-center font-bold c-green">{{ number_format($v->total_asistieron) }} fueron</td>
        <td class="text-center c-red">{{ number_format($v->ausentes) }}</td>
        <td class="text-right font-bold">{{ $v->pct_asistencia }}%</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de registradores.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 3: AFLUENCIA HORARIA GLOBAL -->
    <tr>
        <td colspan="6" class="section-header">3. AFLUENCIA HORARIA GLOBAL DE ASISTENCIA</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th>Franja Horaria</th>
        <th class="text-center">Asistentes Únicos</th>
        <th class="text-center">Total Escaneos</th>
        <th colspan="2" class="text-right">% sobre Pico Máximo</th>
    </tr>
    @php $maxHoraG = $asistenciaPorHora->max('asistentes_unicos') ?: 1; @endphp
    @forelse($asistenciaPorHora as $idx => $ahG)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td class="font-bold c-gold">{{ $ahG->hora_raw }}:00 hrs</td>
        <td class="text-center font-bold">{{ number_format($ahG->asistentes_unicos) }} pers.</td>
        <td class="text-center">{{ number_format($ahG->total_escaneos) }} escaneos</td>
        <td colspan="2" class="text-right font-bold c-blue">{{ round(($ahG->asistentes_unicos / $maxHoraG) * 100, 1) }}%</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de afluencia horaria.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 4: PUESTOS Y ROLES DE AUDIENCIA -->
    <tr>
        <td colspan="6" class="section-header">4. PUESTOS Y ROLES MÁS FRECUENTES (PERFIL DE AUDIENCIA)</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th colspan="2">Puesto / Rol del Participante</th>
        <th class="text-center">Asistentes Únicos</th>
        <th colspan="2" class="text-right">% de Representación</th>
    </tr>
    @forelse($asistenciaPorPuesto as $idx => $puG)
    @php $pctP = $totalAsistieron > 0 ? round(($puG->total_asistentes / $totalAsistieron) * 100, 1) : 0; @endphp
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td colspan="2" class="font-bold">{{ $puG->puesto }}</td>
        <td class="text-center font-bold c-green">{{ number_format($puG->total_asistentes) }} pers.</td>
        <td colspan="2" class="text-right font-bold c-blue">{{ $pctP }}%</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de puestos.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 5: SUCURSALES -->
    <tr>
        <td colspan="6" class="section-header">5. DISTRIBUCIÓN DE ASISTENCIA POR SUCURSAL</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th colspan="2">Sucursal / Filial</th>
        <th class="text-center">Asistentes Únicos</th>
        <th colspan="2" class="text-right">% Aforo Global</th>
    </tr>
    @forelse($asistenciaPorSucursal as $idx => $sucG)
    @php $pctS = $totalAsistieron > 0 ? round(($sucG->total_asistentes / $totalAsistieron) * 100, 1) : 0; @endphp
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td colspan="2" class="font-bold">{{ $sucG->sucursal ?: 'Sin Sucursal' }}</td>
        <td class="text-center font-bold c-gold">{{ number_format($sucG->total_asistentes) }} pers.</td>
        <td colspan="2" class="text-right font-bold c-green">{{ $pctS }}%</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de sucursales.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 6: PROVEEDORES PUNTOS -->
    <tr>
        <td colspan="6" class="section-header">6. PROVEEDORES QUE REPARTIERON MÁS PUNTOS</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th colspan="2">Proveedor</th>
        <th class="text-center">Participantes Atendidos</th>
        <th class="text-center">Operaciones</th>
        <th class="text-right">Total Puntos Otorgados</th>
    </tr>
    @forelse($topProveedoresPuntos as $idx => $p)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td colspan="2" class="font-bold">{{ $p->proveedor }}</td>
        <td class="text-center font-bold">{{ number_format($p->participantes_atendidos) }} pers.</td>
        <td class="text-center">{{ number_format($p->num_transacciones) }} ops.</td>
        <td class="text-right font-bold c-gold">{{ number_format($p->total_puntos) }} pts</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de puntos por proveedor.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 7: PREMIOS CANJEADOS -->
    <tr>
        <td colspan="6" class="section-header">7. PREMIOS CANJEADOS Y TÓMBOLA DE PREMIOS</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th colspan="2">Premio</th>
        <th class="text-center">Piezas Canjeadas</th>
        <th class="text-center">Participantes Canjeadores</th>
        <th class="text-right">Stock Actual Restante</th>
    </tr>
    @forelse($topPremiosCanjeados as $idx => $pr)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td colspan="2" class="font-bold">{{ $pr->premio }}</td>
        <td class="text-center font-bold c-green">{{ number_format($pr->total_canjeados) }} canjeados</td>
        <td class="text-center">{{ number_format($pr->participantes_canjeadores) }} pers.</td>
        <td class="text-right font-bold c-gold">{{ number_format($pr->stock_actual) }} pzas</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin canjes de premios registrados.</td></tr>
    @endforelse

    <tr class="spacer-row"><td colspan="6"></td></tr>

    <!-- SECCIÓN 8: DESGLOSE DETALLADO DÍA POR DÍA -->
    <tr>
        <td colspan="6" class="section-header">8. DESGLOSE DETALLADO DÍA POR DÍA DEL EVENTO</td>
    </tr>

    @foreach($statsPorDia as $fStr => $dStats)
    <tr class="spacer-row"><td colspan="6"></td></tr>
    <tr>
        <td colspan="6" class="day-header">
            DÍA {{ $dStats['numero_dia'] }}: {{ strtoupper($dStats['fecha_formateada']) }} 
            (Asistentes Únicos: {{ number_format($dStats['asistentes_unicos']) }} pers. | Puntos Otorgados: {{ number_format($dStats['total_puntos_dia']) }} pts)
        </td>
    </tr>

    <!-- AFLUENCIA HORARIA DEL DÍA -->
    <tr>
        <td colspan="6" class="sub-header">8.{{ $dStats['numero_dia'] }}.1 Afluencia Horaria del Día {{ $dStats['numero_dia'] }}</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th>Franja Horaria</th>
        <th class="text-center">Asistentes Únicos</th>
        <th class="text-center">Total Escaneos</th>
        <th colspan="2" class="text-right">Concurrencia</th>
    </tr>
    @forelse($dStats['afluencia_horaria'] as $idx => $ahD)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td class="font-bold c-gold">{{ $ahD->hora_raw }}:00 hrs</td>
        <td class="text-center font-bold">{{ number_format($ahD->asistentes_unicos) }} pers.</td>
        <td class="text-center">{{ number_format($ahD->total_escaneos) }} escaneos</td>
        <td colspan="2" class="text-right font-bold c-blue">{{ $ahD->asistentes_unicos }} activos</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin registros en este día.</td></tr>
    @endforelse

    <!-- VENDEDORES DEL DÍA -->
    <tr>
        <td colspan="6" class="sub-header">8.{{ $dStats['numero_dia'] }}.2 Conversión de Vendedores / Registradores en el Día {{ $dStats['numero_dia'] }}</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th>Vendedor / Registrador</th>
        <th class="text-center">Inscritos Registrados</th>
        <th class="text-center">Asistieron este Día</th>
        <th class="text-center">Ausentes este Día</th>
        <th class="text-right">Efectividad el Día (%)</th>
    </tr>
    @forelse($dStats['vendedores'] as $idx => $vD)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td class="font-bold">{{ $vD->vendedor }}</td>
        <td class="text-center font-bold">{{ number_format($vD->total_registrados) }} pers.</td>
        <td class="text-center font-bold c-green">{{ number_format($vD->total_asistieron) }} fueron</td>
        <td class="text-center c-red">{{ number_format($vD->ausentes) }}</td>
        <td class="text-right font-bold">{{ $vD->pct_asistencia }}%</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin datos de vendedores en este día.</td></tr>
    @endforelse

    <!-- ACTIVIDADES Y SESIONES DEL DÍA -->
    <tr>
        <td colspan="6" class="sub-header">8.{{ $dStats['numero_dia'] }}.3 Concurrencia por Actividad / Sesión en el Día {{ $dStats['numero_dia'] }}</td>
    </tr>
    <tr>
        <th class="text-center">#</th>
        <th colspan="2">Actividad / Sesión</th>
        <th class="text-center">Horario</th>
        <th>Salón</th>
        <th class="text-right">Aforo Asistente</th>
    </tr>
    @forelse($dStats['actividades'] as $idx => $actD)
    <tr class="{{ $idx % 2 == 1 ? 'even' : '' }}">
        <td class="text-center font-bold">{{ $idx + 1 }}</td>
        <td colspan="2" class="font-bold">{{ $actD->actividad }}</td>
        <td class="text-center font-bold c-gold">{{ $actD->horario }}</td>
        <td>{{ $actD->salon ?: 'Sin Salón' }}</td>
        <td class="text-right font-bold c-green">{{ number_format($actD->total_asistieron) }} pers.</td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">Sin actividades agendadas este día.</td></tr>
    @endforelse

    @endforeach

</table>

</body>
</html>
