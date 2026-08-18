@extends('layouts.app')

<style>
    /* RESPONSIVIDAD INTELIGENTE DE DETALLE DE CLASE CON ENCABEZADO TIPO TOPBAR FULL-WIDTH */
    @media (min-width: 992px) {
        .actividad-show-grid {
            display: grid !important;
            grid-template-columns: 1fr minmax(300px, 330px) !important;
            gap: 20px !important;
            align-items: start !important;
        }
        .actividad-sidebar-desktop {
            display: flex !important;
            flex-direction: column !important;
            gap: 14px !important;
            position: sticky !important;
            top: 20px !important;
        }
    }

    @media (max-width: 991px) {
        .actividad-show-grid {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }
        .actividad-sidebar-desktop {
            display: none !important;
        }
    }

    /* RESPONSIVIDAD INTELIGENTE TOPBAR Y CONTROLES MÓVILES */
    @media (max-width: 768px) {
        .actividad-topbar-header {
            margin: -16px -16px 16px -16px !important;
            padding: 14px 16px !important;
            border-radius: 0 0 16px 16px !important;
        }
        .topbar-container-flex {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .topbar-top-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
        }
        .topbar-event-name {
            font-size: 10px !important;
            max-width: 60% !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        .topbar-back-btn {
            padding: 5px 10px !important;
            font-size: 11px !important;
        }
        .topbar-title {
            font-size: 17px !important;
            line-height: 1.3 !important;
            margin: 4px 0 8px 0 !important;
            word-break: break-word !important;
        }
        .topbar-badges-container {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)) !important;
            gap: 6px !important;
            width: 100% !important;
        }
        .topbar-badge {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            font-size: 10.5px !important;
            padding: 5px 10px !important;
            border-radius: 20px !important;
            white-space: nowrap !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .scan-form-mobile {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }
        .scan-form-mobile button {
            width: 100% !important;
            justify-content: center !important;
        }
    }


    /* ALTERNANCIA ESTRICTA VISTA MÓVIL Y ESCRITORIO */
    @media (max-width: 767.98px) {
        .asistencia-vista-movil {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }
        .asistencia-vista-desktop {
            display: none !important;
        }
    }
    @media (min-width: 768px) {
        .asistencia-vista-movil {
            display: none !important;
        }
        .asistencia-vista-desktop {
            display: block !important;
        }
    }


    


    /* MODAL CÁMARA FULLSCREEN */
    .qr-fullscreen-active {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 9999999 !important;
        background: #020617 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        padding: 16px !important;
        box-sizing: border-box !important;
        margin: 0 !important;
    }
    .qr-fullscreen-active #qrReaderContainer {
        width: 100% !important;
        max-width: 550px !important;
        flex: 1 !important;
        min-height: calc(100vh - 120px) !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        border: 3px solid var(--accent-gold) !important;
        box-shadow: 0 0 35px rgba(249, 115, 22, 0.5) !important;
        background: #000 !important;
        margin: 0 auto !important;
    }


    /* GARANTIZAR QUE SWEETALERT2 APAREZCA POR ENCIMA DE LA CÁMARA EN PANTALLA COMPLETA */
    .swal2-container {
        z-index: 99999999 !important;
    }
    .swal2-popup.swal2-toast {
        border: 2px solid var(--accent-gold) !important;
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.85) !important;
        font-size: 14.5px !important;
    }


    /* VIDEO DE CÁMARA AL 100% Y RETÍCULA CUADRADA 1:1 */
    #cameraModalOverlay #qrReaderContainer {
        width: 100% !important;
        max-width: 550px !important;
        flex: 1 !important;
        height: 100% !important;
        border-radius: 16px !important;
        overflow: hidden !important;
        border: 3px solid var(--accent-gold) !important;
        box-shadow: 0 0 35px rgba(249, 115, 22, 0.5) !important;
        background: #000 !important;
        margin: 0 auto !important;
        position: relative !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    #cameraModalOverlay #qrReaderContainer video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        border-radius: 14px !important;
    }
    #cameraModalOverlay #qrReaderContainer canvas {
        display: none !important;
    }


    /* ESTILO MINIMALISTA, PLANO Y CUADRADO 100% ANCHO */
    @media (max-width: 768px) {
        .actividad-topbar-header {
            margin: -16px -16px 12px -16px !important;
            padding: 14px 16px !important;
            border-radius: 0 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            background: #0f172a !important;
        }
        .actividad-main-padding {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .actividad-card-container {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            padding: 0 4px !important;
        }
        .topbar-badge {
            border-radius: 4px !important;
        }
    }

</style>

@section('content')
@php
    $isSingleSlotView = (isset($selectedSlot) && $selectedSlot);
@endphp

<!-- ENCABEZADO MINIMALISTA Y ELEGANTE TIPO ARCHITECTURAL TOPBAR -->
<div class="actividad-topbar-header" style="background:#0f172a; border-bottom:1px solid rgba(255,255,255,0.08); padding:16px 20px; margin-bottom:16px;">
    <div style="max-width:1600px; margin:0 auto; display:flex; flex-direction:column; gap:10px;">
        
        <!-- Fila Superior: Nombre del Evento + Único Botón de Regreso -->
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
            <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:800; color:var(--accent-gold); display:flex; align-items:center; gap:6px;">
                <i class="bi bi-calendar-event-fill"></i> Evento: {{ optional($evento)->name_evento ?? 'Evento #'.$actividad->ID_Evento }}
            </div>
            <div>
                <a href="{{ $backUrl ?? route('eventos.show', [$evento, 'active_tab' => $isSingleSlotView ? 'tab-agenda' : 'tab-actividades']) }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:6px; font-weight:800; font-size:11.5px; padding:6px 12px; border-radius:4px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px;">
                    <i class="bi bi-arrow-left"></i> Agenda
                </a>
            </div>
        </div>

        <!-- Título de la Actividad -->
        <h2 style="margin:2px 0; font-weight:800; font-size:19px; color:var(--text-primary); display:flex; align-items:center; gap:8px; letter-spacing:-0.3px;">
            <i class="bi bi-bar-chart-line-fill" style="color:var(--accent-gold);"></i> 
            {{ $isSingleSlotView ? $selectedSlot->Actividad : $actividad->Actividad }}
        </h2>

        <!-- Tira Única de Metadatos Cuadrada y Elegante -->
        <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap; margin-top:2px;">
            @if($isSingleSlotView)
                <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:4px; text-transform:uppercase; letter-spacing:0.5px;">
                    <i class="bi bi-clock-fill"></i> {{ $selectedSlot->Horario }}
                </div>
                <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.3); color:var(--accent-gold); font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:4px;">
                    <i class="bi bi-calendar-event-fill"></i> {{ \Carbon\Carbon::parse($selectedSlot->Fecha)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                </div>
                <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:4px;">
                    <i class="bi bi-geo-alt-fill"></i> {{ $selectedSlot->Salon ?: 'Salón General' }}
                </div>
                <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:var(--text-primary); font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:4px;">
                    <i class="bi bi-people-fill" style="color:var(--accent-gold);"></i> {{ $totalInscritos }}/{{ $capacidadTotal }} AFORO
                </div>
            @endif
            <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.35); color:var(--accent-gold); font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:4px;">
                <i class="bi bi-star-fill"></i> {{ $actividad->Puntos_Default }} PTS
            </div>
        </div>

    </div>
</div>

<div class="actividad-main-container" style="color:var(--text-primary); max-width: 1600px; margin: 0 auto; width:100%;">

    @if(session('success'))
        <div style="background:rgba(34,197,94,0.15); color:#4ade80; padding:12px 18px; border-radius:4px; margin-bottom:16px; border:1px solid rgba(34,197,94,0.3); font-weight:600; display:flex; align-items:center; gap:10px;">
            <i class="bi bi-check-circle-fill" style="font-size:18px;"></i> {{ session('success') }}
        </div>
    @endif

    {{-- LAYOUT PRINCIPAL: EN ESCRITORIO SIDEBAR COMPACTO A LA DERECHA (320px) Y TABLA PRINCIPAL (1fr) --}}
    <div class="actividad-show-grid">

        {{-- COLUMNA IZQUIERDA (PRINCIPAL): Panel de Control de Asistencia y Escáner --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div style="background:transparent; border:none; padding:0;">
                
                <div style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:10px; margin-bottom:14px;">
                    <h3 style="margin:0; font-size:15px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-people-fill" style="color:var(--accent-gold);"></i> 
                        {{ $isSingleSlotView ? 'REGISTRO DE ASISTENCIA — SESIÓN '.$selectedSlot->Horario : 'REGISTRO Y CONTROL DE ASISTENCIA' }}
                    </h3>
                </div>

                {{-- Buscador en tiempo real --}}
                <div class="search-container" style="position:relative; margin-bottom:12px;">
                    <i class="bi bi-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--accent-gold); font-size:14px;"></i>
                    <input type="text" id="busqueda" placeholder="Buscar asistente por nombre, teléfono, empresa..." class="form-control" style="padding-left:42px; font-size:13.5px; background:var(--bg-primary); border:1px solid var(--border); border-radius:4px; color:var(--text-primary);">
                </div>

                {{-- PANEL DUAL DE ENTRADA DE ASISTENCIA: CÁMARA QR E INGRESO MANUAL/USB --}}
                <div style="margin-bottom:14px;">
                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
                        <button type="button" id="toggleQR" class="btn btn-primary" style="flex:1; min-width:200px; font-weight:800; padding:10px 16px; border-radius:4px; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 12px rgba(249,115,22,0.3);">
                            <i class="bi bi-camera-fill" style="font-size:17px;"></i> Escáner QR con Cámara
                        </button>
                    </div>

                    <div id="manualInputContainer">
                        <form id="scanForm" onsubmit="return false;" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:200px;">
                                <i class="bi bi-upc-scan" style="color:var(--accent-gold); font-size:18px;"></i>
                                <input type="text" id="idParticipante" placeholder="Ingresa ID, Teléfono o Código (ej: ID1)..." class="form-control" style="flex:1; background:var(--bg-primary); border:1px solid var(--accent-gold); font-size:13.5px; color:var(--text-primary); border-radius:4px;">
                            </div>
                            <button id="btnProcesarScan" class="btn btn-primary" type="submit" style="font-weight:800; padding:7px 18px; border-radius:4px; text-transform:uppercase; font-size:12.5px;"><i class="bi bi-check-lg"></i> Procesar</button>
                        </form>
                    </div>
                </div>

                {{-- MODAL FULLSCREEN DE CÁMARA QR (UBICADO DIRECTAMENTE EN BODY) --}}
                <div id="cameraModalOverlay" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:#020617; z-index:9999999; flex-direction:column; padding:14px; box-sizing:border-box;">
                    <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(15,23,42,0.95); backdrop-filter:blur(12px); padding:10px 16px; border-radius:4px; border:1px solid rgba(255,255,255,0.12); margin-bottom:12px; max-width:550px; width:100%; margin:0 auto 12px auto;">
                        <span id="qrStatusText" style="color:#ffffff; font-weight:700; font-size:13.5px; display:flex; align-items:center; gap:8px;">
                            <i class="bi bi-camera-video-fill" style="color:var(--accent-gold);"></i> Iniciando cámara...
                        </span>
                        <button type="button" onclick="apagarCamaraQR()" class="btn btn-danger btn-sm" style="font-weight:800; border-radius:4px; padding:6px 14px; font-size:12.5px; text-transform:uppercase;">
                            <i class="bi bi-x-lg"></i> Cerrar Cámara
                        </button>
                    </div>

                    <div style="flex:1; width:100%; max-width:550px; margin:0 auto; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; border-radius:12px; border:3px solid var(--accent-gold); box-shadow:0 0 35px rgba(249,115,22,0.5); background:#000;">
                        <div id="qrReaderContainer" style="width:100%; height:100%; min-height:280px;"></div>
                    </div>
                </div>

                {{-- Tabla dinámica de asistencia --}}
                <div id="resultado" style="display:flex; flex-direction:column; min-height:300px;">
                    <!-- Se carga dinámicamente -->
                </div>

            </div>
        </div>

        {{-- COLUMNA DERECHA: SIDEBAR COMPACTO DE INFORMACIÓN Y KPIS (SÓLO VISIBLE EN ESCRITORIO >= 992px) --}}
        <div class="actividad-sidebar-desktop">
            <div style="background:var(--bg-secondary); border-radius:6px; padding:18px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 4px 16px rgba(0,0,0,0.2);">
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:800; color:var(--accent-gold); margin-bottom:12px; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-info-circle-fill"></i> Detalle de la Clase
                </div>

                @if($isSingleSlotView)
                    <div style="margin-bottom:12px;">
                        <div style="font-size:10.5px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Horario</div>
                        <div style="font-size:17px; font-weight:800; color:var(--text-primary);"><i class="bi bi-clock-fill" style="color:#38bdf8;"></i> {{ $selectedSlot->Horario }}</div>
                    </div>

                    <div style="margin-bottom:12px;">
                        <div style="font-size:10.5px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Fecha y Día</div>
                        <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);"><i class="bi bi-calendar-event-fill" style="color:var(--accent-gold);"></i> {{ \Carbon\Carbon::parse($selectedSlot->Fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}</div>
                    </div>

                    <div style="margin-bottom:12px;">
                        <div style="font-size:10.5px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Salón / Ubicación</div>
                        <div style="font-size:13.5px; font-weight:700; color:#4ade80;"><i class="bi bi-geo-alt-fill"></i> {{ $selectedSlot->Salon ?: 'Salón General' }}</div>
                    </div>
                @endif

                <div style="border-top:1px solid rgba(255,255,255,0.08); padding-top:12px; margin-top:12px;">
                    <div style="font-size:10.5px; color:var(--text-secondary); text-transform:uppercase; font-weight:700; margin-bottom:4px;">Asistencia en esta clase</div>
                    <div style="font-size:20px; font-weight:800; color:var(--text-primary);">
                        {{ $totalInscritos }} <span style="font-size:12px; color:var(--text-muted); font-weight:600;">/ {{ $capacidadTotal }} aforo</span>
                    </div>
                    <div class="progress" style="height:6px; background:rgba(255,255,255,0.08); border-radius:3px; margin-top:6px; overflow:hidden;">
                        <div class="progress-bar" style="width: {{ $porcentajeGlobal }}%; background:linear-gradient(90deg, var(--accent-gold), #f97316);"></div>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:14px; padding-top:10px; border-top:1px solid rgba(255,255,255,0.08); font-size:12px;">
                    <div>
                        <div style="font-size:10px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Puntos</div>
                        <div style="font-weight:800; color:var(--accent-gold);"><i class="bi bi-star-fill"></i> {{ $actividad->Puntos_Default }} pts</div>
                    </div>
                    <div>
                        <div style="font-size:10px; color:var(--text-secondary); text-transform:uppercase; font-weight:700;">Acceso</div>
                        <div style="font-weight:700; color:var(--text-primary);"><i class="bi bi-globe"></i> Pública</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let isProcessingScan = false;
    let html5Scanner = null;
    let isHtml5Scanning = false;
    let lastCameraScanCode = null;
    let lastCameraScanTime = 0;

    const SwalToast = Swal.mixin({
        toast: true,
        position: "top",
        showConfirmButton: false,
        timer: 2200,
        timerProgressBar: true,
        background: "#0f172a",
        color: "#ffffff"
    });

    document.addEventListener("DOMContentLoaded", function () {
        cargarAsistencia();

        let timer;
        const busquedaInput = document.getElementById("busqueda");
        if (busquedaInput) {
            busquedaInput.addEventListener("keyup", function () {
                clearTimeout(timer);
                timer = setTimeout(cargarAsistencia, 300);
            });
        }

        const scanForm = document.getElementById("scanForm");
        if (scanForm) {
            scanForm.addEventListener("submit", function(e) {
                e.preventDefault();
                procesarEscanerManual();
            });
        }

        const idPartInput = document.getElementById("idParticipante");
        if (idPartInput) {
            setTimeout(() => idPartInput.focus(), 300);
        }

        const btnProcesar = document.getElementById("btnProcesarScan");
        if (btnProcesar) {
            btnProcesar.addEventListener("click", function(e) {
                e.preventDefault();
                procesarEscanerManual();
            });
        }

        const toggleQRBtn = document.getElementById("toggleQR");
        if (toggleQRBtn) {
            toggleQRBtn.addEventListener("click", abrirCamaraFullscreen);
        }
    });

    function cargarAsistencia() {
        const q = document.getElementById("busqueda") ? document.getElementById("busqueda").value : "";
        const url = `{{ route('actividades.buscar', $actividad->ID) }}?q=${encodeURIComponent(q)}&slot_id={{ $isSingleSlotView ? $selectedSlot->ID : '' }}`;
        
        fetch(url)
            .then(res => res.text())
            .then(html => {
                const resElem = document.getElementById("resultado");
                if (resElem) resElem.innerHTML = html;
            })
            .catch(err => console.error("Error al cargar asistencia:", err));
    }

    function abrirCamaraFullscreen() {
        const modal = document.getElementById("cameraModalOverlay");
        if (!modal) return;

        if (modal.parentNode !== document.body) {
            document.body.appendChild(modal);
        }

        modal.style.display = "flex";
        encenderCamaraQR();
    }

    function encenderCamaraQR() {
        if (isHtml5Scanning) return;

        const statusElem = document.getElementById("qrStatusText");
        if (statusElem) statusElem.innerHTML = '<i class="bi bi-camera-video-fill" style="color:var(--accent-gold);"></i> Solicitando cámara...';

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

                if (statusElem) statusElem.innerText = `¡Escaneado: ${decodedText}!`;
                procesarCodigoCamara(decodedText);
            },
            (errorMessage) => {
                // Cuadros sin QR, ignorar
            }
        )
        .then(() => {
            isHtml5Scanning = true;
            if (statusElem) statusElem.innerHTML = '<i class="bi bi-camera-video-fill" style="color:var(--accent-gold);"></i> Escaneando QR... Apunta la cámara al código';
        })
        .catch((err) => {
            isHtml5Scanning = false;
            console.error("Error al iniciar cámara HTML5:", err);
            let errMsg = "No se pudo acceder a la cámara.";
            const s = String(err || "");

            if (location.protocol !== "https:" && !location.hostname.match(/^(localhost|127\.0\.0\.1)$/)) {
                errMsg = "Atención: Se requiere conexión segura HTTPS para acceder a la cámara en dispositivos remotos.";
            } else if (s.includes("NotAllowedError") || s.includes("Permission")) {
                errMsg = "Permiso de cámara denegado. Permite el acceso en la barra del navegador.";
            } else if (s.includes("NotFoundError")) {
                errMsg = "No se encontró ninguna cámara disponible en el dispositivo.";
            }

            if (statusElem) statusElem.innerText = errMsg;

            Swal.fire({
                target: document.getElementById('cameraModalOverlay') || 'body',
                icon: "warning",
                title: "Acceso a Cámara",
                text: errMsg,
                confirmButtonColor: "#f97316"
            });
        });
    }

    function apagarCamaraQR() {
        const modal = document.getElementById("cameraModalOverlay");
        if (modal) {
            modal.style.display = "none";
        }

        if (!html5Scanner || !isHtml5Scanning) return;

        html5Scanner.stop().then(() => {
            isHtml5Scanning = false;
        }).catch(err => {
            console.error("Error al apagar cámara:", err);
            isHtml5Scanning = false;
        });
    }

    function procesarCodigoCamara(codigo) {
        if (!codigo) return;

        const modalTarget = document.getElementById('cameraModalOverlay');

        fetch(`{{ route('actividades.asistencia', $actividad->ID) }}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                id_participante: codigo,
                horario: "{{ $isSingleSlotView ? $selectedSlot->ID : '' }}"
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.ok || data.success) {
                Swal.fire({
                    target: modalTarget || 'body',
                    icon: "success",
                    title: "¡Asistencia Registrada!",
                    text: data.msg || data.message || "Asistencia registrada correctamente.",
                    confirmButtonColor: "#f97316",
                    timer: 2500,
                    timerProgressBar: true
                });
                cargarAsistencia();
            } else {
                Swal.fire({
                    target: modalTarget || 'body',
                    icon: "warning",
                    title: "Aviso / Incidencia",
                    text: data.msg || data.message || "Código no válido o participante no registrado.",
                    confirmButtonColor: "#f97316",
                    timer: 3500,
                    timerProgressBar: true
                });
            }
        })
        .catch(err => {
            console.error("Error al procesar QR de cámara:", err);
            Swal.fire({
                target: modalTarget || 'body',
                icon: "error",
                title: "Error de Red",
                text: "No se pudo conectar con el servidor.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function procesarEscanerManual() {
        if (isProcessingScan) return;

        const input = document.getElementById("idParticipante");
        if (!input) return;
        const val = input.value.trim();

        if (!val) {
            return;
        }

        isProcessingScan = true;
        input.value = "";

        fetch(`{{ route('actividades.asistencia', $actividad->ID) }}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                id_participante: val,
                horario: "{{ $isSingleSlotView ? $selectedSlot->ID : '' }}"
            })
        })
        .then(res => res.json())
        .then(data => {
            isProcessingScan = false;
            setTimeout(() => input.focus(), 150);

            if(data.ok || data.success) {
                SwalToast.fire({
                    icon: "success",
                    title: "¡Asistencia Confirmada!",
                    text: data.msg || data.message || "Asistencia registrada correctamente."
                });
                cargarAsistencia();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Aviso / Incidencia",
                    text: data.msg || data.message || "Código no válido o participante no registrado.",
                    confirmButtonColor: "#f97316",
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        })
        .catch(err => {
            isProcessingScan = false;
            setTimeout(() => input.focus(), 150);
            console.error("Error al procesar escáner:", err);
            Swal.fire({
                icon: "error",
                title: "Error de Red",
                text: "No se pudo conectar con el servidor.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function btnMarcarAsistencia(idParticipante) {
        marcarAsistencia(idParticipante, 1);
    }

    function toggleAsistenciaManual(idParticipante, estadoActual) {
        const actionTitle = estadoActual ? "¿Quitar asistencia?" : "¿Marcar asistencia?";
        const actionText = estadoActual 
            ? "Se eliminará el registro de asistencia de este participante en esta actividad." 
            : "Se confirmará la asistencia para este participante.";

        Swal.fire({
            title: actionTitle,
            text: actionText,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: estadoActual ? "#ef4444" : "#22c55e",
            cancelButtonColor: "#64748b",
            confirmButtonText: estadoActual ? "Sí, quitar" : "Sí, marcar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                marcarAsistencia(idParticipante, estadoActual ? 0 : 1);
            }
        });
    }

    function btnInscribirYAsistir(idParticipante) {
        fetch(`{{ route('actividades.inscribir', $actividad->ID) }}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                id_participante: idParticipante,
                horario: "{{ $isSingleSlotView ? $selectedSlot->ID : '' }}"
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.ok || data.success) {
                SwalToast.fire({
                    icon: "success",
                    title: "¡Inscrito y Confirmado!",
                    text: data.msg || data.message || "Inscripción registrada."
                });
                cargarAsistencia();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Incidencia al inscribir",
                    text: data.msg || data.message || "Error al procesar la inscripción.",
                    confirmButtonColor: "#f97316"
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: "error",
                title: "Error de Servidor",
                text: "Ocurrió un problema al procesar la inscripción.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function marcarAsistencia(idParticipante, asistio) {
        const endpoint = asistio 
            ? `{{ route('actividades.asistencia', $actividad->ID) }}`
            : `{{ route('actividades.toggle-asistencia', $actividad->ID) }}`;

        fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ 
                id_participante: idParticipante, 
                asistio: asistio,
                horario: "{{ $isSingleSlotView ? $selectedSlot->ID : '' }}"
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.ok || data.success) {
                SwalToast.fire({
                    icon: "success",
                    title: asistio ? "¡Asistencia Marcada!" : "Asistencia Removida",
                    text: data.msg || data.message || "Registro actualizado."
                });
                cargarAsistencia();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención / Incidencia",
                    text: data.msg || data.message || "No se pudo actualizar el estado.",
                    confirmButtonColor: "#f97316"
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: "error",
                title: "Error de conexión",
                text: "Ocurrió un problema de comunicación.",
                confirmButtonColor: "#f97316"
            });
        });
    }

    function abrirModalRegistro() {
        document.getElementById("modalRegistro").style.display = "flex";
    }

    function cerrarModalRegistro() {
        document.getElementById("modalRegistro").style.display = "none";
    }

    function guardarModalRegistro(e) {
        e.preventDefault();
        const form = document.getElementById("formModalRegistro");
        const formData = new FormData(form);

        fetch(`{{ route('actividades.registro-rapido', $actividad->ID) }}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.ok || data.success) {
                cerrarModalRegistro();
                form.reset();
                Swal.fire({
                    icon: "success",
                    title: "¡Registro Exitoso!",
                    text: data.msg || data.message || "Participante registrado e inscrito correctamente.",
                    confirmButtonColor: "#f97316",
                    timer: 2500
                });
                cargarAsistencia();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error en Registro",
                    text: data.msg || data.message || "Comprueba los datos ingresados.",
                    confirmButtonColor: "#f97316"
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: "error",
                title: "Error de Servidor",
                text: "Ocurrió un problema al procesar el registro rápido.",
                confirmButtonColor: "#f97316"
            });
        });
    }
</script>
@endpush
@endsection
