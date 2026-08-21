@extends('layouts.app')

@section('title', 'Canje de Premios — ' . $evento->name_evento)
@section('page-title', 'Canje de Premios')

@section('topbar-actions')
    <a href="{{ route('eventos.canjes.reporte', $evento) }}" class="btn btn-secondary" style="margin-right:8px;">
        <i class="bi bi-file-earmark-bar-graph"></i> Reporte Global
    </a>
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Evento
    </a>
@endsection

@section('content')

@push('styles')
<style>
    .premio-canje-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-subtle);
        border-radius: 10px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .premio-canje-card.puede { border-color: rgba(16,185,129,0.3); }
    .premio-canje-card.puede:hover { border-color: rgba(16,185,129,0.5); background: rgba(16,185,129,0.04); }
    .premio-canje-card.no-puede { opacity: 0.45; }

    .premio-canje-icon {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .premio-canje-card.puede .premio-canje-icon { background: rgba(16,185,129,0.12); color: #10b981; }
    .premio-canje-card.no-puede .premio-canje-icon { background: rgba(100,116,139,0.12); color: #64748b; }

    .badge-max {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 700;
    }
    .badge-max.si { background: rgba(16,185,129,0.15); color: #10b981; }
    .badge-max.no { background: rgba(239,68,68,0.1); color: #ef4444; }

    .canje-qty-control {
        display: flex; align-items: center; gap: 0;
        border: 1px solid var(--border-subtle); border-radius: 8px;
        overflow: hidden;
    }
    .canje-qty-btn {
        width: 32px; height: 32px;
        background: var(--bg-secondary); border: none;
        color: var(--text-primary); font-size: 16px; font-weight: 700;
        cursor: pointer; transition: background 0.15s;
        display: flex; align-items: center; justify-content: center;
    }
    .canje-qty-btn:hover { background: rgba(212,175,55,0.15); color: var(--accent-gold); }
    .canje-qty-val {
        width: 40px; text-align: center; font-weight: 800;
        font-size: 14px; color: var(--text-primary);
        background: transparent; border: none;
        border-left: 1px solid var(--border-subtle);
        border-right: 1px solid var(--border-subtle);
        height: 32px;
    }

    .btn-canjear-individual {
        padding: 6px 14px; border-radius: 8px;
        border: none; font-size: 12px; font-weight: 700;
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; gap: 5px;
    }
    .btn-canjear-individual.activo {
        background: linear-gradient(135deg, #d4af37, #c9952a);
        color: #000;
    }
    .btn-canjear-individual.activo:hover { transform: scale(1.03); box-shadow: 0 3px 12px rgba(212,175,55,0.4); }
    .btn-canjear-individual.inactivo {
        background: rgba(100,116,139,0.15); color: var(--text-muted);
        pointer-events: none;
    }

    .participante-info-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-subtle);
        border-radius: 12px;
        padding: 20px;
        animation: fadeSlide 0.3s ease;
    }
    @keyframes fadeSlide { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }

    .search-results {
        max-height: 220px; overflow-y: auto;
        border: 1px solid var(--border-subtle);
        border-radius: 8px; margin-top: 8px;
        background: var(--bg-secondary);
    }
    .search-result-item {
        padding: 10px 14px; cursor: pointer;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        transition: background 0.15s;
        display: flex; justify-content: space-between; align-items: center;
    }
    .search-result-item:hover { background: rgba(212,175,55,0.08); }
    .search-result-item:last-child { border-bottom: none; }

    .historial-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.04); font-size: 12px;
    }
    .historial-row:last-child { border-bottom: none; }

    .canje-toast {
        position: fixed; top: 20px; right: 20px; z-index: 9999;
        padding: 16px 24px; border-radius: 10px;
        font-weight: 600; font-size: 14px;
        animation: slideIn 0.3s ease; max-width: 420px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    }
    .canje-toast.success { background: rgba(16,185,129,0.95); color: #fff; }
    .canje-toast.error { background: rgba(239,68,68,0.95); color: #fff; }
    @keyframes slideIn { from { transform: translateX(100%); opacity:0; } to { transform: translateX(0); opacity:1; } }
</style>
@endpush

<!-- KPIs -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="kpi-card" style="--kpi-color:#d4af37">
        <div class="kpi-icon"><i class="bi bi-gift"></i></div>
        <div>
            <div class="kpi-value">{{ $premios->count() }}</div>
            <div class="kpi-label">Premios Disponibles</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#10b981">
        <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
        <div>
            <div class="kpi-value">{{ $totalCanjes }}</div>
            <div class="kpi-label">Canjes Realizados</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#3b82f6">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div>
            <div class="kpi-value">{{ $totalPremiosCanjeados }}</div>
            <div class="kpi-label">Premios Entregados</div>
        </div>
    </div>
</div>

<!-- BUSCADOR PRINCIPAL CON ESCÁNER QR -->
<div class="card" style="margin-bottom:24px; background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.06); padding:16px 20px;">
        <span class="card-title" style="font-size:15px; font-weight:700;"><i class="bi bi-upc-scan" style="color:var(--accent-gold); margin-right:8px;"></i>Escanear o Buscar Participante</span>
        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCamScanner()" style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; padding:6px 14px; background:rgba(212,175,55,0.12); border:1px solid var(--accent-gold); color:var(--accent-gold);">
            <i class="bi bi-camera-fill"></i> <span id="camBtnText">Cámara QR</span>
        </button>
    </div>
    <div class="card-body" style="padding:20px;">
        <!-- Cámara Panel (oculto por defecto) -->
        <div id="camScannerPanel" style="display:none; margin-bottom:16px; background:rgba(10,15,30,0.6); padding:16px; border-radius:10px; border:1px solid rgba(255,255,255,0.08); text-align:center;">
            <div style="display:flex; gap:10px; justify-content:center; margin-bottom:12px;">
                <select id="camSelect" class="form-select" style="max-width:300px; font-size:12px;"></select>
                <button type="button" id="btnCamStart" class="btn btn-sm btn-primary"><i class="bi bi-camera"></i> Encender</button>
                <button type="button" id="btnCamStop" class="btn btn-sm btn-secondary" disabled><i class="bi bi-camera-video-off"></i> Apagar</button>
            </div>
            <div style="position:relative; width:100%; max-width:360px; margin:0 auto; border-radius:8px; overflow:hidden; background:#000;">
                <video id="camVideo" playsinline style="width:100%; height:auto; display:block;"></video>
            </div>
        </div>

        <div style="display:flex; gap:12px; align-items:center;">
            <div style="position:relative; flex:1;">
                <i class="bi bi-search" style="position:absolute; left:16px; top:14px; color:var(--accent-gold); font-size:16px;"></i>
                <input type="text" id="buscarParticipante" placeholder="Ingresa nombre, teléfono, ID o escanea la credencial..." class="form-control" style="padding-left:46px; font-size:15px; height:48px; background:rgba(10,15,30,0.5); border:1px solid rgba(255,255,255,0.12);" autofocus>
            </div>
        </div>
        <div id="searchResults" class="search-results" style="display:none;"></div>
    </div>
</div>

<!-- PANEL INICIAL: MOSTRADO POR DEFECTO ANTES DE SELECCIONAR PARTICIPANTE -->
<div id="panelInicial" style="display:block; animation: fadeSlide 0.4s ease;">
    <div style="display:grid; grid-template-columns:1fr 380px; gap:24px; align-items:start;">

        <!-- Catálogo General de Premios -->
        <div class="card" style="background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div class="card-header" style="border-bottom:1px solid rgba(255,255,255,0.06); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:15px; font-weight:700;"><i class="bi bi-gift-fill" style="color:var(--accent-gold); margin-right:8px;"></i>Catálogo de Premios del Evento</span>
                <span class="badge badge-gold">{{ $premios->count() }} Premios</span>
            </div>
            <div class="card-body" style="padding:20px;">
                @if($premios->isEmpty())
                    <div style="padding:40px; text-align:center; color:var(--text-muted); background:rgba(10,15,30,0.4); border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                        <i class="bi bi-gift" style="font-size:40px; display:block; margin-bottom:10px; opacity:0.4;"></i>
                        No hay premios registrados en este evento.
                    </div>
                @else
                    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:14px;">
                        @foreach($premios as $prem)
                            @php
                                $numCanjeados = \DB::table('canjes')->where('ID_Evento', $evento->ID)->where('ID_Premio', $prem->ID)->sum('Cantidad');
                                $conStock = ($prem->Disponible > 0);
                            @endphp
                            <div onclick="enfocarBuscador()" style="background:rgba(10,15,30,0.5); border:1px solid {{ $conStock ? 'rgba(212,175,55,0.2)' : 'rgba(255,255,255,0.05)' }}; border-radius:10px; padding:14px; display:flex; gap:12px; align-items:center; cursor:pointer; transition:all 0.2s ease;" onmouseenter="this.style.borderColor='var(--accent-gold)'; this.style.transform='translateY(-2px)'" onmouseleave="this.style.borderColor='{{ $conStock ? 'rgba(212,175,55,0.2)' : 'rgba(255,255,255,0.05)' }}'; this.style.transform='translateY(0)'">
                                <div style="width:44px; height:44px; border-radius:10px; background:linear-gradient(135deg, rgba(212,175,55,0.2), rgba(212,175,55,0.05)); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:20px; flex-shrink:0;">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:700; font-size:13.5px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $prem->NombrePremio }}</div>
                                    <div style="display:flex; align-items:center; gap:8px; margin-top:4px;">
                                        <span style="font-size:12px; font-weight:800; color:var(--accent-gold);">⭐ {{ number_format($prem->PuntosNecesarios) }} pts</span>
                                        <span style="font-size:11px; color:var(--text-muted);">· Stock: {{ $prem->Disponible }}</span>
                                    </div>
                                    <div style="font-size:10px; color:var(--text-muted); margin-top:2px;">Canjeados: {{ $numCanjeados }} pzas</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Últimos Canjes Realizados -->
        <div class="card" style="background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div class="card-header" style="border-bottom:1px solid rgba(255,255,255,0.06); padding:16px 20px; display:flex; justify-content:space-between; align-items:center;">
                <span class="card-title" style="font-size:15px; font-weight:700;"><i class="bi bi-clock-history" style="color:var(--accent-gold); margin-right:8px;"></i>Últimos Canjes</span>
                <span class="badge badge-secondary">{{ count($ultimosCanjes) }} recientes</span>
            </div>
            <div class="card-body" style="padding:16px; max-height:480px; overflow-y:auto;">
                @if($ultimosCanjes->isEmpty())
                    <div style="padding:30px 15px; text-align:center; color:var(--text-muted); font-size:13px;">
                        <i class="bi bi-inbox" style="font-size:32px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                        No hay canjes registrados en este evento aún.
                    </div>
                @else
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        @foreach($ultimosCanjes as $canj)
                            <div style="background:rgba(10,15,30,0.5); border:1px solid rgba(255,255,255,0.05); border-radius:8px; padding:10px 12px; font-size:12px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="color:var(--text-primary);">{{ optional($canj->participante)->Nombre ?: 'Participante #'.$canj->ID_Participante }}</strong>
                                    <span style="font-size:10px; color:var(--text-muted);">{{ \Carbon\Carbon::parse($canj->Fecha)->format('H:i') }}</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
                                    <span style="color:var(--accent-gold); font-weight:600;"><i class="bi bi-gift-fill" style="font-size:11px;"></i> {{ optional($canj->premio)->NombrePremio ?: 'Premio' }} x{{ $canj->Cantidad }}</span>
                                    <span style="color:#00bc8c; font-size:10px; font-weight:700;"><i class="bi bi-check-circle-fill"></i> Entregado</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<!-- PANEL PARTICIPANTE + PREMIOS (aparece al seleccionar un participante) -->
<div id="panelCanje" style="display:none; animation: fadeSlide 0.4s ease;">

    <!-- Info del participante -->
    <div id="participanteInfo" style="margin-bottom:24px;"></div>

    <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:flex-start;">
        <!-- Premios canjeables (Columna Izquierda) -->
        <div class="card" style="flex: 1 1 55%; min-width:320px; margin-bottom:24px; background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div class="card-header" style="position: sticky; top: 0; z-index: 10; background: var(--bg-secondary); border-bottom: 1px solid rgba(255,255,255,0.06); border-radius: 12px 12px 0 0; padding:16px 20px;">
                <span class="card-title" style="font-size:15px; font-weight:700;"><i class="bi bi-gift-fill" style="color:var(--accent-gold); margin-right:8px;"></i>Premios Disponibles</span>
                <span id="premiosCount" class="badge badge-gold" style="margin-left:8px;"></span>
            </div>
            <div class="card-body" id="premiosGrid" style="max-height: 520px; overflow-y: auto; padding: 20px;">
                <!-- Se llena via JS -->
            </div>
        </div>

        <!-- Historial de canjes del participante (Columna Derecha) -->
        <div class="card" id="historialCard" style="flex: 1 1 35%; min-width:320px; margin-bottom:24px; background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
            <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; position: sticky; top: 0; z-index: 10; background: var(--bg-secondary); border-bottom: 1px solid rgba(255,255,255,0.06); border-radius: 12px 12px 0 0; padding:16px 20px;">
                <span class="card-title" style="font-size:15px; font-weight:700;"><i class="bi bi-clock-history" style="color:var(--accent-gold); margin-right:8px;"></i>Historial de Canjes</span>
                <span id="historialCount" class="badge badge-secondary"></span>
            </div>
            <div id="historialBody" style="max-height: 520px; overflow-y: auto; padding: 16px;"></div>
        </div>
    </div>
</div>


<div id="toastContainer"></div>

<script src="https://unpkg.com/@zxing/browser@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const buscarInput = document.getElementById('buscarParticipante');
    const searchResults = document.getElementById('searchResults');
    const panelInicial = document.getElementById('panelInicial');
    const panelCanje = document.getElementById('panelCanje');
    let selectedParticipante = null;
    let debounceTimer;

    // Helper para enfocar buscador
    window.enfocarBuscador = function() {
        buscarInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        buscarInput.focus();
    };

    // Helper para regresar a vista inicial
    window.limpiarSeleccion = function() {
        selectedParticipante = null;
        if (panelCanje) panelCanje.style.display = 'none';
        if (panelInicial) panelInicial.style.display = 'block';
        buscarInput.value = '';
        buscarInput.focus();
    };

    // Camera Scanner logic
    let codeReader = null;
    let controls = null;
    window.toggleCamScanner = function() {
        const panel = document.getElementById('camScannerPanel');
        const btnText = document.getElementById('camBtnText');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            if (btnText) btnText.innerText = 'Cerrar Cámara';
            initCamDevices();
        } else {
            panel.style.display = 'none';
            if (btnText) btnText.innerText = 'Cámara QR';
            stopCamScanner();
        }
    };

    function initCamDevices() {
        if (!codeReader) codeReader = new ZXingBrowser.BrowserQRCodeReader();
        ZXingBrowser.BrowserQRCodeReader.listVideoInputDevices()
            .then(devices => {
                const select = document.getElementById('camSelect');
                select.innerHTML = '';
                devices.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.deviceId;
                    opt.text = d.label || `Cámara ${select.length + 1}`;
                    select.appendChild(opt);
                });
            })
            .catch(err => console.error(err));
    }

    document.getElementById('btnCamStart').addEventListener('click', function() {
        if (!codeReader) codeReader = new ZXingBrowser.BrowserQRCodeReader();
        const devId = document.getElementById('camSelect').value;
        this.disabled = true;
        document.getElementById('btnCamStop').disabled = false;

        codeReader.decodeFromVideoDevice(devId, 'camVideo', (result) => {
            if (result) {
                const text = result.getText();
                buscarInput.value = text;
                stopCamScanner();
                document.getElementById('camScannerPanel').style.display = 'none';
                document.getElementById('camBtnText').innerText = 'Cámara QR';
                
                const foundId = extractIdFromQr(text);
                if (foundId) {
                    seleccionarParticipante(foundId);
                } else {
                    buscarInput.dispatchEvent(new Event('input'));
                }
            }
        });
    });

    document.getElementById('btnCamStop').addEventListener('click', function() {
        stopCamScanner();
    });

    function stopCamScanner() {
        if (controls) { controls.stop(); controls = null; }
        document.getElementById('btnCamStart').disabled = false;
        document.getElementById('btnCamStop').disabled = true;
    }

    // Buscar participante (AJAX live search)
    buscarInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        const minLen = /^\d+$/.test(q) ? 1 : 2;
        if (q.length < minLen) { searchResults.style.display = 'none'; return; }
        debounceTimer = setTimeout(() => {
            fetch('{{ route("eventos.canjes.buscar", $evento) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ q })
            })
            .then(r => r.json())
            .then(data => {
                if (data.length === 0) {
                    searchResults.innerHTML = '<div style="padding:16px; text-align:center; color:var(--text-muted); font-size:13px;"><i class="bi bi-person-x" style="font-size:20px; display:block; margin-bottom:4px; opacity:0.5;"></i>No se encontraron participantes</div>';
                } else {
                    searchResults.innerHTML = data.map(p => `
                        <div class="search-result-item" onclick="seleccionarParticipante(${p.ID})">
                            <div>
                                <strong style="color:var(--text-primary); font-size:13px;">${p.Nombre}</strong>
                                <div style="font-size:11px; color:var(--text-muted);">ID: ${p.ID} · ${p.Telefono || 'Sin tel.'} · ${p.Sucursal || '—'}</div>
                            </div>
                            <span class="badge badge-gold">⭐ ${Number(p.Puntos).toLocaleString()} pts</span>
                        </div>
                    `).join('');
                }
                searchResults.style.display = 'block';
            });
        }, 300);
    });

    // Helper para extraer ID numérico de texto QR o escaneos (ej: ID3272ÑAlejandro...)
    function extractIdFromQr(str) {
        if (!str) return null;
        const s = str.trim();
        if (s.includes('Ñ') || s.toUpperCase().startsWith('ID')) {
            const parts = s.split('Ñ');
            if (parts[0]) {
                const rawId = parts[0].replace(/ID/gi, '').replace(/\D/g, '');
                if (rawId) return parseInt(rawId);
            }
        }
        if (/^\d+$/.test(s)) return parseInt(s);
        return null;
    }

    // Enter = si contiene ID escaneado o numérico, cargar directo
    buscarInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim();
            const foundId = extractIdFromQr(q);
            if (foundId) {
                seleccionarParticipante(foundId);
            } else if (q.length >= 2) {
                clearTimeout(debounceTimer);
                buscarInput.dispatchEvent(new Event('input'));
            }
        }
    });

    // Seleccionar participante -> cargar info + premios
    window.seleccionarParticipante = function(id) {
        searchResults.style.display = 'none';
        buscarInput.value = '';

        fetch(`{{ url('eventos/' . $evento->ID . '/canjes/participante') }}/${id}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { showToast(data.msg, 'error'); return; }

            selectedParticipante = data.participante;
            const pts = data.puntosDisponibles;

            if (panelInicial) panelInicial.style.display = 'none';

            // --- Render Info del Participante ---
            document.getElementById('participanteInfo').innerHTML = `
                <div class="participante-info-card" style="background:var(--bg-secondary); border:1px solid rgba(255,255,255,0.08); box-shadow:0 8px 32px rgba(0,0,0,0.25);">
                    <div style="display:flex; align-items:center; gap:16px;">
                        <div style="width:54px; height:54px; border-radius:50%; background:linear-gradient(135deg, rgba(212,175,55,0.3), rgba(212,175,55,0.08)); display:flex; align-items:center; justify-content:center; font-weight:800; color:var(--accent-gold); font-size:22px; flex-shrink:0; border:1px solid var(--accent-gold);">
                            ${data.participante.Nombre.charAt(0).toUpperCase()}
                        </div>
                        <div style="flex:1;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="font-weight:700; font-size:17px; color:var(--text-primary);">${data.participante.Nombre}</div>
                                <button type="button" onclick="limpiarSeleccion()" class="btn btn-sm btn-secondary" style="font-size:11px; padding:3px 10px; border-radius:6px; display:inline-flex; align-items:center; gap:4px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.15); color:var(--text-primary);"><i class="bi bi-arrow-left"></i> Cambiar Participante</button>
                            </div>
                            <div style="font-size:12px; color:var(--text-muted); display:flex; gap:14px; flex-wrap:wrap; margin-top:4px;">
                                <span><i class="bi bi-hash" style="color:var(--accent-gold);"></i> ID: ${data.participante.ID}</span>
                                <span><i class="bi bi-telephone" style="color:var(--accent-gold);"></i> ${data.participante.Telefono || '—'}</span>
                                <span><i class="bi bi-building" style="color:var(--accent-gold);"></i> ${data.participante.Sucursal || '—'}</span>
                                <span><i class="bi bi-shop" style="color:var(--accent-gold);"></i> ${data.participante.Proveedor || '—'}</span>
                            </div>
                        </div>
                        <div style="text-align:right; background:rgba(212,175,55,0.08); padding:10px 18px; border-radius:10px; border:1px solid rgba(212,175,55,0.2);">
                            <div style="font-size:10.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; font-weight:800;">Puntos Disponibles</div>
                            <div style="font-size:30px; font-weight:900; color:var(--accent-gold); line-height:1; margin-top:2px;">⭐ ${Number(pts).toLocaleString()}</div>
                            <div style="font-size:10.5px; color:var(--text-muted); margin-top:3px;">
                                ${Number(data.participante.Puntos).toLocaleString()} acumulados · ${Number(data.puntosGastados).toLocaleString()} gastados
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // --- Render Premios Canjeables ---
            const premios = data.premios_disponibles || [];
            const canjeables = premios.filter(p => p.puede_canjear).length;
            document.getElementById('premiosCount').textContent = `${canjeables} de ${premios.length} disponibles`;

            if (premios.length === 0) {
                document.getElementById('premiosGrid').innerHTML = `
                    <div style="text-align:center; padding:30px; color:var(--text-muted);">
                        <i class="bi bi-gift" style="font-size:36px; display:block; margin-bottom:8px; opacity:0.4;"></i>
                        No hay premios configurados para este evento.
                    </div>`;
            } else {
                document.getElementById('premiosGrid').innerHTML = `
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        ${premios.map(p => `
                            <div class="premio-canje-card ${p.puede_canjear ? 'puede' : 'no-puede'}" id="premio-row-${p.id}" style="background:rgba(10,15,30,0.5); border:1px solid ${p.puede_canjear ? 'rgba(16,185,129,0.3)' : 'rgba(255,255,255,0.06)'}; border-radius:10px; padding:14px; display:flex; align-items:center; gap:14px;">
                                <div class="premio-canje-icon" style="width:46px; height:46px; border-radius:10px; background:${p.puede_canjear ? 'rgba(16,185,129,0.15)' : 'rgba(100,116,139,0.12)'}; color:${p.puede_canjear ? '#10b981' : '#64748b'}; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:700; font-size:14px; color:var(--text-primary);">${p.nombre}</div>
                                    <div style="display:flex; gap:12px; margin-top:4px; flex-wrap:wrap; align-items:center;">
                                        <span style="font-size:12px; color:var(--accent-gold); font-weight:700;">
                                            ⭐ ${Number(p.puntos).toLocaleString()} pts c/u
                                        </span>
                                        <span style="font-size:12px; color:var(--text-muted);">
                                            <i class="bi bi-box-seam"></i> Stock: ${p.stock}
                                        </span>
                                        <span class="badge-max ${p.puede_canjear ? 'si' : 'no'}">
                                            ${p.puede_canjear
                                                ? '<i class="bi bi-check-circle-fill"></i> Puede canjear hasta ' + p.max_canjeable
                                                : '<i class="bi bi-x-circle-fill"></i> ' + (p.stock <= 0 ? 'Sin stock' : 'Puntos insuficientes')
                                            }
                                        </span>
                                    </div>
                                </div>
                                ${p.puede_canjear ? `
                                    <div style="display:flex; align-items:center; gap:10px; flex-shrink:0;">
                                        <div class="canje-qty-control">
                                            <button class="canje-qty-btn" onclick="cambiarCantidad(${p.id}, -1, ${p.max_canjeable})">−</button>
                                            <input type="number" class="canje-qty-val" id="qty-${p.id}" value="1" min="1" max="${p.max_canjeable}" readonly>
                                            <button class="canje-qty-btn" onclick="cambiarCantidad(${p.id}, 1, ${p.max_canjeable})">+</button>
                                        </div>
                                        <button class="btn-canjear-individual activo" onclick="realizarCanje(${p.id}, '${p.nombre.replace(/'/g, "\\'")}')">
                                            <i class="bi bi-gift-fill"></i> Canjear
                                        </button>
                                    </div>
                                ` : `
                                    <button class="btn-canjear-individual inactivo" disabled>
                                        <i class="bi bi-lock"></i> Insuficiente
                                    </button>
                                `}
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            // --- Render Historial ---
            const historial = data.historial || [];
            document.getElementById('historialCount').textContent = historial.length + (historial.length === 1 ? ' canje' : ' canjes');

            if (historial.length > 0) {
                let totalPuntosUsados = historial.reduce((sum, h) => sum + (h.puntos * h.cantidad), 0);
                document.getElementById('historialBody').innerHTML = `
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        ${historial.map(h => `
                            <div style="background:rgba(10,15,30,0.5); border:1px solid rgba(255,255,255,0.05); border-radius:8px; padding:10px 12px; font-size:12px;">
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <strong style="color:var(--text-primary);"><i class="bi bi-gift-fill" style="color:var(--accent-gold); margin-right:4px;"></i> ${h.premio} x${h.cantidad}</strong>
                                    <span style="color:#ef4444; font-weight:700;">-${Number(h.puntos * h.cantidad).toLocaleString()} pts</span>
                                </div>
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px; font-size:11px; color:var(--text-muted);">
                                    <span><i class="bi bi-clock"></i> ${h.fecha}</span>
                                    <span style="color:#00bc8c; font-weight:600;"><i class="bi bi-check-circle-fill"></i> Completado</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
            } else {
                document.getElementById('historialBody').innerHTML = `
                    <div style="text-align:center; padding:24px; color:var(--text-muted); font-size:13px;">
                        <i class="bi bi-inbox" style="font-size:28px; display:block; margin-bottom:6px; opacity:0.4;"></i>
                        Este participante no ha canjeado premios aún.
                    </div>
                `;
            }

            panelCanje.style.display = 'block';
            panelCanje.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    };

    // Cambiar cantidad
    window.cambiarCantidad = function(premioId, delta, max) {
        const input = document.getElementById('qty-' + premioId);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
    };

    // Realizar canje
    window.realizarCanje = function(premioId, premioNombre) {
        if (!selectedParticipante) { showToast('Selecciona un participante primero.', 'error'); return; }

        const qty = parseInt(document.getElementById('qty-' + premioId).value) || 1;
        const btn = event.currentTarget;
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando...';

        fetch('{{ route("eventos.canjes.canjear", $evento) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                id_participante: selectedParticipante.ID,
                id_premio: premioId,
                cantidad: qty,
            })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.msg, data.ok ? 'success' : 'error');
            if (data.ok) {
                seleccionarParticipante(selectedParticipante.ID);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-gift-fill"></i> Canjear';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-gift-fill"></i> Canjear';
            showToast('Error de conexión al procesar el canje.', 'error');
        });
    };

    // Notifications (SweetAlert2 fallback to Toast)
    function showToast(msg, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type === 'success' ? 'success' : 'error',
                title: msg,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true
            });
        } else {
            const toast = document.createElement('div');
            toast.className = `canje-toast ${type}`;
            toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${msg}`;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = '0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }
    }

    // Auto-focus al buscador
    setTimeout(() => buscarInput.focus(), 300);
});
</script>
@endsection
