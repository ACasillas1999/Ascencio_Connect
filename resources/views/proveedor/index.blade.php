@extends('layouts.app')

@section('title', 'Panel de Proveedor')

@section('content')
<style>
    /* CSS ESPECÍFICO PARA EL PANEL DE PROVEEDOR (LIGHT Y DARK THEME) */
    .prov-header-card {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
    
    .prov-kpi-card {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .prov-input-card {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .history-card-row {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 14px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        transition: all 0.25s ease;
        backdrop-filter: blur(8px);
    }

    .prov-title {
        color: #ffffff;
    }

    .prov-subtitle {
        color: #94a3b8;
    }

    .prov-input-field {
        background: #1e293b;
        border: 1px solid #f97316;
        color: #ffffff;
    }

    /* REGLAS PARA TEMA CLARO [data-theme="light"] */
    [data-theme="light"] .prov-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
    }

    [data-theme="light"] .prov-title {
        color: #0f172a !important;
    }

    [data-theme="light"] .prov-subtitle {
        color: #64748b !important;
    }

    [data-theme="light"] .prov-kpi-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    }

    [data-theme="light"] .prov-kpi-label {
        color: #64748b !important;
    }

    [data-theme="light"] .prov-input-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04) !important;
    }

    [data-theme="light"] .prov-input-field {
        background: #f8fafc !important;
        border: 1.5px solid #f97316 !important;
        color: #0f172a !important;
    }

    [data-theme="light"] .history-card-row {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
    }

    [data-theme="light"] .history-card-row:hover {
        background: #f8fafc !important;
    }

    [data-theme="light"] .history-card-row[data-prospecto="1"] {
        background: #fffbeb !important;
        border: 1.5px solid #fde68a !important;
    }

    [data-theme="light"] .btn-manual-container {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    /* Modal de Cámara */
    #cameraModalOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(12px);
        z-index: 999999;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    #cameraModalOverlay #qrReaderContainer video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 10px !important;
    }

    #cameraModalOverlay #qrReaderContainer canvas {
        display: none !important;
    }
</style>

