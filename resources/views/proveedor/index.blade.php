@extends('layouts.app')

@section('title', 'Escanear QR - Proveedor')
@section('page-title', 'Escanear QR')

<style>
    .proveedor-container {
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }
    @media (max-width: 768px) {
        .proveedor-container {
            padding: 0 !important;
        }
        .proveedor-card {
            border-radius: 0 !important;
            border-left: none !important;
            border-right: none !important;
        }
    }
    .swal2-container {
        z-index: 99999999 !important;
    }
    #cameraModalOverlay #qrReaderContainer {
        width: 100% !important;
        max-width: 550px !important;
        flex: 1 !important;
        height: 100% !important;
        border-radius: 12px !important;
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
        border-radius: 10px !important;
    }
    #cameraModalOverlay #qrReaderContainer canvas {
        display: none !important;
    }
</style>

@section('content')
<div class="proveedor-container" style="color:var(--text-primary);">
    
    <!-- TARJETA CABECERA DE INFORMACIÓN DEL PROVEEDOR (MINIMALISTA Y CUADRADA) -->
    <div class="proveedor-card" style="background:#0f172a; border-bottom:1px solid rgba(255,255,255,0.08); padding:16px 20px; margin-bottom:16px; border-radius:4px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; font-weight:800; color:var(--accent-gold); display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-briefcase-fill"></i> Panel de Proveedor
                </div>
                <h2 style="margin:4px 0 2px 0; font-size:18px; font-weight:800; color:var(--text-primary); display:flex; align-items:center; gap:8px;">
                    <i class="bi bi-person-badge-fill" style="color:var(--accent-gold);"></i> Proveedor: {{ $usuario }}
                </h2>
            </div>

            @if(isset($eventos_asignados) && $eventos_asignados->count() > 1)
                <form method="GET" action="{{ route('proveedor.index') }}" style="margin:0;">
                    <select name="evento_id" onchange="this.form.submit()" class="form-control" style="font-size:12.5px; padding:6px 12px; border-radius:4px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary); font-weight:700;">
                        @foreach($eventos_asignados as $evAsig)
                            <option value="{{ $evAsig->ID_Evento }}" {{ $id_evento == $evAsig->ID_Evento ? 'selected' : '' }}>
                                Evento: {{ $evAsig->name_evento }} ({{ $evAsig->Puntos }} pts)
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
        </div>

        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; margin-top:10px;">
            <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.35); color:var(--accent-gold); font-size:11.5px; font-weight:800; padding:4px 10px; border-radius:4px; text-transform:uppercase;">
                <i class="bi bi-star-fill"></i> Otorgando {{ $puntos }} PTS por escaneo
            </div>
            <div style="display:inline-flex; align-items:center; gap:5px; background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; font-size:11.5px; font-weight:700; padding:4px 10px; border-radius:4px;">
                <i class="bi bi-calendar-event-fill"></i> {{ $evento_nombre }}
            </div>
        </div>
    </div>

    <!-- PANEL DUAL DE ENTRADA DE ASISTENCIA Y PUNTOS -->
    <div style="padding:0 4px;">
        <div style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:10px; margin-bottom:14px;">
            <h3 style="margin:0; font-size:15px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-qr-code-scan" style="color:var(--accent-gold);"></i> Asignación de Puntos a Participantes
            </h3>
        </div>

        <div style="margin-bottom:16px;">
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:12px;">
                <button type="button" id="btnStartQR" class="btn btn-primary" style="flex:1; min-width:200px; font-weight:800; padding:12px 18px; border-radius:4px; font-size:13.5px; text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; justify-content:center; gap:8px; box-shadow:0 4px 14px rgba(249,115,22,0.3);">
                    <i class="bi bi-camera-fill" style="font-size:18px;"></i> Escáner QR con Cámara
                </button>
            </div>

            <!-- INGRESO MANUAL O LECTOR DE CÓDIGO BARRAS USB -->
            <form id="formManualProveedor" onsubmit="procesarIngresoManual(event);" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; background:rgba(15,23,42,0.6); padding:12px; border-radius:4px; border:1px solid rgba(255,255,255,0.08);">
                <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:200px;">
                    <i class="bi bi-upc-scan" style="color:var(--accent-gold); font-size:18px;"></i>
                    <input type="text" id="inputCodigoManual" placeholder="Ingresa ID, Teléfono o Código (ej: ID1)..." class="form-control" style="flex:1; background:var(--bg-primary); border:1px solid var(--accent-gold); font-size:13.5px; color:var(--text-primary); border-radius:4px;" required>
                </div>
                <button class="btn btn-primary" type="submit" style="font-weight:800; padding:8px 20px; border-radius:4px; text-transform:uppercase; font-size:12.5px;"><i class="bi bi-check-lg"></i> Asignar Puntos</button>
            </form>
        </div>
    </div>

