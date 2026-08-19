@extends('layouts.app')

@section('title', 'Kiosko Tótem - Consulta de Puntos')

@push('styles')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* -------------------------------------------------------------
       TÓTEM KIOSK ULTRA RESPONSIVE STYLING (FULL SCREEN REPLACEMENT)
    ------------------------------------------------------------- */
    .kiosko-container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        box-sizing: border-box !important;
    }

    /* HEADER TÓTEM ULTRA MODERNO */
    .totem-header-banner {
        background: linear-gradient(135deg, #0b132b 0%, #1c2541 50%, #0f172a 100%);
        border: 1px solid rgba(251, 191, 36, 0.25);
        border-radius: 4px;
        padding: 16px 20px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .totem-header-flex {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .totem-brand-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .totem-icon-orb {
        width: 48px; height: 48px;
        border-radius: 4px;
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: #000;
        display: flex; align-items: center; justify-content: center;
        font-size: 24px;
        box-shadow: 0 0 18px rgba(251, 191, 36, 0.4);
        flex-shrink: 0;
    }

    .totem-main-title {
        font-size: 20px;
        font-weight: 900;
        letter-spacing: -0.3px;
        color: #ffffff;
        text-transform: uppercase;
        margin: 0;
        line-height: 1.1;
    }

    .totem-sub-title {
        font-size: 12px;
        font-weight: 700;
        color: #fbbf24;
        margin-top: 2px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .totem-meta-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .totem-clock-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 16px;
        font-weight: 800;
        color: #fbbf24;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(251, 191, 36, 0.35);
        padding: 5px 14px;
        border-radius: 4px;
    }

    .totem-event-badge {
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.35);
        color: #60a5fa;
        padding: 5px 12px;
        border-radius: 4px;
        font-weight: 800;
        font-size: 12.5px;
    }

    /* GRID TÓTEM */
    .totem-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        transition: opacity 0.3s ease;
    }

    .totem-grid.mode-single {
        grid-template-columns: 1fr !important;
    }

    @media (max-width: 768px) {
        .totem-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .totem-card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        padding: 20px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        box-sizing: border-box !important;
        width: 100% !important;
    }

    .totem-card-title {
        font-size: 18px;
        font-weight: 900;
        color: #ffffff;
        margin: 0 0 14px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        text-transform: uppercase;
    }

    /* ESCÁNER QR TÓTEM CÁMARA */
    .totem-scanner-container {
        position: relative;
        background: #000;
        border: 2px solid rgba(251, 191, 36, 0.4);
        border-radius: 4px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(251, 191, 36, 0.15);
    }

    #reader-kiosko {
        width: 100% !important;
        border: none !important;
        background: #000;
    }

    #reader-kiosko video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }

    .totem-laser-line {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #fbbf24, #38bdf8, #fbbf24, transparent);
        box-shadow: 0 0 15px #fbbf24, 0 0 25px #38bdf8;
        animation: laserScan 2.5s infinite linear;
        z-index: 10;
        pointer-events: none;
    }

    @keyframes laserScan {
        0% { top: 5%; }
        50% { top: 90%; }
        100% { top: 5%; }
    }

    .totem-scan-prompt {
        margin-top: 14px;
        background: rgba(251, 191, 36, 0.1);
        border: 1px solid rgba(251, 191, 36, 0.25);
        border-radius: 4px;
        padding: 12px 16px;
        text-align: center;
        color: #fbbf24;
        font-weight: 800;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    /* CAMPO DE LECTURA USB Y CÓDIGO */
    .totem-input-group {
        display: flex;
        gap: 10px;
        margin-top: 14px;
    }

    .totem-input {
        width: 100%;
        background: #090d16;
        border: 2px solid rgba(251, 191, 36, 0.3);
        border-radius: 4px;
        padding: 16px 18px;
        font-size: 18px;
        font-weight: 900;
        color: #ffffff;
        outline: none;
        box-sizing: border-box !important;
    }

    .totem-btn-action {
        background: linear-gradient(135deg, #fbbf24, #d97706);
        color: #000000;
        font-weight: 900;
        font-size: 16px;
        border: none;
        border-radius: 4px;
        padding: 0 24px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        box-shadow: 0 4px 14px rgba(251, 191, 36, 0.25);
    }

    /* RESULTADO EN TÓTEM REEMPLAZO A PANTALLA COMPLETA */
    .totem-result-card {
        display: none;
        background: linear-gradient(135deg, #0b132b 0%, #1e293b 100%);
        border: 2px solid #fbbf24;
        border-radius: 4px;
        padding: 24px;
        color: #ffffff;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.7);
        box-sizing: border-box !important;
        width: 100% !important;
        overflow: hidden !important;
        animation: totemPopIn 0.35s ease;
    }

    @keyframes totemPopIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .totem-part-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 16px;
        margin-bottom: 20px;
    }

    .totem-avatar-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .totem-avatar-badge {
        width: 64px; height: 64px;
        border-radius: 4px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        color: #000;
        font-size: 28px;
        font-weight: 900;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(251, 191, 36, 0.4);
    }

    .totem-part-name {
        font-size: 26px;
        font-weight: 900;
        color: #ffffff;
        margin: 0;
        line-height: 1.2;
    }

    .totem-points-hero {
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
        color: #000000;
        padding: 14px 28px;
        border-radius: 4px;
        font-size: 36px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
    }

    .totem-stats-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .totem-stat-box {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        padding: 14px 8px;
        text-align: center;
    }

    .totem-stat-num {
        font-size: 24px;
        font-weight: 900;
        color: #fbbf24;
    }

    .totem-stat-txt {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .totem-reset-bar {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(251, 191, 36, 0.3);
        border-radius: 4px;
        padding: 12px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 16px;
    }

    .totem-progress-container {
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        margin-top: 10px;
        overflow: hidden;
    }

    .totem-progress-fill {
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, #fbbf24, #38bdf8);
        transition: width 1s linear;
    }

    .totem-table-wrap {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 4px;
    }

    .totem-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .totem-table th {
        background: rgba(255,255,255,0.06);
        color: #94a3b8;
        padding: 10px 12px;
        text-align: left;
        font-weight: 700;
    }

    .totem-table td {
        padding: 10px 12px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
</style>
@endpush

@section('content')
<div class="kiosko-container">

    <!-- HEADER TÓTEM EVENTO -->
    <div class="totem-header-banner">
        <div class="totem-header-flex">
            
            <div class="totem-brand-wrap">
                <div class="totem-icon-orb">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <div>
                    <h1 class="totem-main-title">CONSULTA DE PUNTOS</h1>
                    <div class="totem-sub-title">✨ MÓDULO INTERACTIVO</div>
                </div>
            </div>

            <div class="totem-meta-wrap">
                @if(auth()->user()->esAdmin() || ($tipoKiosko ?? 'hibrido') === 'hibrido')
                    <div class="kiosko-mode-pills">
                        <button type="button" class="kmode-pill {{ ($tipoKiosko ?? 'hibrido') === 'hibrido' ? 'active' : '' }}" onclick="setModoKiosko('hibrido')" id="btnModoHibrido">
                            <i class="bi bi-arrows-angle-expand"></i> Híbrido
                        </button>
                        <button type="button" class="kmode-pill {{ ($tipoKiosko ?? 'hibrido') === 'camara' ? 'active' : '' }}" onclick="setModoKiosko('camara')" id="btnModoCamara">
                            <i class="bi bi-camera"></i> Cámara
                        </button>
                        <button type="button" class="kmode-pill {{ ($tipoKiosko ?? 'hibrido') === 'codigo' ? 'active' : '' }}" onclick="setModoKiosko('codigo')" id="btnModoCodigo">
                            <i class="bi bi-search"></i> Código ID
                        </button>
                    </div>
                @else
                    <span style="background: rgba(251, 191, 36, 0.15); border: 1px solid rgba(251, 191, 36, 0.35); color:#fbbf24; padding:5px 12px; border-radius:4px; font-weight:800; font-size:12.5px; display:inline-flex; align-items:center; gap:6px;">
                        @if(($tipoKiosko ?? 'hibrido') === 'camara')
                            <i class="bi bi-camera-fill"></i> MODO: ESCÁNER QR
                        @else
                            <i class="bi bi-search"></i> MODO: LECTOR CÓDIGO USB
                        @endif
                    </span>
                @endif

                <div class="totem-clock-badge" id="kioskoClock">00:00:00</div>

                @if($evento)
                    <div class="totem-event-badge">
                        <i class="bi bi-calendar-event"></i> {{ $evento->name_evento }}
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- GRID PRINCIPAL DEL TÓTEM (ESCÁNER / CÁMARA) -->
    <div class="totem-grid {{ ($tipoKiosko ?? 'hibrido') !== 'hibrido' ? 'mode-single' : '' }}" id="kioskoGrid">
        
        <!-- PANEL CÁMARA TÓTEM -->
        <div class="totem-card" id="cardKioskoCamara" style="display: {{ ($tipoKiosko ?? 'hibrido') === 'codigo' ? 'none' : 'block' }};">
            <h3 class="totem-card-title">
                <i class="bi bi-camera-fill" style="color:#fbbf24;"></i> Escáner QR de Gafete
            </h3>

            <div class="totem-scanner-container">
                <div class="totem-laser-line" id="totemLaserLine"></div>
                <div id="reader-kiosko"></div>
            </div>

            <div class="totem-scan-prompt">
                <i class="bi bi-qr-code"></i>
                <span>ACERCA EL CÓDIGO QR DE TU GAFETE A LA CÁMARA</span>
            </div>
        </div>

        <!-- PANEL BÚSQUEDA MANUAL / LECTOR DE CÓDIGO BARRAS USB -->
        <div class="totem-card" id="cardKioskoCodigo" style="display: {{ ($tipoKiosko ?? 'hibrido') === 'camara' ? 'none' : 'block' }};">
            <h3 class="totem-card-title">
                <i class="bi bi-search" style="color:#38bdf8;"></i> Lectura por Código / Gafete
            </h3>
            <p style="font-size:13px; color:#94a3b8; margin:0 0 12px 0;">
                Escanea tu gafete con la pistola lectora USB o ingresa tu ID / RFC en pantalla.
            </p>

            <form id="formKioskoBuscar" onsubmit="procesarConsultaKiosko(event)">
                <label style="font-size:11.5px; font-weight:800; text-transform:uppercase; color:#fbbf24;">CÓDIGO QR O ID DE PARTICIPANTE:</label>
                <div class="totem-input-group">
                    <input type="text" id="kioskoInputCodigo" class="totem-input" placeholder="Escanea o ingresa tu ID..." autofocus autocomplete="off">
                    <button type="submit" class="totem-btn-action">
                        <i class="bi bi-search"></i> CONSULTAR
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- TARJETA DE RESULTADO DE IMPACTO EN TÓTEM (REEMPLAZA EL ESCÁNER A PANTALLA COMPLETA) -->
    <div id="participanteResultCard" class="totem-result-card">
        
        <div class="totem-part-header">
            <div class="totem-avatar-wrap">
                <div class="totem-avatar-badge" id="resAvatar">A</div>
                <div>
                    <h2 class="totem-part-name" id="resNombre">Alex Casillas</h2>
                    <div style="font-size:14px; color:#94a3b8; margin-top:3px;">
                        <span id="resRFC" style="font-weight:800; color:#fbbf24;">RFC: ---</span> | 
                        <span id="resCategoria" style="font-weight:700; color:#38bdf8;">Participante</span>
                    </div>
                </div>
            </div>

            <!-- HERO PUNTOS ACUMULADOS -->
            <div class="totem-points-hero">
                <i class="bi bi-star-fill"></i>
                <span id="resPuntosTotales">0</span>
                <span style="font-size:18px; font-weight:900; opacity:0.85;">PUNTOS</span>
            </div>
        </div>

        <!-- STATS SECUNDARIAS EN 3 COLUMNAS -->
        <div class="totem-stats-row">
            <div class="totem-stat-box">
                <div class="totem-stat-num" id="resPuntosIndiv">0</div>
                <div class="totem-stat-txt">Individuales</div>
            </div>
            <div class="totem-stat-box">
                <div class="totem-stat-num" id="resPuntosGrup">0</div>
                <div class="totem-stat-txt">Grupales (RFC)</div>
            </div>
            <div class="totem-stat-box">
                <div class="totem-stat-num" id="resTotalVisitas">0</div>
                <div class="totem-stat-txt">Stands / Visitas</div>
            </div>
        </div>

        <!-- TABLA HISTORIAL PUNTOS -->
        <div>
            <h4 style="font-size:15px; font-weight:900; color:#fbbf24; margin:0 0 10px 0; text-transform:uppercase; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-clock-history"></i> Historial Reciente de Visitas a Stands
            </h4>
            <div class="totem-table-wrap">
                <table class="totem-table">
                    <thead>
                        <tr>
                            <th>Stand / Origen</th>
                            <th>Tipo</th>
                            <th style="text-align:right;">Puntos</th>
                            <th style="text-align:right;">Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="resHistorialBody">
                        <!-- JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BARRA DE REINICIO DE PANTALLA -->
        <div class="totem-reset-bar">
            <div style="font-size:14px; font-weight:800; color:#ffffff;">
                <i class="bi bi-hourglass-split" style="color:#fbbf24;"></i>
                Limpiando pantalla en <span id="resetCountdown" style="color:#fbbf24; font-size:20px; font-weight:900;">10</span> seg...
            </div>
            <button type="button" onclick="resetPantallaKiosko()" class="totem-btn-action" style="padding: 10px 20px; font-size: 14px;">
                <i class="bi bi-arrow-counterclockwise"></i> NUEVA CONSULTA
            </button>
        </div>
        <div class="totem-progress-container">
            <div class="totem-progress-fill" id="totemProgressFill"></div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const str = now.toLocaleTimeString('es-MX', { hour12: true });
        const el = document.getElementById('kioskoClock');
        if (el) el.innerText = str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    let currentModoKiosko = "{{ $tipoKiosko ?? 'hibrido' }}";
    let html5QrCodeKiosko = null;
    let resetTimerInterval = null;
    let countdownSeconds = 10;
    let isProcessingScan = false;

    // Cambiar Modo Dinámicamente
    function setModoKiosko(modo) {
        currentModoKiosko = modo;
        
        document.querySelectorAll('.kmode-pill').forEach(b => b.classList.remove('active'));
        if (modo === 'hibrido' && document.getElementById('btnModoHibrido')) document.getElementById('btnModoHibrido').classList.add('active');
        if (modo === 'camara' && document.getElementById('btnModoCamara')) document.getElementById('btnModoCamara').classList.add('active');
        if (modo === 'codigo' && document.getElementById('btnModoCodigo')) document.getElementById('btnModoCodigo').classList.add('active');

        const grid = document.getElementById('kioskoGrid');
        const cardCam = document.getElementById('cardKioskoCamara');
        const cardCod = document.getElementById('cardKioskoCodigo');

        if (grid) grid.style.display = 'grid';

        if (modo === 'hibrido') {
            grid.classList.remove('mode-single');
            cardCam.style.display = 'block';
            cardCod.style.display = 'block';
        } else if (modo === 'camara') {
            grid.classList.add('mode-single');
            cardCam.style.display = 'block';
            cardCod.style.display = 'none';
        } else if (modo === 'codigo') {
            grid.classList.add('mode-single');
            cardCam.style.display = 'none';
            cardCod.style.display = 'block';
            const input = document.getElementById('kioskoInputCodigo');
            if (input) input.focus();
        }
    }

    // Auto Encendido de Cámara para Tótem
    function autoIniciarCamaraTótem() {
        if (currentModoKiosko === 'codigo') return;

        if (!html5QrCodeKiosko) {
            html5QrCodeKiosko = new Html5Qrcode("reader-kiosko");
        }

        const config = { 
            fps: 15, 
            qrbox: function(w, h) {
                const size = Math.floor(Math.min(w, h) * 0.75);
                return { width: size, height: size };
            }
        };

        html5QrCodeKiosko.start(
            { facingMode: "environment" },
            config,
            onScanSuccessKiosko
        ).catch(err => {
            console.warn("Error cámara trasera:", err);
            html5QrCodeKiosko.start(
                { facingMode: "user" },
                config,
                onScanSuccessKiosko
            ).catch(err2 => console.error("Sin acceso a cámara:", err2));
        });
    }

    function onScanSuccessKiosko(decodedText) {
        if (isProcessingScan) return;
        isProcessingScan = true;

        if (html5QrCodeKiosko && html5QrCodeKiosko.isScanning) {
            try { html5QrCodeKiosko.pause(true); } catch(e) {}
        }

        document.getElementById('kioskoInputCodigo').value = decodedText;
        consultarPuntos(decodedText);
    }

    // Consulta de Puntos vía AJAX
    function procesarConsultaKiosko(e) {
        e.preventDefault();
        const val = document.getElementById('kioskoInputCodigo').value.trim();
        if (val) {
            consultarPuntos(val);
        }
    }

    function consultarPuntos(codigo) {
        clearInterval(resetTimerInterval);

        fetch("{{ route('kiosko.buscar') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.ok) {
                isProcessingScan = false;
                if (html5QrCodeKiosko) {
                    try { html5QrCodeKiosko.resume(); } catch(e) {}
                }
                Swal.fire({
                    icon: 'warning',
                    title: 'Participante no encontrado',
                    text: data.message || 'Código QR no registrado.',
                    confirmButtonColor: '#fbbf24',
                    timer: 3000
                });
                return;
            }

            const p = data.participante;
            mostrarResultadoParticipante(p);
        })
        .catch(err => {
            console.error(err);
            isProcessingScan = false;
            if (html5QrCodeKiosko) {
                try { html5QrCodeKiosko.resume(); } catch(e) {}
            }
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo consultar la información en la base de datos.',
                confirmButtonColor: '#fbbf24'
            });
        });
    }

    function mostrarResultadoParticipante(p) {
        // OCULTAR EL PANEL DE CÁMARA/ESCÁNER PARA QUE EL RESULTADO OCUPE EL 100% DE LA PANTALLA DEL TÓTEM
        const grid = document.getElementById('kioskoGrid');
        if (grid) grid.style.display = 'none';

        const card = document.getElementById('participanteResultCard');
        
        document.getElementById('resAvatar').innerText = (p.nombre ? p.nombre.charAt(0).toUpperCase() : 'P');
        document.getElementById('resNombre').innerText = p.nombre;
        document.getElementById('resRFC').innerText = "RFC: " + p.rfc;
        document.getElementById('resCategoria').innerText = p.categoria;
        document.getElementById('resPuntosTotales').innerText = p.puntos_totales;

        document.getElementById('resPuntosIndiv').innerText = p.puntos_individuales;
        document.getElementById('resPuntosGrup').innerText = p.puntos_grupales;
        document.getElementById('resTotalVisitas').innerText = p.total_visitas;

        // Historial
        const tbody = document.getElementById('resHistorialBody');
        tbody.innerHTML = '';
        if (p.historial && p.historial.length > 0) {
            p.historial.forEach(h => {
                const tr = document.createElement('tr');
                const isPositive = h.puntos > 0;
                const ptsColor = isPositive ? '#4ade80' : '#f87171';
                const ptsSign = isPositive ? '+' : '';
                
                tr.innerHTML = `
                    <td style="padding:10px 12px; font-weight:800; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${h.origen || 'Stand'}</td>
                    <td style="padding:10px 12px; text-transform:uppercase; font-size:11px; opacity:0.85;">${h.tipo}</td>
                    <td style="padding:10px 12px; text-align:right; font-weight:900; color:${ptsColor}; white-space:nowrap;">${ptsSign}${h.puntos} pts</td>
                    <td style="padding:10px 12px; text-align:right; color:#94a3b8; font-size:12px; white-space:nowrap;">${h.fecha ? h.fecha.substring(0, 16) : ''}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4" style="padding:16px; text-align:center; color:#94a3b8;">Sin movimientos de puntos registrados.</td></tr>`;
        }

        card.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });

        // Temporizador y Barra de Progreso Tótem
        countdownSeconds = 10;
        const progressFill = document.getElementById('totemProgressFill');
        if (progressFill) progressFill.style.width = '100%';

        document.getElementById('resetCountdown').innerText = countdownSeconds;
        resetTimerInterval = setInterval(() => {
            countdownSeconds--;
            document.getElementById('resetCountdown').innerText = countdownSeconds;
            if (progressFill) {
                progressFill.style.width = (countdownSeconds * 10) + '%';
            }
            if (countdownSeconds <= 0) {
                resetPantallaKiosko();
            }
        }, 1000);
    }

    function resetPantallaKiosko() {
        clearInterval(resetTimerInterval);
        isProcessingScan = false;
        if (html5QrCodeKiosko) {
            try { html5QrCodeKiosko.resume(); } catch(e) {}
        }

        // OCULTAR RESULTADO Y MOSTRAR DE NUEVO EL ESCÁNER A PANTALLA COMPLETA
        document.getElementById('participanteResultCard').style.display = 'none';
        setModoKiosko(currentModoKiosko);

        const input = document.getElementById('kioskoInputCodigo');
        if (input) {
            input.value = '';
            input.focus();
        }
    }

    // Inicialización automática
    document.addEventListener('DOMContentLoaded', function() {
        setModoKiosko(currentModoKiosko);
        setTimeout(autoIniciarCamaraTótem, 400);
    });
</script>
@endpush