<div class="proveedor-container">
    
    <!-- TARJETA CABECERA DE INFORMACIÓN DEL PROVEEDOR -->
    <div class="prov-header-card">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:800; color:#f97316; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-briefcase-fill"></i> Panel de Proveedor
                </div>
                <h2 class="prov-title" style="margin:4px 0 2px 0; font-size:20px; font-weight:900; display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-person-badge-fill" style="color:#f97316;"></i> Proveedor: {{ $usuario }}
                </h2>
            </div>

            @if(isset($eventos_asignados) && $eventos_asignados->count() > 1)
                <form method="GET" action="{{ route('proveedor.index') }}" style="margin:0;">
                    <select name="evento_id" onchange="this.form.submit()" class="form-control prov-input-field" style="font-size:13px; padding:7px 14px; border-radius:6px; font-weight:700;">
                        @foreach($eventos_asignados as $evAsig)
                            <option value="{{ $evAsig->ID_Evento }}" {{ $id_evento == $evAsig->ID_Evento ? 'selected' : '' }}>
                                Evento: {{ $evAsig->name_evento }} ({{ $evAsig->Puntos }} pts)
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-top:12px;">
            <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(249,115,22,0.15); border:1px solid rgba(249,115,22,0.4); color:#ea580c; font-size:12px; font-weight:900; padding:5px 12px; border-radius:6px; text-transform:uppercase;">
                <i class="bi bi-star-fill"></i> Otorgando {{ $puntos }} PTS por escaneo
            </div>
            <div style="display:inline-flex; align-items:center; gap:6px; background:rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.4); color:#0284c7; font-size:12px; font-weight:800; padding:5px 12px; border-radius:6px;">
                <i class="bi bi-calendar-event-fill"></i> {{ $evento_nombre }}
            </div>
        </div>
    </div>

    <!-- PANEL DE ENTRADA DE ESCANEO Y PUNTOS -->
    <div class="prov-input-card">
        <div style="border-bottom:1px solid rgba(148,163,184,0.2); padding-bottom:10px; margin-bottom:14px;">
            <h3 class="prov-title" style="margin:0; font-size:15px; font-weight:900; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-qr-code-scan" style="color:#f97316;"></i> Asignación de Puntos a Participantes
            </h3>
        </div>

        <div>
            <div style="margin-bottom:14px;">
                <button type="button" id="btnStartQR" class="btn btn-primary" style="width:100%; font-weight:900; padding:14px 20px; border-radius:8px; font-size:14px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; justify-content:center; gap:10px; box-shadow:0 4px 16px rgba(249,115,22,0.35); background:linear-gradient(135deg, #f97316, #ea580c); border:none; color:#ffffff;">
                    <i class="bi bi-camera-fill" style="font-size:20px;"></i> Escáner QR con Cámara
                </button>
            </div>

            <!-- INGRESO MANUAL O LECTOR DE CÓDIGO BARRAS USB -->
            <form id="formManualProveedor" onsubmit="procesarIngresoManual(event);" class="btn-manual-container" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; background:rgba(15,23,42,0.5); padding:12px; border-radius:8px; border:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; align-items:center; gap:10px; flex:1; min-width:220px;">
                    <i class="bi bi-upc-scan" style="color:#f97316; font-size:20px;"></i>
                    <input type="text" id="inputCodigoManual" placeholder="Ingresa ID, Teléfono o Código (ej: ID1)..." class="form-control prov-input-field" style="flex:1; font-size:13.5px; border-radius:6px; padding:9px 14px; font-weight:600;" required>
                </div>
                <button class="btn btn-primary" type="submit" style="font-weight:900; padding:10px 22px; border-radius:6px; text-transform:uppercase; font-size:12.5px; background:#f97316; border:none; color:#ffffff;"><i class="bi bi-check-lg" style="font-size:16px;"></i> Asignar Puntos</button>
            </form>
        </div>
    </div>

    <!-- TARJETAS DE KPIS Y RESUMEN DE PUNTOS -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(130px, 1fr)); gap:12px; margin-bottom:20px;">
        <div class="prov-kpi-card">
            <div class="prov-kpi-label" style="font-size:11px; text-transform:uppercase; font-weight:800; letter-spacing:0.5px;">Puntos Entregados</div>
            <div style="font-size:24px; font-weight:900; color:#f97316; margin-top:4px;">
                <i class="bi bi-star-fill"></i> {{ number_format($total_puntos_entregados) }}
            </div>
        </div>
        <div class="prov-kpi-card">
            <div class="prov-kpi-label" style="font-size:11px; text-transform:uppercase; font-weight:800; letter-spacing:0.5px;">Escaneos Realizados</div>
            <div style="font-size:24px; font-weight:900; color:#0284c7; margin-top:4px;">
                <i class="bi bi-qr-code-scan"></i> {{ number_format($total_escaneos) }}
            </div>
        </div>
        <div class="prov-kpi-card">
            <div class="prov-kpi-label" style="font-size:11px; text-transform:uppercase; font-weight:800; letter-spacing:0.5px;">Prospectos Marcados</div>
            <div style="font-size:24px; font-weight:900; color:#d97706; margin-top:4px;">
                <i class="bi bi-person-star-fill"></i> <span id="numProspectos">{{ number_format($total_prospectos ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE PARTICIPANTES ATENDIDOS -->
    <div style="margin-bottom:30px;">
        <div style="border-bottom:1px solid rgba(148,163,184,0.2); padding-bottom:10px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <h3 class="prov-title" style="margin:0; font-size:15px; font-weight:900; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-clock-history" style="color:#f97316;"></i> Historial de Escaneos
            </h3>
            <div style="display:flex; gap:8px; align-items:center;">
                <button type="button" id="btnFilterAll" onclick="filtrarHistorial('todos')" class="btn-filter-historial" style="background:#f97316; color:#ffffff; border:none; font-weight:900; font-size:11.5px; padding:6px 14px; border-radius:6px; cursor:pointer; transition:all 0.2s;">
                    Todos
                </button>
                <button type="button" id="btnFilterProspects" onclick="filtrarHistorial('prospectos')" class="btn-filter-historial" style="background:rgba(217,119,6,0.15); color:#d97706; border:1.5px solid rgba(217,119,6,0.5); font-weight:900; font-size:11.5px; padding:6px 14px; border-radius:6px; cursor:pointer; transition:all 0.2s;">
                    <i class="bi bi-star-fill"></i> Sólo Prospectos ⭐
                </button>
            </div>
        </div>

        @if($historial->isEmpty())
            <div class="prov-input-card" style="padding:32px 20px; text-align:center;">
                <i class="bi bi-inbox" style="font-size:36px; color:#94a3b8; display:block; margin-bottom:10px;"></i>
                <div class="prov-title" style="font-size:14px; font-weight:800;">Aún no has otorgado puntos en este evento</div>
                <div class="prov-subtitle" style="font-size:12px; margin-top:4px;">Usa el escáner QR o el ingreso manual para registrar tu primer participante.</div>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:10px;">
                @foreach($historial as $item)
                    @php
                        $words = explode(' ', trim($item->participante_nombre));
                        $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                        $isProspecto = !empty($item->es_prospecto);
                    @endphp
                    <div class="history-card-row" id="history-row-{{ $item->id_registro }}" data-prospecto="{{ $isProspecto ? '1' : '0' }}">
                        <div style="display:flex; align-items:center; gap:12px; flex:1; min-width:200px;">
                            <div style="width:40px; height:40px; border-radius:8px; background: {{ $isProspecto ? 'linear-gradient(135deg, rgba(217,119,6,0.25), rgba(251,191,36,0.15))' : 'linear-gradient(135deg, rgba(249,115,22,0.2), rgba(212,175,55,0.1))' }}; border: 1.5px solid {{ $isProspecto ? '#d97706' : 'rgba(249,115,22,0.4)' }}; display:flex; align-items:center; justify-content:center; color: {{ $isProspecto ? '#d97706' : '#f97316' }}; font-size:15px; font-weight:900; flex-shrink:0;">
                                {{ $initials ?: 'P' }}
                            </div>
                            <div>
                                <div class="prov-title" style="font-size:14.5px; font-weight:900; line-height:1.2; display:flex; align-items:center; gap:8px;">
                                    <span>{{ $item->participante_nombre }}</span>
                                    <span id="badge-prospect-tag-{{ $item->id_registro }}" style="display: {{ $isProspecto ? 'inline-flex' : 'none' }}; align-items:center; gap:4px; background:rgba(217,119,6,0.15); border:1px solid #d97706; color:#d97706; font-size:10.5px; font-weight:900; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                                        <i class="bi bi-star-fill"></i> Prospecto
                                    </span>
                                </div>
                                <div class="prov-subtitle" style="font-size:12px; margin-top:3px; display:flex; align-items:center; gap:8px; flex-wrap:wrap; font-weight:600;">
                                    <span>ID: #{{ $item->id_participante }}</span>
                                    @if(!empty($item->RFC))
                                        <span>•</span>
                                        <span>RFC: {{ $item->RFC }}</span>
                                    @endif
                                    <span>•</span>
                                    <span><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($item->fecha)->format('h:i:s A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; align-items:center; gap:10px;">
                            <span class="badge" style="background:rgba(34,197,94,0.16); border:1.5px solid rgba(34,197,94,0.4); color:#16a34a; font-weight:900; font-size:12.5px; padding:6px 13px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">
                                +{{ $item->puntos }} PTS
                            </span>

                            <button type="button" 
                                    onclick="toggleProspecto({{ $item->id_registro }}, this)" 
                                    id="btn-prospecto-{{ $item->id_registro }}"
                                    style="background: {{ $isProspecto ? 'rgba(217, 119, 6, 0.18)' : 'rgba(148, 163, 184, 0.12)' }}; 
                                           border: 1.5px solid {{ $isProspecto ? '#d97706' : 'rgba(148, 163, 184, 0.3)' }}; 
                                           color: {{ $isProspecto ? '#d97706' : '#64748b' }}; 
                                           font-weight: 900; font-size: 12px; padding: 7px 14px; border-radius: 6px; 
                                           cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                                <i class="bi {{ $isProspecto ? 'bi-star-fill' : 'bi-star' }}" style="color: {{ $isProspecto ? '#d97706' : 'inherit' }}; font-size: 14px;"></i>
                                <span>{{ $isProspecto ? 'Prospecto ⭐' : 'Marcar Prospecto' }}</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- OVERLAY MODAL CÁMARA ESCÁNER QR -->
<div id="cameraModalOverlay">
    <div style="width:100%; max-width:550px; display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span id="qrStatusText" style="color:#ffffff; font-weight:800; font-size:14px; display:flex; align-items:center; gap:8px;">
            <i class="bi bi-camera-video-fill" style="color:#f97316;"></i> Iniciando cámara...
        </span>
        <button type="button" onclick="apagarCamaraQR()" class="btn btn-danger btn-sm" style="font-weight:900; border-radius:6px; padding:7px 16px; font-size:12.5px; text-transform:uppercase;">
            <i class="bi bi-x-lg"></i> Cerrar Cámara
        </button>
    </div>

    <div style="flex:1; width:100%; max-width:550px; margin:0 auto; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; border-radius:12px; border:3px solid #f97316; box-shadow:0 0 35px rgba(249,115,22,0.5); background:#000;">
        <div id="qrReaderContainer" style="width:100%; height:100%; min-height:280px;"></div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Html5Qrcode Library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5Scanner = null;
    let isHtml5Scanning = false;
    let lastCameraScanCode = "";
    let lastCameraScanTime = 0;

    const SwalToast = Swal.mixin({
        toast: true,
        position: "top",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    document.getElementById("btnStartQR").addEventListener("click", function() {
        abrirCamaraQR();
    });

    function abrirCamaraQR() {
        let modalOverlay = document.getElementById("cameraModalOverlay");
        if (modalOverlay && modalOverlay.parentElement !== document.body) {
            document.body.appendChild(modalOverlay);
        }
        modalOverlay.style.display = "flex";
        encenderCamaraQR();
    }

    function apagarCamaraQR() {
        if (html5Scanner && isHtml5Scanning) {
            html5Scanner.stop().then(() => {
                isHtml5Scanning = false;
                document.getElementById("cameraModalOverlay").style.display = "none";
            }).catch(err => {
                console.error("Error al apagar cámara:", err);
                isHtml5Scanning = false;
                document.getElementById("cameraModalOverlay").style.display = "none";
            });
        } else {
            document.getElementById("cameraModalOverlay").style.display = "none";
        }
    }

    function encenderCamaraQR() {
        if (isHtml5Scanning) return;

        const statusElem = document.getElementById("qrStatusText");
        if (statusElem) statusElem.innerHTML = '<i class="bi bi-camera-video-fill" style="color:#f97316;"></i> Solicitando cámara...';

        if (!html5Scanner) {
            html5Scanner = new Html5Qrcode("qrReaderContainer");
        }

        const config = { 
            fps: 15, 
            qrbox: function(viewfinderWidth, viewfinderHeight) {
                const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                const qrboxSize = Math.max(180, Math.floor(minEdge * 0.70));
                return {
                    width: qrboxSize,
                    height: qrboxSize
                };
            }
        };

        html5Scanner.start(
            { facingMode: "environment" },
            config,
            (decodedText, decodedResult) => {
                const now = Date.now();
                if (lastCameraScanCode === decodedText && (now - lastCameraScanTime) < 2500) {
                    return;
                }
                lastCameraScanCode = decodedText;
                lastCameraScanTime = now;

                if (statusElem) statusElem.innerText = "¡Escaneado: " + decodedText + "!";
                procesarCodigoAsignarPuntos(decodedText);
            },
            (errorMessage) => {
                // Ignore frame decode errors
            }
        )
        .then(() => {
            isHtml5Scanning = true;
            if (statusElem) statusElem.innerHTML = '<i class="bi bi-camera-video-fill" style="color:#f97316;"></i> Escaneando QR... Apunta la cámara al código';
        })
        .catch((err) => {
            console.error("Error iniciando cámara Html5Qrcode:", err);
            isHtml5Scanning = false;
            if (statusElem) statusElem.innerHTML = '<i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i> No se pudo acceder a la cámara.';
            Swal.fire({
                icon: "error",
                title: "Error de Cámara",
                text: "No se pudo acceder a la cámara. Asegúrate de otorgar permisos o utiliza el ingreso manual.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function procesarIngresoManual(e) {
        e.preventDefault();
        const input = document.getElementById("inputCodigoManual");
        const val = input.value ? input.value.trim() : "";
        if (!val) return;
        procesarCodigoAsignarPuntos(val);
        input.value = "";
    }

    function procesarCodigoAsignarPuntos(codigo) {
        const modalOverlay = document.getElementById("cameraModalOverlay");
        const modalTarget = (modalOverlay && modalOverlay.style.display !== "none") ? modalOverlay : document.body;

        fetch("{{ route('proveedor.asignar-puntos') }}", {
            method: "POST",
            headers: { 
                "Content-Type": "application/x-www-form-urlencoded",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: "codigo=" + encodeURIComponent(codigo)
        })
        .then(async res => {
            const rawText = await res.text();
            let data = null;
            try {
                data = JSON.parse(rawText);
            } catch (e) {
                data = null;
            }

            const message = (data && data.message) ? data.message : rawText;

            const isSuccess = res.ok && (
                (data && data.ok === true) || 
                rawText.includes('✅') || 
                rawText.toLowerCase().includes('puntos asignados')
            );

            const isCooldown = (res.status === 429) || 
                (data && data.cooldown) || 
                rawText.toLowerCase().includes('esperar 2 minutos') || 
                rawText.toLowerCase().includes('cooldown');

            if (isCooldown) {
                Swal.fire({
                    target: modalTarget,
                    icon: "info",
                    title: "⏳ Tiempo de Espera (2 Minutos)",
                    text: message,
                    confirmButtonColor: "#f97316",
                    confirmButtonText: "Entendido"
                });
            } else if (isSuccess) {
                Swal.fire({
                    target: modalTarget,
                    icon: "success",
                    title: "🟢 ¡Puntos Otorgados!",
                    text: message,
                    confirmButtonColor: "#f97316",
                    confirmButtonText: "Entendido"
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    target: modalTarget,
                    icon: "warning",
                    title: "⚠️ Atención",
                    text: message,
                    confirmButtonColor: "#f97316"
                });
            }
        })
        .catch(err => {
            console.error("Error en fetch de asignar puntos:", err);
            Swal.fire({
                target: modalTarget,
                icon: "warning",
                title: "Error de Conexión",
                text: "Ocurrió un problema de comunicación con el servidor.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function toggleProspecto(idRegistro, btn) {
        fetch("{{ route('proveedor.toggle-prospecto') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: "id_registro=" + encodeURIComponent(idRegistro)
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok) {
                const row = document.getElementById("history-row-" + idRegistro);
                const tagBadge = document.getElementById("badge-prospect-tag-" + idRegistro);
                const icon = btn.querySelector("i");
                const span = btn.querySelector("span");
                const numElem = document.getElementById("numProspectos");

                let currentCount = parseInt(numElem ? numElem.innerText.replace(/\D/g, '') : '0') || 0;

                const isLightTheme = document.documentElement.getAttribute("data-theme") === "light";

                if (data.es_prospecto) {
                    btn.style.background = isLightTheme ? "#fffbeb" : "rgba(217, 119, 6, 0.18)";
                    btn.style.borderColor = "#d97706";
                    btn.style.color = "#d97706";
                    if (icon) { icon.className = "bi bi-star-fill"; icon.style.color = "#d97706"; }
                    if (span) span.innerText = "Prospecto ⭐";
                    if (row) {
                        row.setAttribute("data-prospecto", "1");
                    }
                    if (tagBadge) tagBadge.style.display = "inline-flex";

                    currentCount++;
                    if (numElem) numElem.innerText = currentCount;

                    SwalToast.fire({
                        icon: 'success',
                        title: '⭐ ¡Marcado como prospecto!'
                    });
                } else {
                    btn.style.background = isLightTheme ? "#f1f5f9" : "rgba(148, 163, 184, 0.12)";
                    btn.style.borderColor = isLightTheme ? "#cbd5e1" : "rgba(148, 163, 184, 0.3)";
                    btn.style.color = "#64748b";
                    if (icon) { icon.className = "bi bi-star"; icon.style.color = "inherit"; }
                    if (span) span.innerText = "Marcar Prospecto";
                    if (row) {
                        row.setAttribute("data-prospecto", "0");
                    }
                    if (tagBadge) tagBadge.style.display = "none";

                    currentCount = Math.max(0, currentCount - 1);
                    if (numElem) numElem.innerText = currentCount;

                    SwalToast.fire({
                        icon: 'info',
                        title: 'Desmarcado de prospectos'
                    });
                }
            } else {
                Swal.fire({ icon: "error", title: "Atención", text: data.message || "Error al actualizar." });
            }
        })
        .catch(err => {
            console.error("Error al cambiar estado de prospecto:", err);
        });
    }

    function filtrarHistorial(tipo) {
        const rows = document.querySelectorAll(".history-card-row");
        const btnAll = document.getElementById("btnFilterAll");
        const btnProspects = document.getElementById("btnFilterProspects");
        const isLightTheme = document.documentElement.getAttribute("data-theme") === "light";

        if (tipo === 'prospectos') {
            if (btnAll) { 
                btnAll.style.background = "transparent"; 
                btnAll.style.color = isLightTheme ? "#64748b" : "#94a3b8"; 
                btnAll.style.border = isLightTheme ? "1px solid #cbd5e1" : "1px solid rgba(255,255,255,0.15)"; 
            }
            if (btnProspects) { 
                btnProspects.style.background = "#d97706"; 
                btnProspects.style.color = "#ffffff"; 
                btnProspects.style.borderColor = "#d97706"; 
            }

            rows.forEach(r => {
                if (r.getAttribute("data-prospecto") === "1") {
                    r.style.display = "flex";
                } else {
                    r.style.display = "none";
                }
            });
        } else {
            if (btnAll) { 
                btnAll.style.background = "#f97316"; 
                btnAll.style.color = "#ffffff"; 
                btnAll.style.border = "none"; 
            }
            if (btnProspects) { 
                btnProspects.style.background = "rgba(217,119,6,0.15)"; 
                btnProspects.style.color = "#d97706"; 
                btnProspects.style.borderColor = "rgba(217,119,6,0.5)"; 
            }

            rows.forEach(r => {
                r.style.display = "flex";
            });
        }
    }
</script>
@endsection