</div>

    <!-- TARJETAS DE KPIS Y RESUMEN DE PUNTOS -->
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px; padding:0 4px;">
        <div style="background:rgba(15,23,42,0.8); border:1px solid rgba(212,175,55,0.3); border-radius:4px; padding:12px; text-align:center;">
            <div style="font-size:10.5px; text-transform:uppercase; font-weight:800; color:var(--text-secondary); letter-spacing:0.5px;">Puntos Entregados</div>
            <div style="font-size:22px; font-weight:900; color:var(--accent-gold); margin-top:2px;">
                <i class="bi bi-star-fill"></i> {{ number_format($total_puntos_entregados) }}
            </div>
        </div>
        <div style="background:rgba(15,23,42,0.8); border:1px solid rgba(56,189,248,0.3); border-radius:4px; padding:12px; text-align:center;">
            <div style="font-size:10.5px; text-transform:uppercase; font-weight:800; color:var(--text-secondary); letter-spacing:0.5px;">Escaneos Realizados</div>
            <div style="font-size:22px; font-weight:900; color:#38bdf8; margin-top:2px;">
                <i class="bi bi-qr-code-scan"></i> {{ number_format($total_escaneos) }}
            </div>
        </div>
    </div>

    <!-- HISTORIAL DE PARTICIPANTES ATENDIDOS -->
    <div style="padding:0 4px; margin-bottom:24px;">
        <div style="border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:8px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:14.5px; font-weight:800; color:var(--text-primary); text-transform:uppercase; letter-spacing:0.5px; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-clock-history" style="color:var(--accent-gold);"></i> Historial de Escaneos
            </h3>
            <span style="font-size:11px; color:var(--text-muted); font-weight:700;">Últimos 50 registros</span>
        </div>

        @if($historial->isEmpty())
            <div style="background:rgba(15,23,42,0.6); border:1px solid rgba(255,255,255,0.08); border-radius:4px; padding:24px 16px; text-align:center; color:var(--text-secondary);">
                <i class="bi bi-inbox" style="font-size:32px; color:var(--text-muted); display:block; margin-bottom:8px;"></i>
                <div style="font-size:13.5px; font-weight:700; color:var(--text-primary);">Aún no has otorgado puntos en este evento</div>
                <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">Usa el escáner QR o el ingreso manual para registrar tu primer participante.</div>
            </div>
        @else
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($historial as $item)
                    @php
                        $words = explode(' ', trim($item->participante_nombre));
                        $initials = strtoupper(substr($words[0] ?? '', 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                    @endphp
                    <div style="background:rgba(15,23,42,0.85); border:1px solid rgba(255,255,255,0.08); border-radius:4px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center; backdrop-filter:blur(8px);">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:36px; height:36px; border-radius:4px; background:linear-gradient(135deg, rgba(249,115,22,0.2), rgba(212,175,55,0.1)); border:1px solid rgba(249,115,22,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:13px; font-weight:800; flex-shrink:0;">
                                {{ $initials ?: 'P' }}
                            </div>
                            <div>
                                <div style="font-size:14px; font-weight:800; color:var(--text-primary); line-height:1.2;">{{ $item->participante_nombre }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px; display:flex; align-items:center; gap:6px;">
                                    <span>#{{ $item->id_participante }}</span>
                                    <span>•</span>
                                    <span><i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($item->fecha)->format('h:i:s A') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span class="badge" style="background:rgba(34,197,94,0.16); border:1px solid rgba(34,197,94,0.35); color:#4ade80; font-weight:900; font-size:12px; padding:5px 11px; border-radius:4px; display:inline-flex; align-items:center; gap:4px;">
                                +{{ $item->puntos }} PTS
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- MODAL FULLSCREEN DE CÁMARA QR -->
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
                procesarCodigoAsignarPuntos(decodedText);
            },
            (errorMessage) => {
                // Ignore frame decode errors
            }
        )
        .then(() => {
            isHtml5Scanning = true;
            if (statusElem) statusElem.innerHTML = '<i class="bi bi-camera-video-fill" style="color:var(--accent-gold);"></i> Escaneando QR... Apunta la cámara al código';
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
</script>
@endsection