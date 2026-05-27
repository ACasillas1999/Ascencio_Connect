@if($participantes->isEmpty())
    <div style="padding:30px; text-align:center; color:var(--text-secondary); background:var(--bg-dark); border-radius:8px;">
        <i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:10px;"></i>
        No se encontraron participantes con esa búsqueda.
        <br><br>
        <button class="btn btn-primary" onclick="abrirModalRegistro()">+ Nuevo Registro Rápido</button>
    </div>
@else
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
        <span style="color:var(--text-secondary); font-size:14px;">Resultados encontrados: {{ count($participantes) }}</span>
        <button class="btn btn-secondary btn-sm" onclick="abrirModalRegistro()">+ Nuevo Registro Rápido</button>
    </div>
    <div style="overflow-x:auto;">
        <table class="table-modern" style="width:100%; border-collapse:collapse; text-align:left;">
            <thead>
                <tr style="border-bottom:2px solid var(--border); color:var(--text-secondary);">
                    <th style="padding:10px;">ID</th>
                    <th style="padding:10px;">Nombre Completo</th>
                    <th style="padding:10px;">Empresa</th>
                    <th style="padding:10px;">Estado</th>
                    <th style="padding:10px;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participantes as $p)
                    @php
                        $clase = $p->clases->first();
                    @endphp
                    <tr style="border-bottom:1px solid var(--border);">
                        <td style="padding:10px;">{{ $p->ID }}</td>
                        <td style="padding:10px; font-weight:500;">{{ $p->Nombre }}</td>
                        <td style="padding:10px; color:var(--text-secondary); font-size:13px;">{{ $p->Proveedor ?: '—' }}</td>
                        <td style="padding:10px;">
                            @if($clase)
                                @if($clase->Asistio)
                                    <span style="color:#00bc8c; font-weight:bold;">
                                        <i class="bi bi-check-circle-fill"></i> Asistió 
                                        @if($clase->Asistencia_Fecha)
                                            <br><small style="color:var(--text-secondary); font-size:12px; font-weight:normal;">({{ \Carbon\Carbon::parse($clase->Asistencia_Fecha)->format('Y-m-d h:i:s A') }})</small>
                                        @endif
                                    </span>
                                @else
                                    <span style="color:var(--accent-gold); font-weight:bold;"><i class="bi bi-clock"></i> Inscrito (Pendiente)</span>
                                @endif
                            @else
                                <span style="color:var(--text-secondary);"><i class="bi bi-dash-circle"></i> No Inscrito</span>
                            @endif
                        </td>
                        <td style="padding:10px;">
                            @if($clase)
                                @if(!$clase->Asistio)
                                    <button class="btn btn-sm btn-success" onclick="btnMarcarAsistencia({{ $p->ID }})">
                                        <i class="bi bi-check2-square"></i> Asistir
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-sm btn-primary" onclick="btnInscribirYAsistir({{ $p->ID }})">
                                    <i class="bi bi-plus-circle"></i> Inscribir y Asistir
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
