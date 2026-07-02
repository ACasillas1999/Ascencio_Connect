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

<!-- BUSCADOR PRINCIPAL -->
<div class="card" style="margin-bottom:24px;">
    <div class="card-header">
        <span class="card-title"><i class="bi bi-upc-scan" style="color:var(--accent-gold); margin-right:8px;"></i>Escanear o Buscar Participante</span>
    </div>
    <div class="card-body">
        <div style="display:flex; gap:12px; align-items:center;">
            <div style="position:relative; flex:1;">
                <i class="bi bi-search" style="position:absolute; left:14px; top:12px; color:var(--text-muted);"></i>
                <input type="text" id="buscarParticipante" placeholder="Escribe nombre, teléfono, RFC o ID del participante..." class="form-control" style="padding-left:42px; font-size:15px; height:44px;" autofocus>
            </div>
        </div>
        <div id="searchResults" class="search-results" style="display:none;"></div>
    </div>
</div>

<!-- PANEL PARTICIPANTE + PREMIOS (aparece al seleccionar uno) -->
<div id="panelCanje" style="display:none; animation: fadeSlide 0.4s ease;">

    <!-- Info del participante -->
    <div id="participanteInfo" style="margin-bottom:24px;"></div>

    <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:flex-start;">
        <!-- Premios canjeables (Columna Izquierda) -->
        <div class="card" style="flex: 1 1 55%; min-width:320px; margin-bottom:24px;">
            <div class="card-header" style="position: sticky; top: 0; z-index: 10; background: var(--bg-secondary); border-bottom: 1px solid var(--border-subtle); border-radius: 12px 12px 0 0;">
                <span class="card-title"><i class="bi bi-gift-fill" style="color:var(--accent-gold); margin-right:8px;"></i>Premios Disponibles</span>
                <span id="premiosCount" class="badge badge-secondary" style="margin-left:8px;"></span>
            </div>
            <div class="card-body" id="premiosGrid" style="max-height: 500px; overflow-y: auto; padding-right: 8px;">
                <!-- Se llena via JS -->
            </div>
        </div>

        <!-- Historial de canjes del participante (Columna Derecha) -->
        <div class="card" id="historialCard" style="flex: 1 1 35%; min-width:320px; margin-bottom:24px;">
            <div class="card-header" style="display:flex; align-items:center; justify-content:space-between; position: sticky; top: 0; z-index: 10; background: var(--bg-secondary); border-bottom: 1px solid var(--border-subtle); border-radius: 12px 12px 0 0;">
                <span class="card-title"><i class="bi bi-clock-history" style="color:var(--accent-gold); margin-right:8px;"></i>Historial de Canjes</span>
                <span id="historialCount" class="badge badge-secondary"></span>
            </div>
            <div id="historialBody" style="max-height: 500px; overflow-y: auto;"></div>
        </div>
    </div>
</div>


