@if($participantes->isEmpty())
    <div style="flex:1; min-height:220px; padding:35px 20px; text-align:center; color:var(--text-secondary); background:rgba(15,23,42,0.6); border-radius:4px; border:1px solid rgba(255,255,255,0.08); display:flex; flex-direction:column; align-items:center; justify-content:center; backdrop-filter:blur(8px);">
        <div style="width:54px; height:54px; border-radius:50%; background:rgba(249,115,22,0.1); border:1px solid rgba(249,115,22,0.25); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:24px; margin-bottom:12px;">
            <i class="bi bi-search"></i>
        </div>
        <div style="font-size:14px; font-weight:700; color:var(--text-primary); margin-bottom:4px;">No se encontraron participantes</div>
        <div style="font-size:12.5px; color:var(--text-muted); max-width:320px;">Comprueba el término ingresado o añade un participante directamente.</div>
        <button class="btn btn-primary" onclick="abrirModalRegistro()" style="margin-top:16px; padding:9px 20px; font-weight:700; font-size:13px; border-radius:4px; box-shadow:0 4px 14px rgba(249,115,22,0.3);">
            <i class="bi bi-person-plus-fill"></i> + Nuevo Registro Rápido
        </button>
    </div>
@else
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
        <span style="color:var(--text-secondary); font-size:13px; font-weight:600; display:flex; align-items:center; gap:6px;">
            Resultados: <strong style="color:var(--accent-gold); font-size:15px; font-weight:800;">{{ count($participantes) }}</strong>
        </span>
        <button class="btn btn-primary btn-sm" onclick="abrirModalRegistro()" style="font-weight:700; padding:8px 16px; border-radius:9px; box-shadow:0 4px 12px rgba(249,115,22,0.25);">
            <i class="bi bi-person-plus-fill"></i> + Nuevo Registro Rápido
        </button>
    </div>

    <!-- VISTA MÓVIL: TARJETAS TÁCTILES MODERNAS (< 768px) -->
    <div class="asistencia-vista-movil">
        @foreach($participantes as $p)
            @php
                $clase = $p->clases->first();
                $words = explode(' ', trim($p->Nombre));
                $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            @endphp
            <div style="background:rgba(15,23,42,0.85); border:1px solid rgba(255,255,255,0.09); border-radius:4px; padding:16px; box-shadow:0 6px 18px rgba(0,0,0,0.3); backdrop-filter:blur(10px); position:relative; overflow:hidden;">
                <!-- Franja decorativa lateral -->
                <div style="position:absolute; left:0; top:0; bottom:0; width:4px; background:{{ $clase && $clase->Asistio ? '#22c55e' : ($clase ? 'var(--accent-gold)' : '#64748b') }};"></div>

                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:12px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:40px; height:40px; border-radius:4px; background:linear-gradient(135deg, rgba(249,115,22,0.2), rgba(212,175,55,0.1)); border:1px solid rgba(249,115,22,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:14px; font-weight:800; flex-shrink:0;">
                            {{ $initials ?: 'P' }}
                        </div>
                        <div>
                            <div style="font-size:15px; font-weight:800; color:var(--text-primary); line-height:1.2;">{{ $p->Nombre }}</div>
                            <div style="font-size:12px; color:var(--text-secondary); margin-top:2px; display:flex; align-items:center; gap:4px;">
                                <i class="bi bi-building" style="color:var(--text-muted); font-size:11px;"></i> {{ $p->Proveedor ?: 'Sin proveedor' }}
                            </div>
                        </div>
                    </div>

                    <div>
                        @if($clase)
                            @if($clase->Asistio)
                                <span class="badge" style="background:rgba(34,197,94,0.18); border:1px solid rgba(34,197,94,0.4); color:#4ade80; font-weight:800; font-size:11px; padding:5px 10px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="bi bi-check-circle-fill"></i> Asistió
                                </span>
                            @else
                                <span class="badge" style="background:rgba(249,115,22,0.18); border:1px solid rgba(249,115,22,0.4); color:var(--accent-gold); font-weight:800; font-size:11px; padding:5px 10px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="bi bi-clock-history"></i> Pendiente
                                </span>
                            @endif
                        @else
                            <span class="badge" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:var(--text-muted); font-weight:700; font-size:10.5px; padding:4px 8px; border-radius:4px;">
                                No Inscrito
                            </span>
                        @endif
                    </div>
                </div>

                @if($clase && $clase->Asistio && $clase->Asistencia_Fecha)
                    <div style="font-size:11px; color:rgba(255,255,255,0.6); margin-bottom:12px; display:flex; align-items:center; gap:5px; background:rgba(255,255,255,0.03); padding:4px 8px; border-radius:4px; border:1px solid rgba(255,255,255,0.05);">
                        <i class="bi bi-clock" style="color:#4ade80;"></i> Asistencia confirmada a las <strong>{{ \Carbon\Carbon::parse($clase->Asistencia_Fecha)->format('H:i:s A') }}</strong>
                    </div>
                @endif

                <!-- BOTÓN DE ACCIÓN TOUCH FULL-WIDTH -->
                <div style="margin-top:8px;">
                    @if($clase)
                        @if(!$clase->Asistio)
                            <button class="btn btn-success" onclick="btnMarcarAsistencia({{ $p->ID }})" style="width:100%; font-weight:800; font-size:13px; padding:10px; border-radius:4px; background:linear-gradient(135deg, #16a34a, #22c55e); border:none; box-shadow:0 4px 14px rgba(34,197,94,0.3); display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                                <i class="bi bi-check2-square" style="font-size:16px;"></i> Marcar Asistencia
                            </button>
                        @else
                            @if(auth()->check() && auth()->user()->Rol === 'Administrador')
                                <button class="btn btn-secondary" onclick="toggleAsistenciaManual({{ $p->ID }}, true)" style="width:100%; font-weight:700; font-size:12.5px; padding:9px; border-radius:4px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.35); color:#f87171; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                                    <i class="bi bi-x-circle-fill"></i> Quitar Asistencia
                                </button>
                            @else
                                <div style="width:100%; text-align:center; color:#4ade80; font-size:13px; font-weight:800; padding:8px; background:rgba(34,197,94,0.12); border-radius:4px; border:1px solid rgba(34,197,94,0.25);">
                                    <i class="bi bi-check-lg"></i> Asistencia Registrada
                                </div>
                            @endif
                        @endif
                    @else
                        <button class="btn btn-primary" onclick="btnInscribirYAsistir({{ $p->ID }})" style="width:100%; font-weight:800; font-size:13px; padding:10px; border-radius:4px; background:linear-gradient(135deg, #ea580c, #f97316); border:none; box-shadow:0 4px 14px rgba(249,115,22,0.3); display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                            <i class="bi bi-plus-circle-fill" style="font-size:15px;"></i> Inscribir y Asistir
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- VISTA ESCRITORIO: TABLA MODERNA CON ELEGANCIA DE CORTE (>= 768px) -->
    <div class="asistencia-vista-desktop" style="overflow-x:auto; width:100%; border-radius:4px; border:1px solid rgba(255,255,255,0.08); background:rgba(15,23,42,0.6); box-shadow:0 6px 20px rgba(0,0,0,0.3); backdrop-filter:blur(10px);">
        <table class="table align-middle" style="width:100%; min-width:600px; border-collapse:collapse; text-align:left; margin:0;">
            <thead>
                <tr style="border-bottom:1px solid rgba(255,255,255,0.1); background:rgba(15,23,42,0.9); color:var(--text-secondary); font-size:11px; text-transform:uppercase; letter-spacing:0.6px;">
                    <th style="padding:14px; width:65px;">ID</th>
                    <th style="padding:14px;">Participante</th>
                    <th style="padding:14px;">Empresa / Proveedor</th>
                    <th style="padding:14px;">Estado de Asistencia</th>
                    <th style="padding:14px; text-align:right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participantes as $p)
                    @php
                        $clase = $p->clases->first();
                        $words = explode(' ', trim($p->Nombre));
                        $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                    @endphp
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04); font-size:13px; transition:background 0.15s;">
                        <td style="padding:14px; font-weight:800; color:var(--text-muted);">#{{ $p->ID }}</td>
                        <td style="padding:14px; font-weight:800; color:var(--text-primary);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:32px; height:32px; border-radius:4px; background:linear-gradient(135deg, rgba(249,115,22,0.2), rgba(212,175,55,0.1)); border:1px solid rgba(249,115,22,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:12px; font-weight:800; flex-shrink:0;">
                                    {{ $initials ?: 'P' }}
                                </div>
                                <span style="white-space:nowrap; font-weight:700;">{{ $p->Nombre }}</span>
                            </div>
                        </td>
                        <td style="padding:14px; color:var(--text-secondary); font-size:12.5px; white-space:nowrap;">{{ $p->Proveedor ?: '—' }}</td>
                        <td style="padding:14px; white-space:nowrap;">
                            @if($clase)
                                @if($clase->Asistio)
                                    <span class="badge" style="background:rgba(34,197,94,0.16); border:1px solid rgba(34,197,94,0.35); color:#4ade80; font-weight:800; font-size:11.5px; padding:5px 11px; border-radius:4px; display:inline-flex; align-items:center; gap:5px;">
                                        <i class="bi bi-check-circle-fill"></i> Asistió
                                        @if($clase->Asistencia_Fecha)
                                            <small style="color:rgba(255,255,255,0.6); font-weight:normal; font-size:10.5px; margin-left:2px;">
                                                ({{ \Carbon\Carbon::parse($clase->Asistencia_Fecha)->format('H:i:s A') }})
                                            </small>
                                        @endif
                                    </span>
                                @else
                                    <span class="badge" style="background:rgba(249,115,22,0.16); border:1px solid rgba(249,115,22,0.35); color:var(--accent-gold); font-weight:800; font-size:11.5px; padding:5px 11px; border-radius:4px; display:inline-flex; align-items:center; gap:5px;">
                                        <i class="bi bi-clock-history"></i> Inscrito (Pendiente)
                                    </span>
                                @endif
                            @else
                                <span class="badge" style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-muted); font-size:11px; padding:4px 9px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                    <i class="bi bi-dash-circle"></i> No Inscrito
                                </span>
                            @endif
                        </td>
                        <td style="padding:14px; text-align:right; white-space:nowrap;">
                            @if($clase)
                                @if(!$clase->Asistio)
                                    <button class="btn btn-sm btn-success" onclick="btnMarcarAsistencia({{ $p->ID }})" style="font-weight:700; font-size:12.5px; padding:6px 14px; border-radius:4px; background:linear-gradient(135deg, #16a34a, #22c55e); border:none; box-shadow:0 3px 10px rgba(34,197,94,0.25);">
                                        <i class="bi bi-check2-square"></i> Marcar Asistencia
                                    </button>
                                @else
                                    @if(auth()->check() && auth()->user()->Rol === 'Administrador')
                                        <button class="btn btn-sm btn-secondary" onclick="toggleAsistenciaManual({{ $p->ID }}, true)" style="font-weight:600; font-size:11.5px; padding:5px 11px; border-radius:4px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.35); color:#f87171;" title="Permiso de Administrador: Quitar asistencia">
                                            <i class="bi bi-x-circle-fill"></i> Quitar Asistencia
                                        </button>
                                    @else
                                        <span style="color:#4ade80; font-size:12.5px; font-weight:800;"><i class="bi bi-check-lg"></i> Confirmado</span>
                                    @endif
                                @endif
                            @else
                                <button class="btn btn-sm btn-primary" onclick="btnInscribirYAsistir({{ $p->ID }})" style="font-weight:700; font-size:12.5px; padding:6px 14px; border-radius:4px; background:linear-gradient(135deg, #ea580c, #f97316); border:none; box-shadow:0 3px 10px rgba(249,115,22,0.25);">
                                    <i class="bi bi-plus-circle-fill"></i> Inscribir y Asistir
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif