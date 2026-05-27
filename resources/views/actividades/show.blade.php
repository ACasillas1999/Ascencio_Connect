@extends('layouts.app')

@section('content')
@php
    $horarioActivo = request('horario');
@endphp

<div style="padding:20px; color:var(--text-primary);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; font-weight:600;">Detalles de la Actividad: <span style="color:var(--accent-gold);">{{ $actividad->Actividad }}</span></h2>
        <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:8px;">
            <i class="bi bi-arrow-left"></i> Volver al Evento
        </a>
    </div>

    @if(session('success'))
        <div style="background:rgba(0,188,140,0.1); color:#00bc8c; padding:15px; border-radius:8px; margin-bottom:20px; border-left:4px solid #00bc8c;">
            {{ session('success') }}
        </div>
    @endif

    {{-- ═══════ LAYOUT: CENTRO + DERECHA ═══════ --}}
    <div style="display:grid; grid-template-columns:1fr 340px; gap:20px; align-items:start;">

        {{-- ═══════ CENTRO: Gestión de Asistencia ═══════ --}}
        <div>
            <div style="background:var(--bg-secondary); border-radius:12px; padding:20px; box-shadow:0 4px 6px rgba(0,0,0,0.3); border:1px solid var(--border);">
                <h3 style="margin-top:0; border-bottom:1px solid var(--border); padding-bottom:10px; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-people-fill" style="color:var(--accent-gold);"></i> Gestión de Asistencia
                </h3>

                {{-- Buscador --}}
                <div class="search-container" style="position:relative; margin-bottom:15px;">
                    <i class="bi bi-search" style="position:absolute; left:15px; top:12px; color:var(--text-secondary);"></i>
                    <input type="text" id="busqueda" placeholder="Buscar por nombre, teléfono o empresa..." class="form-control" style="padding-left:45px;">
                </div>

                {{-- Scanner QR (oculto por defecto en desktop) --}}
                <div style="margin-bottom:15px;">
                    <button type="button" id="toggleQR" onclick="document.getElementById('qrPanel').style.display = document.getElementById('qrPanel').style.display === 'none' ? 'block' : 'none'; this.querySelector('i').classList.toggle('bi-chevron-down'); this.querySelector('i').classList.toggle('bi-chevron-up');" style="background:var(--bg-dark); border:1px solid var(--border-subtle); border-radius:8px; padding:10px 16px; color:var(--text-secondary); font-size:13px; cursor:pointer; width:100%; text-align:left; display:flex; align-items:center; gap:8px; transition:all 0.2s;">
                        <i class="bi bi-camera"></i> Escáner QR / Cámara <i class="bi bi-chevron-down" style="margin-left:auto;"></i>
                    </button>
                    <div id="qrPanel" style="display:none; margin-top:10px;">
                        <div style="background:var(--bg-dark); padding:15px; border-radius:8px; border:1px solid var(--border);">
                            <div id="qrWrap" style="display:flex; flex-direction:column; align-items:center; gap:10px;">
                                <div style="display:flex; gap:10px; flex-wrap:wrap; width:100%;">
                                    <select id="cameraSelect" class="form-select" style="flex:1; min-width:200px;"></select>
                                    <button id="btnStartQR" type="button" class="btn btn-primary"><i class="bi bi-camera"></i> Encender</button>
                                    <button id="btnStopQR" type="button" class="btn btn-secondary" disabled><i class="bi bi-camera-video-off"></i> Apagar</button>
                                </div>
                                <div style="position:relative; width:100%; max-width:400px; border-radius:8px; overflow:hidden; background:#000;">
                                    <video id="qrVideo" playsinline style="width:100%; height:auto; display:block;"></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pistola escáner USB --}}
                <div style="background:var(--bg-dark); padding:12px; border-radius:8px; margin-bottom:15px; border:1px solid var(--border);">
                    <form id="scanForm" onsubmit="return false;" style="display:flex; gap:10px;">
                        <input type="text" id="idParticipante" placeholder="Pistola Escáner USB o ID manual..." class="form-control" style="flex:1; border: 2px dashed var(--accent-gold);">
                        <button id="btnProcesarScan" class="btn btn-primary" type="button"><i class="bi bi-upc-scan"></i></button>
                    </form>
                </div>

                {{-- Tabla de participantes --}}
                <div id="resultado">
                    <!-- Contenido dinámico desde _tabla_asistencia.blade.php -->
                </div>
            </div>
        </div>

        {{-- ═══════ DERECHA: Info General + Horarios ═══════ --}}
        <div style="position:sticky; top:20px;">
            {{-- Info General --}}
            <div style="background:var(--bg-secondary); border-radius:12px; padding:20px; box-shadow:0 4px 6px rgba(0,0,0,0.3); border:1px solid var(--border); margin-bottom:16px;">
                <h4 style="margin:0 0 14px; font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-info-circle-fill" style="color:var(--accent-gold);"></i> Información General
                </h4>
                <div style="display:grid; gap:10px; font-size:13px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Evento</span>
                        <span style="font-weight:600; color:var(--text-primary); text-align:right; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ optional($evento)->name_evento ?? 'Evento #'.$actividad->ID_Evento }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Descripción</span>
                        <span style="font-weight:600; color:var(--text-primary); text-align:right; max-width:180px;">{{ $actividad->Descripcion ?: '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Puntos Default</span>
                        <span class="badge badge-gold">{{ $actividad->Puntos_Default }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Acceso</span>
                        {!! $actividad->Exclusiva ? '<span style="color:#ef4444; font-weight:bold; font-size:12px;"><i class="bi bi-lock-fill"></i> Exclusiva</span>' : '<span style="color:#00bc8c; font-size:12px;"><i class="bi bi-unlock-fill"></i> Pública</span>' !!}
                    </div>
                </div>

                {{-- Contador Inscritos --}}
                <div style="margin-top:14px; padding:14px; background:var(--bg-dark); border-radius:8px; text-align:center;">
                    <div style="font-size:28px; font-weight:800; color:var(--accent-gold); line-height:1;">
                        {{ $clases->count() }} <span style="font-size:14px; font-weight:400; color:var(--text-secondary);">/ {{ $actividad->capacidad }}</span>
                    </div>
                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px; text-transform:uppercase; letter-spacing:1px;">Participantes Inscritos</div>
                    @php
                        $pct = $actividad->capacidad > 0 ? min(100, round(($clases->count() / $actividad->capacidad) * 100)) : 0;
                        $barColor = $pct > 90 ? '#ef4444' : ($pct > 70 ? '#f59e0b' : '#10b981');
                    @endphp
                    <div style="margin-top:8px; height:6px; background:rgba(255,255,255,0.08); border-radius:3px; overflow:hidden;">
                        <div style="height:100%; width:{{ $pct }}%; background:{{ $barColor }}; border-radius:3px; transition:width 0.5s ease;"></div>
                    </div>
                </div>
            </div>

            {{-- Horarios --}}
            <div style="background:var(--bg-secondary); border-radius:12px; padding:20px; box-shadow:0 4px 6px rgba(0,0,0,0.3); border:1px solid var(--border);">
                <h4 style="margin:0 0 14px; font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-calendar3" style="color:var(--accent-gold);"></i> Horarios en la Agenda
                </h4>

                @if($agenda->isEmpty())
                    <div style="padding:16px; text-align:center; color:var(--text-muted); background:var(--bg-dark); border-radius:8px; font-size:13px;">
                        <i class="bi bi-calendar-x" style="font-size:20px; display:block; margin-bottom:6px; opacity:0.5;"></i>
                        No está en la agenda aún.
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @foreach($agenda as $slot)
                            @php
                                $esActivo = ($horarioActivo && $slot->ID == $horarioActivo);
                                $insSlot = \DB::table('clase')->where('ID_Agenda', $slot->ID)->count();
                            @endphp
                            <div style="padding:12px; border-radius:8px; border:1px solid {{ $esActivo ? 'rgba(212,175,55,0.5)' : 'var(--border-subtle)' }}; background:{{ $esActivo ? 'rgba(212,175,55,0.08)' : 'var(--bg-dark)' }}; transition:all 0.2s; {{ $esActivo ? 'box-shadow:0 0 0 2px rgba(212,175,55,0.2);' : '' }}">
                                @if($esActivo)
                                    <div style="font-size:9px; text-transform:uppercase; letter-spacing:1.5px; color:var(--accent-gold); font-weight:800; margin-bottom:6px;">
                                        <i class="bi bi-arrow-right-circle-fill"></i> CONSULTANDO ESTE HORARIO
                                    </div>
                                @endif
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <span style="font-weight:700; font-size:13px; color:var(--text-primary);">
                                        <i class="bi bi-clock" style="color:var(--accent-gold); font-size:11px;"></i>
                                        {{ $slot->Horario }}
                                    </span>
                                    <span class="badge badge-gold" style="font-size:10px;">{{ $slot->Puntos_Asistencia }} pts</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-muted);">
                                    <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($slot->Fecha)->format('d/m/Y') }}</span>
                                    <span><i class="bi bi-geo-alt"></i> {{ $slot->Salon ?: 'Sin asignar' }}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                                    <span style="font-size:11px; color:var(--text-muted);"><i class="bi bi-people"></i> {{ $insSlot }} inscritos</span>
                                    @if(!$esActivo)
                                        <a href="{{ route('actividades.show', $actividad->ID) }}?horario={{ $slot->ID }}" style="font-size:10px; color:var(--accent-gold); text-decoration:none; display:flex; align-items:center; gap:3px;">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Registro Rápido -->
<div id="modalRegistro" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
    <div style="background:var(--bg-secondary); padding:25px; border-radius:12px; width:90%; max-width:500px; border:1px solid var(--border);">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border); padding-bottom:10px; margin-bottom:20px;">
            <h3 style="margin:0; color:var(--text-primary);">Registro Nuevo Participante</h3>
            <button onclick="cerrarModalRegistro()" style="background:none; border:none; color:var(--text-secondary); font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form id="formRegistroRapido">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Nombre Completo:</label>
                <input type="text" id="regNombre" name="nombre" required class="form-control">
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Teléfono (10 dígitos):</label>
                <input type="tel" id="regTelefono" name="telefono" required class="form-control" pattern="[0-9]{10}">
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block; margin-bottom:5px;">Empresa / Proveedor:</label>
                <input type="text" id="regProveedor" name="proveedor" required class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">REGISTRAR E INSCRIBIR</button>
        </form>
    </div>
</div>

<script src="https://unpkg.com/@zxing/browser@latest"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const busInput = document.getElementById("busqueda");
    const resDiv = document.getElementById("resultado");
    const scanInput = document.getElementById("idParticipante");
    const scanForm = document.getElementById("scanForm");
    const btnProcesarScan = document.getElementById("btnProcesarScan");
    const actividadId = "{{ $actividad->ID }}";
    const csrfToken = "{{ csrf_token() }}";

    function extraerIdParticipante(raw) {
        const limpio = String(raw || '').trim();
        if (!limpio) return 0;
        if (/^\d+$/.test(limpio)) return parseInt(limpio, 10);
        let match = limpio.match(/\bID\b\s*[:\-NÑ]?\s*(\d{1,10})(?=\D|$)/i);
        if (match) return parseInt(match[1], 10);
        match = limpio.match(/ID\s*[:\-NÑ]?\s*(\d{1,10})(?=\D|$)/i);
        if (match) return parseInt(match[1], 10);
        match = limpio.match(/(?:^|\D)(\d{1,10})(?=\D|$)/);
        return match ? parseInt(match[1], 10) : 0;
    }

    const horarioActivo = "{{ $horarioActivo }}";

    function refresh() {
        const query = busInput.value;
        
        fetch("{{ route('actividades.buscar', $actividad->ID) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ busqueda: query, horario: horarioActivo })
        })
        .then(res => res.text())
        .then(html => resDiv.innerHTML = html)
        .catch(err => console.error(err));
    }

    busInput.addEventListener("input", refresh);
    refresh();

    window.btnMarcarAsistencia = (id) => enviarAccion("{{ route('actividades.asistencia', $actividad->ID) }}", id);
    window.btnInscribirYAsistir = (id) => enviarAccion("{{ route('actividades.inscribir', $actividad->ID) }}", id);

    function enviarAccion(url, idPart) {
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ id_participante: idPart, horario: horarioActivo })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.msg);
            if(data.ok) refresh();
        })
        .catch(err => console.error(err));
    }

    let lastScannedId = null;
    let lastScanTime = 0;

    function procesarScan(e, rawValue = null) {
        if(e) e.preventDefault();
        const raw = rawValue !== null ? rawValue : scanInput.value.trim();
        if(!raw) return;
        const idParticipante = extraerIdParticipante(raw);
        if(!idParticipante) {
            alert("No se pudo extraer un ID válido del QR");
            scanInput.focus();
            return;
        }
        
        // Bloqueo para no escanear el mismo ID 20 veces en menos de 3 segundos
        const now = Date.now();
        if (lastScannedId === idParticipante && (now - lastScanTime) < 3000) {
            scanInput.value = '';
            scanInput.focus();
            return; 
        }
        
        lastScannedId = idParticipante;
        lastScanTime = now;

        const url = "{{ route('actividades.asistencia', $actividad->ID) }}";
            
        enviarAccion(url, idParticipante);
        scanInput.value = '';
        scanInput.focus();
    }

    scanForm.addEventListener("submit", procesarScan);
    document.getElementById("btnProcesarScan").addEventListener("click", procesarScan);

    // Escáner Global Mágico
    let keysBuffer = '';
    let keysTimeout;
    
    document.addEventListener('keydown', function(e) {
        // Ignorar si el usuario está escribiendo manualmente en el buscador principal o modal
        if (e.target.tagName === 'INPUT' && e.target.id !== 'idParticipante') {
            return;
        }
        
        if (e.key === 'Enter') {
            if (keysBuffer.length > 0) {
                e.preventDefault();
                procesarScan(null, keysBuffer);
                keysBuffer = '';
            }
            return;
        }
        
        if (e.key.length === 1) { 
            keysBuffer += e.key;
            clearTimeout(keysTimeout);
            keysTimeout = setTimeout(() => {
                keysBuffer = ''; // si pasan más de 50ms sin teclas, lo borramos (fue humano, no pistola)
            }, 50);
        }
    });

    // Auto focus al entrar
    setTimeout(() => scanInput.focus(), 500);

    // Scanner
    const codeReader = new ZXingBrowser.BrowserMultiFormatReader();
    let selectedId;
    const video = document.getElementById('qrVideo');
    const select = document.getElementById('cameraSelect');

    ZXingBrowser.BrowserCodeReader.listVideoInputDevices().then(devices => {
        if(devices.length === 0) return;
        selectedId = devices[0].deviceId;
        devices.forEach(d => {
            const opt = document.createElement('option');
            opt.text = d.label || 'Cámara ' + (select.length + 1);
            opt.value = d.deviceId;
            select.appendChild(opt);
        });
        select.onchange = () => selectedId = select.value;

        document.getElementById('btnStartQR').onclick = () => {
            codeReader.decodeFromVideoDevice(selectedId, video, (res, err) => {
                if (res) {
                    scanInput.value = res.text;
                    procesarScan(new Event('submit'));
                }
            });
            document.getElementById('btnStartQR').disabled = true;
            document.getElementById('btnStopQR').disabled = false;
        };
        
        document.getElementById('btnStopQR').onclick = () => {
            codeReader.reset();
            document.getElementById('btnStartQR').disabled = false;
            document.getElementById('btnStopQR').disabled = true;
        };
    });

    // Modal
    const modal = document.getElementById("modalRegistro");
    window.abrirModalRegistro = () => { modal.style.display = "flex"; };
    window.cerrarModalRegistro = () => { modal.style.display = "none"; };
    
    document.getElementById("formRegistroRapido").onsubmit = function(e) {
        e.preventDefault();
        const data = {
            nombre: document.getElementById('regNombre').value,
            telefono: document.getElementById('regTelefono').value,
            proveedor: document.getElementById('regProveedor').value,
            horario: horarioActivo
        };
        fetch("{{ route('actividades.registro-rapido', $actividad->ID) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            alert(data.msg);
            if(data.ok) {
                cerrarModalRegistro();
                refresh();
                this.reset();
                scanInput.focus();
            }
        });
    };
});
</script>

<style>
    .table-modern th { font-weight: 500; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-modern td { vertical-align: middle; }
    .table-modern tbody tr:hover { background: rgba(255,255,255,0.02); }
</style>
@endsection