<div id="toastContainer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const buscarInput = document.getElementById('buscarParticipante');
    const searchResults = document.getElementById('searchResults');
    const panelCanje = document.getElementById('panelCanje');
    let selectedParticipante = null;
    let debounceTimer;

    // Buscar participante
    buscarInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        // Para IDs numéricos aceptar desde 1 carácter, para texto desde 2
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
                    searchResults.innerHTML = '<div style="padding:16px; text-align:center; color:var(--text-muted); font-size:13px;"><i class="bi bi-person-x" style="font-size:20px; display:block; margin-bottom:4px; opacity:0.5;"></i>Sin resultados</div>';
                } else {
                    searchResults.innerHTML = data.map(p => `
                        <div class="search-result-item" onclick="seleccionarParticipante(${p.ID})">
                            <div>
                                <strong style="color:var(--text-primary); font-size:13px;">${p.Nombre}</strong>
                                <div style="font-size:11px; color:var(--text-muted);">ID: ${p.ID} · ${p.Telefono || 'Sin tel.'} · ${p.Sucursal || '—'}</div>
                            </div>
                            <span class="badge badge-gold">${Number(p.Puntos).toLocaleString()} pts</span>
                        </div>
                    `).join('');
                }
                searchResults.style.display = 'block';
            });
        }, 300);
    });

    // Enter = si es ID numérico, cargar directo
    buscarInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const q = this.value.trim();
            if (/^\d+$/.test(q)) {
                // Es un ID numérico, cargar directo
                seleccionarParticipante(parseInt(q));
            } else if (q.length >= 2) {
                // Disparar búsqueda inmediata
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

            // --- Render Info del Participante ---
            document.getElementById('participanteInfo').innerHTML = `
                <div class="participante-info-card">
                    <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                        <div style="width:52px; height:52px; border-radius:50%; background:linear-gradient(135deg, rgba(212,175,55,0.2), rgba(212,175,55,0.05)); display:flex; align-items:center; justify-content:center; font-weight:800; color:var(--accent-gold); font-size:20px; flex-shrink:0;">
                            ${data.participante.Nombre.charAt(0).toUpperCase()}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:16px; color:var(--text-primary);">${data.participante.Nombre}</div>
                            <div style="font-size:12px; color:var(--text-muted); display:flex; gap:12px; flex-wrap:wrap; margin-top:2px;">
                                <span><i class="bi bi-hash"></i> ${data.participante.ID}</span>
                                <span><i class="bi bi-telephone"></i> ${data.participante.Telefono || '—'}</span>
                                <span><i class="bi bi-building"></i> ${data.participante.Sucursal || '—'}</span>
                                <span><i class="bi bi-shop"></i> ${data.participante.Proveedor || '—'}</span>
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Puntos Disponibles</div>
                            <div style="font-size:28px; font-weight:900; color:var(--accent-gold); line-height:1;">${Number(pts).toLocaleString()}</div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
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
                            <div class="premio-canje-card ${p.puede_canjear ? 'puede' : 'no-puede'}" id="premio-row-${p.id}">
                                <div class="premio-canje-icon">
                                    <i class="bi bi-trophy-fill"></i>
                                </div>
                                <div style="flex:1; min-width:0;">
                                    <div style="font-weight:700; font-size:14px; color:var(--text-primary);">${p.nombre}</div>
                                    <div style="display:flex; gap:12px; margin-top:4px; flex-wrap:wrap;">
                                        <span style="font-size:12px; color:var(--accent-gold); font-weight:700;">
                                            <i class="bi bi-star-fill" style="font-size:10px;"></i> ${Number(p.puntos).toLocaleString()} pts c/u
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
                                            <i class="bi bi-gift"></i> Canjear
                                        </button>
                                    </div>
                                ` : `
                                    <button class="btn-canjear-individual inactivo" disabled>
                                        <i class="bi bi-lock"></i> No disponible
                                    </button>
                                `}
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            // --- Render Historial ---
            const historial = data.historial || [];
            const historialCard = document.getElementById('historialCard');
            document.getElementById('historialCount').textContent = historial.length + (historial.length === 1 ? ' canje' : ' canjes');

            if (historial.length > 0) {
                let totalPuntosUsados = historial.reduce((sum, h) => sum + (h.puntos * h.cantidad), 0);
                document.getElementById('historialBody').innerHTML = `
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Premio</th>
                                    <th style="text-align:center;">Tipo</th>
                                    <th style="text-align:center;">Cantidad</th>
                                    <th style="text-align:center;">Costo (pts)</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${historial.map(h => `
                                    <tr>
                                        <td>
                                            <div style="display:flex; align-items:center; gap:8px;">
                                                <i class="bi bi-gift-fill" style="color:var(--accent-gold);"></i>
                                                <strong style="color:var(--text-primary);">${h.premio}</strong>
                                            </div>
                                        </td>
                                        <td style="text-align:center;">
                                            ${h.tipo === 'puntos' 
                                                ? '<span class="badge badge-secondary" style="color:#a855f7; border-color:#d8b4fe; background:#f3e8ff;">🎟️ Canje</span>'
                                                : '<span class="badge badge-secondary" style="color:#ea580c; border-color:#fdba74; background:#ffedd5;">🎯 Ruleta</span>'
                                            }
                                        </td>
                                        <td style="text-align:center; font-weight:700;">${h.cantidad}</td>
                                        <td style="text-align:center;"><span style="color:#ef4444; font-weight:600;">-${Number(h.puntos * h.cantidad).toLocaleString()}</span></td>
                                        <td style="font-size:12px; color:var(--text-muted);">${h.fecha}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr style="border-top:2px solid var(--border-subtle);">
                                    <td colspan="2" style="font-weight:700; color:var(--text-primary); text-align:right;">Total</td>
                                    <td style="text-align:center; font-weight:700;">${historial.reduce((s, h) => s + h.cantidad, 0)}</td>
                                    <td style="text-align:center; font-weight:800; color:#ef4444;">-${Number(totalPuntosUsados).toLocaleString()} pts</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
            } else {
                document.getElementById('historialBody').innerHTML = `
                    <div style="text-align:center; padding:24px; color:var(--text-muted);">
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
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> ...';

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
                // Refrescar todo el panel con datos actualizados
                seleccionarParticipante(selectedParticipante.ID);
            } else {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-gift"></i> Canjear';
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-gift"></i> Canjear';
            showToast('Error de conexión.', 'error');
        });
    };

    // Toast
    function showToast(msg, type) {
        const toast = document.createElement('div');
        toast.className = `canje-toast ${type}`;
        toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i> ${msg}`;
        document.getElementById('toastContainer').appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = '0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Auto-focus al buscador
    setTimeout(() => buscarInput.focus(), 300);
});
</script>
@endsection
