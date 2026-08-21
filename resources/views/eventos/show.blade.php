@extends('layouts.app')

@section('title', $evento->name_evento)
@section('page-title', $evento->name_evento)

@section('topbar-center')
<script>
    window.switchPremiosSubtab = function(tabName) {
        const catalogSec = document.getElementById('premios-sub-catalog');
        const rankingSec = document.getElementById('premios-sub-ranking');
        const tombolaSec = document.getElementById('premios-sub-tombola');

        const btnCatalog = document.getElementById('subtab-btn-catalog');
        const btnRanking = document.getElementById('subtab-btn-ranking');
        const btnTombola = document.getElementById('subtab-btn-tombola');

        if (window.innerWidth >= 992) {
            if (catalogSec) catalogSec.style.setProperty('display', 'block', 'important');
            if (rankingSec) rankingSec.style.setProperty('display', 'block', 'important');
            if (tombolaSec) tombolaSec.style.setProperty('display', 'block', 'important');
            return;
        }

        if (catalogSec) catalogSec.style.setProperty('display', (tabName === 'catalog') ? 'block' : 'none', 'important');
        if (rankingSec) rankingSec.style.setProperty('display', (tabName === 'ranking') ? 'block' : 'none', 'important');
        if (tombolaSec) tombolaSec.style.setProperty('display', (tabName === 'tombola') ? 'block' : 'none', 'important');

        const tabs = [
            { btn: btnCatalog, name: 'catalog' },
            { btn: btnRanking, name: 'ranking' },
            { btn: btnTombola, name: 'tombola' }
        ];

        tabs.forEach(item => {
            if (item.btn) {
                if (item.name === tabName) {
                    item.btn.style.background = 'var(--accent-gold)';
                    item.btn.style.color = '#0f172a';
                    item.btn.style.fontWeight = '800';
                } else {
                    item.btn.style.background = 'transparent';
                    item.btn.style.color = 'var(--text-secondary)';
                    item.btn.style.fontWeight = '700';
                }
            }
        });
    };

    window.switchTab = function(btn, tabId) {
        if (!tabId) return;
        try {
            localStorage.setItem('evento_active_tab_' + '{{ $evento->ID }}', tabId);
        } catch(e) {}
        document.querySelectorAll('.tab-btn, .mobile-tab-btn').forEach(b => b.classList.remove('active'));
        if (btn && btn.classList) btn.classList.add('active');
        
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        const target = document.getElementById(tabId);
        if (target) target.style.display = 'block';

        const kpiContainer = document.getElementById('kpi-cards-container');
        if (kpiContainer) {
            if ((tabId === 'tab-general' || tabId === 'tab-participantes' || tabId === 'tab-actividades') && window.innerWidth >= 992) {
                kpiContainer.style.display = 'grid';
            } else {
                kpiContainer.style.display = 'none';
            }
        }

        if (tabId === 'tab-gafete' && typeof window.setupEditor === 'function') {
            window.setupEditor();
        }
    };

    window.switchAgendaDate = function(dateStr) {
        // Ocultar todas las tablas
        document.querySelectorAll('.agenda-date-pane').forEach(pane => pane.style.display = 'none');
        // Quitar active a todos los botones
        document.querySelectorAll('.tab-date-btn').forEach(btn => {
            btn.style.color = 'var(--text-muted)';
            btn.style.borderBottom = '2px solid transparent';
            btn.classList.remove('active');
        });

        // Mostrar el seleccionado
        const targetPane = document.getElementById('agenda-date-' + dateStr);
        if (targetPane) targetPane.style.display = 'block';

        // Estilizar el botón clickeado
        const targetBtn = document.querySelector(`.tab-date-btn[data-date="${dateStr}"]`);
        if (targetBtn) {
            targetBtn.style.color = 'var(--accent-gold)';
            targetBtn.style.borderBottom = '2px solid var(--accent-gold)';
            targetBtn.classList.add('active');
        }
    };

    window.toggleAgendaFullscreen = function() {
        const card = document.getElementById('agenda-main-card');
        const icon = document.getElementById('icon-fullscreen-agenda');
        const lbl = document.getElementById('lbl-fullscreen-agenda');
        
        if (!card) return;
        
        const isFS = card.classList.toggle('card-agenda-fullscreen');
        const isMobilePhone = window.innerWidth < 768;
        
        if (isFS) {
            if (icon) icon.className = 'bi bi-fullscreen-exit';
            if (lbl) lbl.textContent = 'Salir';
            document.body.style.overflow = 'hidden';

            // Activar Fullscreen nativo del dispositivo si está disponible
            if (card.requestFullscreen) {
                card.requestFullscreen().catch(() => {});
            } else if (card.webkitRequestFullscreen) {
                card.webkitRequestFullscreen().catch(() => {});
            }

            // Si es celular (no tablet), solicitar orientación Horizontal (Landscape)
            if (isMobilePhone && screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('landscape').catch(() => {});
            }
        } else {
            if (icon) icon.className = 'bi bi-arrows-fullscreen';
            if (lbl) lbl.textContent = 'Pantalla Completa';
            document.body.style.overflow = '';

            if (document.fullscreenElement && document.exitFullscreen) {
                document.exitFullscreen().catch(() => {});
            } else if (document.webkitFullscreenElement && document.webkitExitFullscreen) {
                document.webkitExitFullscreen().catch(() => {});
            }

            // Al salir de pantalla completa, en celulares regresar inmediatamente a orientación Vertical (Portrait)
            if (isMobilePhone && screen.orientation && screen.orientation.lock) {
                screen.orientation.lock('portrait').catch(() => {
                    if (screen.orientation.unlock) screen.orientation.unlock();
                });
            } else if (screen.orientation && screen.orientation.unlock) {
                screen.orientation.unlock().catch(() => {});
            }
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const card = document.getElementById('agenda-main-card');
            if (card && card.classList.contains('card-agenda-fullscreen')) {
                window.toggleAgendaFullscreen();
            }
        }
    });
</script>
@if(auth()->check() && auth()->user()->Rol !== 'Evento')
<!-- TABS NAV - hidden on mobile (shown in sticky-tabs-bar instead) -->
<div class="tabs-wrapper tabs-desktop-only">
    <button class="tab-btn active" onclick="switchTab(this, 'tab-general')">General y Agenda</button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-participantes')">Participantes <span class="badge badge-secondary" style="margin-left:4px">{{ $evento->participantes_count }}</span></button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-actividades')">Actividades</button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-proveedores')"><i class="bi bi-briefcase-fill" style="margin-right:4px; color:var(--accent-gold);"></i> Proveedores <span class="badge badge-secondary" style="margin-left:4px">{{ $proveedores->count() }}</span></button>
    <button class="tab-btn" onclick="switchTab(this, 'tab-premios')">Premios</button>
    @if($evento->machote_gafete)
    <button class="tab-btn" onclick="switchTab(this, 'tab-gafete')"><i class="bi bi-person-vcard" style="margin-right:2px;"></i> Gafete</button>
    @endif
    @if($evento->machote_horario)
    <button class="tab-btn" onclick="switchTab(this, 'tab-horario')"><i class="bi bi-clock" style="margin-right:2px;"></i> Horario</button>
    @endif
</div>
@endif
@endsection

@section('topbar-actions')
    <a href="{{ route('eventos.estadisticas', $evento) }}" class="btn btn-sm" title="Estadísticas" style="background:linear-gradient(135deg, #10b981, #059669); border:1px solid #059669; color:#ffffff; font-weight:700; font-size:12px; display:inline-flex; align-items:center; gap:4px; padding:6px 10px; border-radius:8px; text-decoration:none; flex-shrink:0;">
        <i class="bi bi-bar-chart-line-fill"></i><span class="d-none d-sm-inline">Estadísticas</span>
    </a>
    <a href="{{ route('eventos.sorteo', $evento) }}" class="btn btn-sm d-none d-md-inline-flex" title="Tómbola" style="background:linear-gradient(135deg, #00a0e9, #0084c2); border:1px solid #0084c2; color:#ffffff; font-weight:700; font-size:12px; align-items:center; gap:4px; padding:6px 10px; border-radius:8px; text-decoration:none; flex-shrink:0;">
        <i class="bi bi-play-circle-fill"></i><span>Tómbola</span>
    </a>
    <a href="{{ route('eventos.canjes.index', $evento) }}" class="btn btn-sm" title="Canjes" style="background:linear-gradient(135deg, var(--accent-gold), #b38e1b); border:1px solid #b38e1b; color:#ffffff; font-weight:700; font-size:12px; display:inline-flex; align-items:center; gap:4px; padding:6px 10px; border-radius:8px; text-decoration:none; flex-shrink:0;">
        <i class="bi bi-gift-fill"></i><span class="d-none d-sm-inline">Canjes</span>
    </a>
    <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-secondary btn-sm" title="Editar" style="font-weight:700; font-size:12px; display:inline-flex; align-items:center; gap:4px; padding:6px 10px; border-radius:8px; text-decoration:none; flex-shrink:0;">
        <i class="bi bi-pencil"></i><span class="d-none d-sm-inline">Editar</span>
    </a>
    <a href="{{ route('eventos.index') }}" class="btn btn-secondary btn-sm" title="Volver" style="font-weight:700; font-size:12px; display:inline-flex; align-items:center; gap:4px; padding:6px 10px; border-radius:8px; text-decoration:none; flex-shrink:0;">
        <i class="bi bi-arrow-left"></i><span class="d-none d-sm-inline">Volver</span>
    </a>
@endsection

@push('styles')
<style>
    /* ========================================================= */
    /* PESTAÑAS TOPBAR DESK Y MÓVIL (TABS CAPSULE DESIGN)         */
    /* ========================================================= */
    .tabs-wrapper {
        display: inline-flex;
        gap: 4px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 4px;
        border-radius: 30px;
        align-items: center;
        height: 44px;
        box-shadow: inset 0 2px 6px rgba(0, 0, 0, 0.3);
    }

    .tab-btn {
        background: transparent;
        border: none;
        padding: 6px 16px;
        color: var(--text-muted) !important;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        border-radius: 20px;
        transition: all 0.2s ease;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        height: 34px;
    }

    .tab-btn:hover {
        color: var(--text-primary) !important;
        background: rgba(255, 255, 255, 0.05);
    }

    .tab-btn.active {
        background: linear-gradient(135deg, var(--accent-gold) 0%, #b38e1b 100%) !important;
        color: #000000 !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
    }

    .tab-btn .badge {
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 6px;
        background: rgba(255, 255, 255, 0.18);
        color: var(--text-primary);
    }

    .tab-btn.active .badge {
        background: rgba(0, 0, 0, 0.2);
        color: #000000;
        font-weight: 700;
    }

    /* BARRA MÓVIL STICKY DE TABS */
    .mobile-tabs-bar {
        display: none;
    }

    .event-dashboard-grid {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 20px;
    }

    @media (max-width: 991px) {
        .tabs-desktop-only {
            display: none !important;
        }
        .mobile-tabs-bar {
            display: flex !important;
            gap: 6px;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-subtle);
            padding: 8px 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        .mobile-tab-btn {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 6px 14px;
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 20px;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }
        .mobile-tab-btn:hover:not(.active) {
            background: rgba(255,255,255,0.08) !important;
            color: var(--text-primary) !important;
        }
        .mobile-tab-btn.active {
            background: linear-gradient(135deg, var(--accent-gold), #b38e1b) !important;
            color: #000 !important;
            font-weight: 700;
            border-color: transparent;
            box-shadow: 0 3px 10px rgba(212,175,55,0.3);
        }
        .event-dashboard-grid {
            grid-template-columns: 1fr !important;
            gap: 16px !important;
        }
    }

    /* ========================================================= */
    /* PANTALLA COMPLETA ULTRA-FLUIDA PARA AGENDA (HORARIOS)     */
    /* ========================================================= */
    .card-agenda-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 99999 !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 20px !important;
        box-sizing: border-box !important;
        background: #0b1329 !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: none !important;
    }

    [data-theme="light"] .card-agenda-fullscreen {
        background: #f1f5f9 !important;
    }

    .card-agenda-fullscreen > div[style*="overflow-x:auto"] {
        flex: 1 !important;
        max-height: none !important;
        height: 100% !important;
    }


    /* ========================================================= */
    /* STRIP COMPACTO MÓVIL PARA DETALLES DE EVENTO             */
    /* ========================================================= */
    .mobile-detalles-strip {
        display: none;
    }

    @media (max-width: 991px) {
        .detalles-desktop-card {
            display: none !important;
        }
        .mobile-detalles-strip {
            display: flex !important;
            gap: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
            padding: 8px 12px;
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            margin-bottom: 12px;
        }
        [data-theme="light"] .mobile-detalles-strip {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
        }
        .mdet-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11.5px;
            color: var(--text-secondary);
            flex-shrink: 0;
        }
        .mdet-item i {
            color: var(--accent-gold);
            font-size: 12.5px;
        }
        .mdet-item strong {
            color: var(--text-primary);
            font-weight: 700;
        }
    }

    /* ========================================================= */
    /* PANTALLA COMPLETA PERFECTA CON SCROLL 2D (HORIZ/VERT)    */
    /* ========================================================= */
    .card-agenda-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 99999 !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 12px 14px !important;
        box-sizing: border-box !important;
        background: #0b1329 !important;
        display: flex !important;
        flex-direction: column !important;
        box-shadow: none !important;
    }

    [data-theme="light"] .card-agenda-fullscreen {
        background: #f1f5f9 !important;
    }

    /* ========================================================= */
    /* PANTALLA COMPLETA CON SCROLL VERTICAL Y HORIZONTAL DIRECTO */
    /* ========================================================= */
    .card-agenda-fullscreen {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        z-index: 99999 !important;
        border-radius: 0 !important;
        margin: 0 !important;
        padding: 12px 16px !important;
        box-sizing: border-box !important;
        background: #0b1329 !important;
        overflow-y: auto !important; /* HABILITA SCROLL VERTICAL DIRECTO DE TODO EL PANEL */
        overflow-x: auto !important; /* HABILITA SCROLL HORIZONTAL DIRECTO */
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x pan-y !important;
        box-shadow: none !important;
    }

    [data-theme="light"] .card-agenda-fullscreen {
        background: #f1f5f9 !important;
    }

    .card-agenda-fullscreen .card-header {
        position: relative !important;
        z-index: 50 !important;
        background: #0b1329 !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    }

    [data-theme="light"] .card-agenda-fullscreen .card-header {
        background: #f1f5f9 !important;
    }

    /* BARRA DE FECHAS SIEMPRE VISIBLE EN MÓVIL Y ESCRITORIO (SIN SCROLLBAR TAPANDO TEXTO) */
    .agenda-date-tabs-bar {
        display: flex !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        white-space: nowrap !important;
        background: rgba(0, 0, 0, 0.35) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        padding: 10px 16px !important;
        gap: 8px !important;
        flex-shrink: 0 !important;
        align-items: center !important;
        min-height: 48px !important;
        z-index: 40 !important;
        -ms-overflow-style: none !important;  /* IE and Edge */
        scrollbar-width: none !important;     /* Firefox */
    }

    .agenda-date-tabs-bar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }

    [data-theme="light"] .agenda-date-tabs-bar {
        background: #e2e8f0 !important;
        border-bottom: 1px solid #cbd5e1 !important;
    }

    .card-agenda-fullscreen .agenda-date-tabs-bar {
        position: relative !important;
        top: auto !important;
        background: #0d1733 !important;
        padding: 10px 16px !important;
        min-height: 52px !important;
    }

    /* Botones de fechas estilizados como pills doradas totalmente visibles */
    .tab-date-btn {
        padding: 7px 18px !important;
        border-radius: 20px !important;
        font-size: 12.5px !important;
        font-weight: 700 !important;
        white-space: nowrap !important;
        flex-shrink: 0 !important;
        cursor: pointer !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        background: rgba(255, 255, 255, 0.05) !important;
        color: var(--text-muted) !important;
        transition: all 0.2s ease !important;
        line-height: 1.2 !important;
        display: inline-flex !important;
        align-items: center !important;
    }

    .tab-date-btn:hover {
        color: var(--text-primary) !important;
        background: rgba(255, 255, 255, 0.1) !important;
    }

    .tab-date-btn.active {
        background: linear-gradient(135deg, var(--accent-gold) 0%, #b38e1b 100%) !important;
        color: #000000 !important;
        border-color: transparent !important;
        box-shadow: 0 3px 10px rgba(212, 175, 55, 0.35) !important;
    }

    .card-agenda-fullscreen .agenda-timetable-scroll-wrap {
        width: 100% !important;
        overflow: visible !important;
    }

    .card-agenda-fullscreen .tt-grid-matrix {
        width: 100% !important;
        min-width: 100% !important;
        box-sizing: border-box !important;
    }


    /* Ocultar KPIs superiores en vista móvil (< 991px) */
    @media (max-width: 991px) {
        #kpi-cards-container,
        .kpi-grid {
            display: none !important;
        }
    }


    /* Ajustar la matriz de horarios al 100% horizontal en pantalla completa */
    .card-agenda-fullscreen .tt-grid-matrix {
        width: 100% !important;
        min-width: 100% !important;
        box-sizing: border-box !important;
    }


    /* ========================================================= */
    /* SCROLL VERTICAL + HORIZONTAL MATRIZ DE AGENDA (NORMAL Y FULLSCREEN) */
    /* ========================================================= */
    .agenda-timetable-scroll-wrap {
        width: 100% !important;
        max-width: 100% !important;
        max-height: 560px; /* En vista normal acota la altura para scroll vertical */
        overflow-x: auto !important;
        overflow-y: auto !important; /* Habilita scrollbar vertical en normal y movil */
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-x pan-y !important;
        box-sizing: border-box !important;
        border-radius: 10px;
    }

    .card-agenda-fullscreen .agenda-timetable-scroll-wrap {
        height: calc(100vh - 130px) !important;
        max-height: calc(100vh - 130px) !important;
        overflow-x: auto !important;
        overflow-y: auto !important;
        box-sizing: border-box !important;
    }

    /* Estilos para encabezados pegajosos (sticky) durante el scroll vertical */
    .tt-header-cell {
        background: #0f172a !important;
    }
    [data-theme="light"] .tt-header-cell {
        background: #f8fafc !important;
    }

    .tt-time-cell {
        background: #0f172a !important;
    }
    [data-theme="light"] .tt-time-cell {
        background: #f8fafc !important;
    }


    /* SEPARACIÓN ESTRICTA ENTRE TABLA DE ESCRITORIO Y TARJETAS MÓVILES */
    @media (min-width: 768px) {
        .participantes-mobile-cards {
            display: none !important;
        }
        .participantes-desktop-table {
            display: block !important;
        }
    }

    @media (max-width: 767px) {
        .participantes-desktop-table {
            display: none !important;
        }
        .participantes-mobile-cards {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
            padding: 12px !important;
        }
    }


    /* ========================================================= */
    /* RESPONSIVIDAD Y AISLAMIENTO DE VISTAS (ACTIVIDADES, PROVEEDORES, PREMIOS) */
    /* ========================================================= */
    @media (min-width: 992px) {
        .actividades-desktop-table,
        .proveedores-desktop-table,
        .premios-desktop-table {
            display: block !important;
        }
        .actividades-mobile-cards,
        .proveedores-mobile-cards,
        .premios-mobile-cards {
            display: none !important;
        }
    }

    @media (max-width: 991px) {
        .actividades-desktop-table,
        .proveedores-desktop-table,
        .premios-desktop-table {
            display: none !important;
        }
        .actividades-mobile-cards,
        .proveedores-mobile-cards,
        .premios-mobile-cards {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            padding: 12px !important;
        }
    }

    @media (max-width: 991px) {
        .premios-split-grid {
            grid-template-columns: 1fr !important;
        }
    }


    /* SUBMÓDULOS DE GAFETE Y HORARIO DISPONIBLES ÚNICAMENTE EN VISTA PC (>= 992px) */
    @media (max-width: 991px) {
        .tab-btn-pc-only,
        .mobile-tab-btn[onclick*="tab-gafete"],
        .mobile-tab-btn[onclick*="tab-horario"] {
            display: none !important;
        }
    }





    /* VISTA DE ESCRITORIO DE PREMIOS (>= 992px): MOSTRAR LAS 3 SECCIONES AL MISMO TIEMPO */
    @media (min-width: 992px) {
        .premios-subtab-bar {
            display: none !important;
        }
        #premios-sub-catalog,
        #premios-sub-ranking,
        #premios-sub-tombola {
            display: block !important;
        }
        .premios-split-container {
            display: grid !important;
            grid-template-columns: 1.2fr 0.8fr !important;
            gap: 16px !important;
            margin-bottom: 20px !important;
        }
    }

    @media (max-width: 991px) {
        .premios-subtab-bar {
            display: flex !important;
        }
        .premios-split-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }
    }


    /* RESPONSIVIDAD Y SUB-PESTAÑAS DE PREMIOS (MÓVIL < 992px: 3 SUB-PESTAÑAS / PC >= 992px: TODO SIMULTÁNEO) */
    @media (min-width: 992px) {
        .premios-subtab-bar {
            display: none !important;
        }
        #premios-sub-catalog,
        #premios-sub-ranking,
        #premios-sub-tombola {
            display: block !important;
        }
        .premios-split-container {
            display: grid !important;
            grid-template-columns: 1.2fr 0.8fr !important;
            gap: 16px !important;
            margin-bottom: 20px !important;
        }
    }

    @media (max-width: 991px) {
        .premios-subtab-bar {
            display: flex !important;
        }
        .premios-split-container {
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }
        /* Ocultar por defecto en móvil el ranking y la tómbola para cargar solo el catálogo */
        #premios-sub-ranking,
        #premios-sub-tombola {
            display: none !important;
        }
        #premios-sub-catalog {
            display: block !important;
        }
    }


    /* RESPONSIVIDAD Y AISLAMIENTO VISTA TÓMBOLA */
    @media (min-width: 768px) {
        .tombola-desktop-table { display: block !important; }
        .tombola-mobile-cards { display: none !important; }
    }
    @media (max-width: 767px) {
        .tombola-desktop-table { display: none !important; }
        .tombola-mobile-cards { display: flex !important; flex-direction: column !important; }
    }


    /* ESTILOS DE MODAL COMPACTO MÓVIL Y CABECERA LIMPIA */
    .modal-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding-bottom: 12px !important;
        margin-bottom: 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        width: 100% !important;
    }
    .modal-title {
        margin: 0 !important;
        font-size: 16.5px !important;
        font-weight: 800 !important;
        color: var(--accent-gold, #f97316) !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .modal-close {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #94a3b8 !important;
        font-size: 18px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        line-height: 1 !important;
        padding: 0 !important;
    }
    .modal-close:hover {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #ef4444 !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }

        /* ESTILOS DE SELECT Y OPCIONES */
    select.form-control option,
    select option {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }

    /* ESTILOS DESKTOP PARA MODAL AGENDA */
    #modal-agenda .modal-content,
    #modal-agenda-edit .modal-content {
        padding: 24px !important;
    }

    .modal-agenda-grid {
        display: flex !important;
        flex-direction: row !important;
        gap: 24px !important;
        flex: 1 !important;
        min-height: 0 !important;
        overflow: hidden !important;
        padding-top: 8px !important;
    }
    .modal-agenda-form-col {
        width: 370px !important;
        flex-shrink: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 16px !important;
        overflow-y: auto !important;
        padding-right: 12px !important;
    }
    .modal-agenda-preview-col {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        min-width: 0 !important;
        overflow: hidden !important;
        padding-left: 12px !important;
    }
    .desktop-only-divider {
        width: 1px !important;
        background: rgba(255, 255, 255, 0.1) !important;
        margin: 0 4px !important;
    }
    .modal-footer-btns {
        display: flex !important;
        gap: 12px !important;
        margin-top: auto !important;
        padding-top: 16px !important;
    }
    .modal-footer-btns .btn {
        flex: 1 !important;
    }

    @media (max-width: 768px) {
        .modal-agenda-preview-col,
        .desktop-only-divider {
            display: none !important;
        }
        .modal-content {
            max-height: 92vh !important;
            overflow-y: auto !important;
            padding: 16px !important;
            border-radius: 8px !important;
        }
        .modal-agenda-grid {
            flex-direction: column !important;
            min-height: auto !important;
        }
        .modal-agenda-form-col {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
    }

</style>
@endpush

@section('content')
<!-- MOBILE STICKY TABS BAR — only visible on mobile screens -->
@if(auth()->check() && auth()->user()->Rol !== 'Evento')
<div class="mobile-tabs-bar" id="mobile-tabs-bar">
    <button class="mobile-tab-btn active" onclick="switchTab(this, 'tab-general')">General y Agenda</button>
    <button class="mobile-tab-btn" onclick="switchTab(this, 'tab-participantes')">Participantes <span class="badge badge-secondary">{{ $evento->participantes_count }}</span></button>
    <button class="mobile-tab-btn" onclick="switchTab(this, 'tab-actividades')">Actividades</button>
    <button class="mobile-tab-btn" onclick="switchTab(this, 'tab-proveedores')"><i class="bi bi-briefcase-fill" style="color:var(--accent-gold);"></i> Proveedores</button>
    <button class="mobile-tab-btn" onclick="switchTab(this, 'tab-premios')">Premios</button>

</div>
@endif





@if(auth()->check() && auth()->user()->Rol !== 'Evento')

<!-- INFO CARDS (KPIs) -->
@php
    $capacidad = $evento->capacidad > 0 ? $evento->capacidad : 1;
    $porcentajeOcupacion = min(100, round(($evento->participantes_count / $capacidad) * 100));
    $colorAforo = $porcentajeOcupacion >= 100 ? '#ef4444' : ($porcentajeOcupacion > 85 ? '#f59e0b' : '#10b981');
@endphp
<div id="kpi-cards-container" class="kpi-grid" style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    <div class="kpi-card" style="--kpi-color:{{ $colorAforo }}; display:block; padding:16px 20px;">
        <div style="display:flex; align-items:center; gap:16px; margin-bottom:12px;">
            <div class="kpi-icon" style="margin-bottom:0;"><i class="bi bi-people"></i></div>
            <div>
                <div class="kpi-value" style="display:flex; align-items:baseline; gap:4px;">
                    {{ $evento->participantes_count }} 
                    <span style="font-size:14px;color:var(--text-muted);font-weight:600;">/ {{ number_format($evento->capacidad) }}</span>
                </div>
                <div class="kpi-label">Aforo Registrado ({{ $porcentajeOcupacion }}%)</div>
            </div>
        </div>
        <div style="width:100%; height:6px; background:rgba(255,255,255,0.05); border-radius:4px; overflow:hidden; border:1px solid var(--border-subtle);">
            <div style="height:100%; background:var(--kpi-color); width:{{ $porcentajeOcupacion }}%; border-radius:4px; transition:width 1s ease;"></div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#3b82f6">
        <div class="kpi-icon"><i class="bi bi-mortarboard"></i></div>
        <div>
            <div class="kpi-value">{{ $evento->actividades_count }}</div>
            <div class="kpi-label">Actividades</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:#c9a227">
        <div class="kpi-icon"><i class="bi bi-calendar3"></i></div>
        <div>
            <div class="kpi-value">{{ $evento->agenda_count }}</div>
            <div class="kpi-label">Slots Agenda</div>
        </div>
    </div>
    <div class="kpi-card" style="--kpi-color:{{ $evento->estado === 'EN CURSO' ? '#10b981' : '#64748b' }}">
        <div class="kpi-icon"><i class="bi bi-activity"></i></div>
        <div>
            <div style="font-size:14px;font-weight:700;margin-top:4px">
                <span class="badge {{ $evento->badge_color }}">{{ $evento->estado }}</span>
            </div>
            <div class="kpi-label">Estado</div>
        </div>
    </div>
</div>
@endif

<!-- TAB 1: GENERAL Y AGENDA -->
<div id="tab-general" class="tab-pane" style="display:block;">
    <div class="event-dashboard-grid" style="display:grid; grid-template-columns:{{ (auth()->check() && auth()->user()->Rol === 'Evento') ? '1fr' : '320px minmax(0, 1fr)' }}; gap:20px;">
        @if(auth()->check() && auth()->user()->Rol !== 'Evento')
        <!-- DETALLES (DESK CARD) -->
        <div class="card detalles-desktop-card">
            <div class="card-header">
                <span class="card-title"><i class="bi bi-info-circle" style="color:var(--accent-gold);margin-right:8px"></i>Detalles</span>
            </div>
            <div class="card-body">
                @php
                    $filas = [
                        ['Inicio',     $evento->fecha_inicio->format('d/m/Y'), 'calendar3'],
                        ['Fin',        $evento->fecha_fin->format('d/m/Y'), 'calendar3-range'],
                        ['Duración',   $evento->duracion, 'clock'],
                        ['Ubicación',  $evento->ubicacion, 'geo-alt'],
                        ['Capacidad',  number_format($evento->capacidad) . ' personas', 'person-check'],
                        ['Puntos',     ucfirst($evento->tipo_puntos), 'star'],
                    ];
                @endphp
                @foreach($filas as [$label, $valor, $icon])
                <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-subtle)">
                    <i class="bi bi-{{ $icon }}" style="color:var(--accent-gold);font-size:14px;margin-top:2px;min-width:16px"></i>
                    <div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);font-weight:700">{{ $label }}</div>
                        <div style="font-size:13.5px;margin-top:2px">{{ $valor }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- DETALLES (MÓVIL STRIP COMPACTO) -->
        <div class="mobile-detalles-strip">
            <div class="mdet-item"><i class="bi bi-calendar3"></i> Inicio: <strong>{{ $evento->fecha_inicio->format('d/m/Y') }}</strong></div>
            <div class="mdet-item">•</div>
            <div class="mdet-item"><i class="bi bi-calendar3-range"></i> Fin: <strong>{{ $evento->fecha_fin->format('d/m/Y') }}</strong></div>
            <div class="mdet-item">•</div>
            <div class="mdet-item"><i class="bi bi-clock"></i> Duración: <strong>{{ $evento->duracion }}</strong></div>
            <div class="mdet-item">•</div>
            <div class="mdet-item"><i class="bi bi-geo-alt"></i> Ubicación: <strong>{{ $evento->ubicacion }}</strong></div>
            <div class="mdet-item">•</div>
            <div class="mdet-item"><i class="bi bi-person-check"></i> Capacidad: <strong>{{ number_format($evento->capacidad) }}</strong></div>
            <div class="mdet-item">•</div>
            <div class="mdet-item"><i class="bi bi-star"></i> Puntos: <strong>{{ ucfirst($evento->tipo_puntos) }}</strong></div>
        </div>
        @endif

        <!-- AGENDA -->
        <div class="card" id="agenda-main-card" style="min-width:0; max-width:100%; overflow:hidden;">
            <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="card-title"><i class="bi bi-calendar-event" style="color:var(--accent-gold);margin-right:8px"></i>Horarios (Agenda)</span>
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap; min-width:0;">
                    <div style="position:relative; min-width:100px; flex:1; max-width:220px;">
                        <i class="bi bi-search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
                        <input type="text" class="form-control form-control-sm" placeholder="Filtrar clases..." style="padding-left:30px; font-size:12px;" oninput="filterAgendaSlots(this.value)">
                    </div>
                    @if(auth()->check() && auth()->user()->Rol !== 'Evento')
                    <button type="button" class="btn btn-sm btn-primary" onclick="openAddAgendaModal()"><i class="bi bi-plus-lg"></i> Agregar</button>
                    @endif
                    <button type="button" id="btn-fullscreen-agenda" class="btn btn-sm btn-secondary" onclick="toggleAgendaFullscreen()" title="Pantalla Completa" style="font-weight:700; font-size:12px; display:inline-flex; align-items:center; gap:5px; padding:6px 10px; border-radius:8px;">
                        <i class="bi bi-arrows-fullscreen" id="icon-fullscreen-agenda"></i> <span class="d-none d-md-inline" id="lbl-fullscreen-agenda">Pantalla Completa</span>
                    </button>
                </div>
            </div>
            
            @php
                $period = \Carbon\CarbonPeriod::create($evento->fecha_inicio, $evento->fecha_fin);
                $agendaGrouped = $agenda->groupBy(function($item) {
                    return $item->Fecha->format('Y-m-d');
                });
                
                $allDates = collect($period)->map->format('Y-m-d')
                    ->concat($agendaGrouped->keys())
                    ->unique()
                    ->sort()
                    ->values();
            @endphp
            
            <!-- TABS POR FECHA -->
            @php
                // Collect all unique salones from agenda + configured salones
                $allSalones = $salones->pluck('Nombre')->toArray();
                if (empty($allSalones)) {
                    $allSalones = $agenda->pluck('Salon')->filter()->unique()->sort()->values()->toArray();
                }
                if (empty($allSalones)) {
                    $allSalones = ['Sin Salón'];
                }
            @endphp
            <div class="agenda-date-tabs-bar" style="display:flex; overflow-x:auto; border-bottom:1px solid var(--border-subtle); padding:10px 20px; gap:8px; flex-shrink:0; align-items:center; min-height:48px;">
                @foreach($allDates as $index => $dateStr)
                    @php $dateObj = \Carbon\Carbon::parse($dateStr); @endphp
                    <button class="tab-date-btn" data-date="{{ $dateStr }}"
                            onclick="switchAgendaDate('{{ $dateStr }}')"
                            style="padding:8px 16px; border:none; background:none; color:{{ $index === 0 ? 'var(--accent-gold)' : 'var(--text-muted)' }}; border-bottom:{{ $index === 0 ? '2px solid var(--accent-gold)' : '2px solid transparent' }}; cursor:pointer; font-weight:600; font-size:13px; white-space:nowrap; transition:all 0.2s ease;">
                        {{ $dateObj->locale('es')->isoFormat('D MMM, YYYY') }}
                    </button>
                @endforeach
            </div>

            <!-- TIMETABLE GRID por fecha -->
            <div style="padding:0; border-top:none; background:transparent;">
                @foreach($allDates as $index => $dateStr)
                    @php 
                        $slots = $agendaGrouped->get($dateStr, collect());
                        $slotsByTime = $slots->sortBy('Horario')->groupBy('Horario');
                        $allTimeSlots = $slotsByTime->keys()->sort()->values();
                    @endphp
                    <div id="agenda-date-{{ $dateStr }}" class="agenda-date-pane" style="display:{{ $index === 0 ? 'block' : 'none' }}; animation: fadeIn 0.3s ease;">
                        @if($slotsByTime->isEmpty())
                            <div style="text-align:center; padding:60px 30px; color:var(--text-muted);">
                                <i class="bi bi-calendar-x" style="font-size:40px;display:block;margin-bottom:16px;opacity:0.35; color:var(--accent-gold);"></i>
                                <div style="font-size:15px; font-weight:600; margin-bottom:6px;">Sin actividades programadas</div>
                                <div style="font-size:13px; opacity:0.7;">Usa el botón "Agregar" para añadir actividades a este día.</div>
                            </div>
                        @else
                            {{-- ============================================================ --}}
                            {{-- CSS GRID MATRIX TIMETABLE WITH ROW SPANNING                 --}}
                            {{-- Activities span vertically across their full time duration   --}}
                            {{-- ============================================================ --}}
                            @php
                                $numCols  = count($allSalones);
                                $gridCols = '85px ' . implode(' ', array_fill(0, max(1, $numCols), 'minmax(100px, 1fr)'));

                                // Determine time bounds for this date
                                $minStartMins = 9 * 60; // 09:00
                                $maxEndMins   = 18 * 60; // 18:00
                                if ($slots->isNotEmpty()) {
                                    $pStarts = [];
                                    $pEnds   = [];
                                    foreach ($slots as $s) {
                                        if ($s->Horario && strpos($s->Horario, '-') !== false) {
                                            list($hS, $hE) = explode('-', $s->Horario);
                                            if (strpos($hS, ':') !== false) {
                                                list($sh, $sm) = explode(':', trim($hS));
                                                $pStarts[] = ((int)$sh * 60) + (int)$sm;
                                            }
                                            if (strpos($hE, ':') !== false) {
                                                list($eh, $em) = explode(':', trim($hE));
                                                $pEnds[] = ((int)$eh * 60) + (int)$em;
                                            }
                                        }
                                    }
                                    if (!empty($pStarts)) $minStartMins = min($pStarts);
                                    if (!empty($pEnds))   $maxEndMins   = max($pEnds);
                                }
                                $startHour = max(0, (int)floor($minStartMins / 60) - 1);
                                $endHour   = min(24, (int)ceil($maxEndMins / 60) + 1);
                                if ($endHour - $startHour < 4) $endHour = min(24, $startHour + 4);

                                // 15-minute tracks (20px per track -> 80px per hour)
                                $totalTracks = ($endHour - $startHour) * 4;
                            @endphp

                            <div style="overflow-x:auto; overflow-y:visible; width:100%; -webkit-overflow-scrolling:touch; touch-action:pan-x pan-y;">
                                <div class="tt-grid-matrix" style="min-width:max-content; width:100%; display:grid; grid-template-columns:{{ $gridCols }}; grid-template-rows:42px repeat({{ $totalTracks }}, 20px); border-radius:10px; background:var(--bg-card); border:1px solid rgba(255,255,255,0.06); position:relative;">

                                    {{-- HEADER ROW (Row 1) --}}
                                    <div class="tt-header-cell" style="grid-column:1; grid-row:1; position:sticky; top:0; left:0; z-index:20; padding:8px 12px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid var(--accent-gold); border-right:1px solid var(--border); display:flex; align-items:center;">
                                        <i class="bi bi-clock" style="color:var(--accent-gold); margin-right:5px;"></i> Hora
                                    </div>
                                    @foreach($allSalones as $sIdx => $salonNombre)
                                    <div class="tt-header-cell" style="grid-column:{{ $sIdx + 2 }}; grid-row:1; position:sticky; top:0; z-index:10; padding:8px 12px; font-size:11px; font-weight:700; text-align:center; border-bottom:2px solid var(--accent-gold); border-left:1px solid var(--border); display:flex; align-items:center; justify-content:center; gap:5px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <i class="bi bi-door-open" style="color:var(--accent-gold); font-size:13px; flex-shrink:0;"></i>
                                        <span style="overflow:hidden; text-overflow:ellipsis;">{{ $salonNombre }}</span>
                                    </div>
                                    @endforeach

                                    {{-- TIME LABELS (Column 1) --}}
                                    @for($h = $startHour; $h < $endHour; $h++)
                                        @php
                                            $tIdx = ($h - $startHour) * 4;
                                            $rowStart = 2 + $tIdx;
                                            $label = sprintf('%02d:00', $h);
                                        @endphp
                                        <div class="tt-time-cell" style="grid-column:1; grid-row:{{ $rowStart }} / span 4; position:sticky; left:0; z-index:8; padding:0 12px; display:flex; align-items:center; border-right:1px solid var(--border); border-top:1px solid var(--border);">
                                            <span style="font-weight:700; font-size:11.5px; color:inherit;">{{ $label }}</span>
                                        </div>
                                    @endfor

                                    {{-- BACKGROUND GRID LINES & CELL TRACKS --}}
                                    @for($t = 0; $t < $totalTracks; $t++)
                                        @php
                                            $rIdx = 2 + $t;
                                            $isHourBorder = ($t % 4 === 0);
                                            $bdrTop = $isHourBorder ? '1px solid rgba(212,175,55,0.18)' : '1px dashed rgba(255,255,255,0.03)';
                                        @endphp
                                        @foreach($allSalones as $sIdx => $salonNombre)
                                            <div style="grid-column:{{ $sIdx + 2 }}; grid-row:{{ $rIdx }}; border-top:{{ $bdrTop }}; border-left:1px solid rgba(255,255,255,0.04); pointer-events:none;"></div>
                                        @endforeach
                                    @endfor

                                    {{-- ACTIVITY CARDS (WITH DYNAMIC ROW SPANNING!) --}}
                                    @foreach($slots as $slot)
                                        @php
                                            $salonIdx = array_search($slot->Salon, $allSalones);
                                            if ($salonIdx === false) $salonIdx = 0;
                                            $colIdx = 2 + $salonIdx;

                                            $sMins = $startHour * 60;
                                            $eMins = $endHour * 60;

                                            if ($slot->Horario && strpos($slot->Horario, '-') !== false) {
                                                list($hS, $hE) = explode('-', $slot->Horario);
                                                if (strpos($hS, ':') !== false) {
                                                    list($sh, $sm) = explode(':', trim($hS));
                                                    $sMins = ((int)$sh * 60) + (int)$sm;
                                                }
                                                if (strpos($hE, ':') !== false) {
                                                    list($eh, $em) = explode(':', trim($hE));
                                                    $eMins = ((int)$eh * 60) + (int)$em;
                                                }
                                            }

                                            $startTrack = max(0, (int)floor(($sMins - ($startHour * 60)) / 15));
                                            $endTrack   = min($totalTracks, (int)ceil(($eMins - ($startHour * 60)) / 15));
                                            if ($endTrack <= $startTrack) $endTrack = $startTrack + 2;

                                            $rStart = 2 + $startTrack;
                                            $rEnd   = 2 + $endTrack;

                                            $actividadObj = $actividades->firstWhere('Actividad', $slot->Actividad);
                                            $inscritos    = \DB::table('clase')->where('ID_Agenda', $slot->ID)->count();
                                            $capacidad    = $actividadObj ? $actividadObj->capacidad : null;
                                            $pct          = ($capacidad && $capacidad > 0) ? min(100, round(($inscritos / $capacidad) * 100)) : null;
                                            $pctColor     = $pct === null ? 'var(--accent-gold)' : ($pct >= 90 ? '#ef4444' : ($pct >= 60 ? '#f97316' : '#22c55e'));
                                        @endphp
                                        <div class="timetable-cell-card"
                                             style="grid-column:{{ $colIdx }}; grid-row:{{ $rStart }} / {{ $rEnd }}; z-index:12; margin:3px; background:linear-gradient(135deg,rgba(212,175,55,0.12) 0%,rgba(255,255,255,0.03) 100%); border:1px solid rgba(212,175,55,0.25); border-left:3.5px solid var(--accent-gold); border-radius:8px; padding:8px 10px; position:relative; transition:all 0.2s ease; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; {{ $actividadObj ? 'cursor:pointer;' : '' }}"
                                             @if($actividadObj) onclick="window.location.href='{{ route('actividades.show', $actividadObj->ID) }}?horario={{ $slot->ID }}&from_tab=tab-agenda'" @endif
                                             data-tt-nombre="{{ addslashes($slot->Actividad) }}"
                                             data-tt-horario="{{ $slot->Horario }}"
                                             data-tt-salon="{{ addslashes($slot->Salon ?? 'Sin Salón') }}"
                                             data-tt-inscritos="{{ $inscritos }}"
                                             data-tt-capacidad="{{ $capacidad ?? '—' }}"
                                             data-tt-pct="{{ $pct ?? '' }}"
                                             data-tt-pct-color="{{ $pctColor }}"
                                             data-tt-desc="{{ addslashes($actividadObj->Descripcion ?? '') }}"
                                             data-tt-exclusiva="{{ $actividadObj ? ($actividadObj->Exclusiva ? 'Sí' : 'No') : '' }}"
                                             data-tt-puntos="{{ $actividadObj->Puntos_Default ?? '' }}"
                                             onmouseenter="showTimetableTooltip(event, this)"
                                             onmouseleave="hideTimetableTooltip()"
                                             onmousemove="moveTimetableTooltip(event)">

                                            @if(auth()->check() && auth()->user()->Rol !== 'Evento')
                                            <div style="position:absolute; top:4px; right:4px; display:flex; gap:2px; z-index:15;" onclick="event.stopPropagation()">
                                                <button type="button" class="btn btn-sm"
                                                    onclick="openEditAgendaModal({{ $slot->ID }},'{{ addslashes($slot->Actividad) }}','{{ $slot->Fecha->format('Y-m-d') }}','{{ $slot->Horario }}','{{ addslashes($slot->Salon) }}')"
                                                    style="color:var(--text-muted);background:none;border:none;padding:2px 4px;font-size:11px;line-height:1;transition:color 0.2s;"
                                                    onmouseover="this.style.color='var(--accent-gold)'" onmouseout="this.style.color='var(--text-muted)'">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('agenda.destroy', $slot) }}" method="POST" class="delete-form" data-message="¿Eliminar el horario de '{{ $slot->Actividad }}'?" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <input type="hidden" name="active_tab" value="tab-general">
                                                    <button type="button" class="btn btn-sm btn-delete" style="color:rgba(239,68,68,0.6);background:none;border:none;padding:2px 4px;font-size:11px;line-height:1;transition:color 0.2s;"
                                                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='rgba(239,68,68,0.6)'">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif

                                            <div>
                                                <div style="font-size:12.5px;font-weight:700;color:var(--text-primary);padding-right:40px;line-height:1.25;margin-bottom:3px;">
                                                    {{ $slot->Actividad }}
                                                </div>
                                                <div style="font-size:10px; color:var(--accent-gold); font-weight:600; display:flex; align-items:center; gap:4px;">
                                                    <i class="bi bi-clock"></i> {{ $slot->Horario }}
                                                </div>
                                            </div>

                                            @if($actividadObj && $capacidad)
                                            <div style="display:flex;align-items:center;gap:6px;margin-top:4px;">
                                                <div style="flex:1;height:3.5px;background:rgba(255,255,255,0.1);border-radius:99px;overflow:hidden;">
                                                    <div style="width:{{ $pct }}%;height:100%;background:{{ $pctColor }};border-radius:99px;"></div>
                                                </div>
                                                <span style="font-size:10px;color:{{ $pctColor }};white-space:nowrap;font-weight:700;">{{ $inscritos }}/{{ $capacidad }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->Rol !== 'Evento')
<!-- TAB 2: PARTICIPANTES REDISEÑADO & RESPONSIVO -->
<div id="tab-participantes" class="tab-pane" style="display:none;">
    <!-- MINI METRICAS PARTICIPANTES -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-bottom:16px;">
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:rgba(212,175,55,0.15); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:18px;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700; letter-spacing:0.5px;">Registrados</div>
                <div style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ number_format($evento->participantes_count) }}</div>
            </div>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center; color:#10b981; font-size:18px;">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700; letter-spacing:0.5px;">Puntos Totales</div>
                <div style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ number_format($participantes->sum('Puntos')) }}</div>
            </div>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 16px; display:flex; align-items:center; gap:12px;">
            <div style="width:40px; height:40px; border-radius:10px; background:rgba(0,160,233,0.15); display:flex; align-items:center; justify-content:center; color:#00a0e9; font-size:18px;">
                <i class="bi bi-building"></i>
            </div>
            <div>
                <div style="font-size:11px; text-transform:uppercase; color:var(--text-muted); font-weight:700; letter-spacing:0.5px;">Sucursales</div>
                <div style="font-size:18px; font-weight:800; color:var(--text-primary); margin-top:2px;">{{ $participantes->pluck('Sucursal')->filter()->unique()->count() }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i class="bi bi-person-badge-fill" style="color:var(--accent-gold); font-size:18px;"></i>
                <span>Directorio de Participantes</span>
            </span>
            <a href="{{ route('participantes.index', ['evento' => $evento->ID]) }}" class="btn btn-sm btn-secondary" style="border-radius:8px; font-weight:600; font-size:12px;">
                <i class="bi bi-list-stars me-1"></i> Ver todos
            </a>
        </div>
        <div style="padding:14px 16px; background:rgba(10,15,30,0.3); border-bottom:1px solid rgba(255,255,255,0.08); display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            <div style="position:relative; flex:1; min-width:220px;">
                <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px;"></i>
                <input type="text" id="search-participantes" class="form-control form-control-sm" placeholder="Buscar por nombre, RFC, teléfono, sucursal..." style="padding-left:36px; font-size:12.5px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" oninput="filterTable('table-participantes', this.value, document.getElementById('filter-sucursal-participantes').value)">
            </div>
            <select id="filter-sucursal-participantes" class="form-control form-control-sm" style="width:170px; font-size:12.5px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" onchange="filterTable('table-participantes', document.getElementById('search-participantes').value, this.value)">
                <option value="">Todas las Sucursales</option>
                <option value="DIMEGSA">DIMEGSA</option>
                <option value="DEASA">DEASA</option>
                <option value="AIESA">AIESA</option>
                <option value="SEGSA">SEGSA</option>
                <option value="FESA">FESA</option>
                <option value="TAPATIA">TAPATIA</option>
                <option value="GABSA">GABSA</option>
                <option value="ILUMINACION">ILUMINACION</option>
                <option value="VALLARTA">VALLARTA</option>
                <option value="QUERETARO">QUERETARO</option>
                <option value="CODI">CODI</option>
            </select>
        </div>

        <!-- TABLA ESCRITORIO -->
        <div class="table-wrapper participantes-desktop-table">
            <table id="table-participantes" class="table align-middle" style="margin:0;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">ID</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Participante</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">RFC</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Sucursal</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px;">Proveedor</th>
                        <th style="padding:12px 16px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; text-align:center;">Puntos</th>
                        <th style="padding:12px 16px; text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $p)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04); transition:background 0.2s ease;">
                        <td style="padding:12px 16px; font-size:12px; font-weight:700; color:var(--text-muted);">#{{ $p->ID }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg, var(--accent-gold), #b38e1b); color:#000; font-weight:800; font-size:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    {{ strtoupper(substr($p->Nombre, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:13.5px; color:var(--text-primary);">{{ $p->Nombre }}</div>
                                    <div style="font-size:11px; color:var(--text-muted);">{{ $p->Telefono ?: 'Sin teléfono' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--text-muted);">{{ $p->RFC ?: '—' }}</td>
                        <td style="padding:12px 16px; font-size:12px;"><span class="badge" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:var(--text-secondary); padding:4px 8px; border-radius:6px;">{{ $p->Sucursal ?: 'Sin sucursal' }}</span></td>
                        <td style="padding:12px 16px; font-size:12px; color:var(--text-secondary);">{{ Str::limit($p->Proveedor, 22) ?: '—' }}</td>
                        <td style="padding:12px 16px; text-align:center;">
                            <span class="badge" style="background:linear-gradient(135deg, rgba(212,175,55,0.2), rgba(212,175,55,0.05)); border:1px solid rgba(212,175,55,0.3); color:var(--accent-gold); font-weight:800; font-size:12px; padding:5px 10px; border-radius:20px;">
                                <i class="bi bi-star-fill me-1" style="font-size:10px;"></i>{{ number_format($p->Puntos) }}
                            </span>
                        </td>
                        <td style="padding:12px 16px; text-align:right;">
                            <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm btn-secondary" style="border-radius:8px; padding:4px 10px;" title="Ver Perfil">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; padding:36px; color:var(--text-muted);">Sin participantes registrados en este evento.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- VISTA TARJETAS MÓVIL (COMPACTA Y ULTRA-ESTÉTICA) -->
        <div class="participantes-mobile-cards">
            @forelse($participantes as $p)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                    <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg, var(--accent-gold), #b38e1b); color:#000; font-weight:800; font-size:14px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        {{ strtoupper(substr($p->Nombre, 0, 1)) }}
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:700; font-size:13.5px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $p->Nombre }}</div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:1px; display:flex; gap:6px; flex-wrap:wrap;">
                            <span>{{ $p->Sucursal ?: 'General' }}</span>
                            <span>•</span>
                            <span style="color:var(--accent-gold); font-weight:700;">{{ number_format($p->Puntos) }} pts</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('participantes.show', $p) }}" class="btn btn-sm" style="background:rgba(212,175,55,0.15); border:1px solid rgba(212,175,55,0.3); color:var(--accent-gold); border-radius:8px; padding:6px 12px; font-weight:700; font-size:12px; flex-shrink:0; text-decoration:none;">
                    <i class="bi bi-eye"></i>
                </a>
            </div>
            @empty
            <div style="text-align:center; padding:32px; color:var(--text-muted); font-size:13px;">Sin participantes registrados</div>
            @endforelse
        </div>

        @if($participantes->hasPages())
        <div style="padding:12px 24px; border-top:1px solid var(--border-subtle);">
            {{ $participantes->links() }}
        </div>
        @endif
    </div>
</div>

<!-- TAB 3: ACTIVIDADES -->
<div id="tab-actividades" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center; flex-wrap:wrap; gap:10px;">
            <span class="card-title" style="display:flex; align-items:center; gap:8px;">
                <i class="bi bi-tags-fill" style="color:var(--accent-gold); font-size:18px;"></i>
                <span>Catálogo de Actividades</span>
            </span>
            <button type="button" class="btn btn-sm btn-primary" onclick="openAddActividadModal()" style="font-weight:700; border-radius:8px;">
                <i class="bi bi-plus-lg"></i> Agregar Actividad
            </button>
        </div>
        <div style="padding:14px 16px; background:rgba(10,15,30,0.3); border-bottom:1px solid rgba(255,255,255,0.08); display:flex; gap:12px; align-items:center;">
            <div style="position:relative; flex:1;">
                <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px;"></i>
                <input type="text" id="search-actividades" class="form-control form-control-sm" placeholder="Buscar actividad por nombre..." style="padding-left:36px; font-size:12.5px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" oninput="filterTable('table-actividades', this.value)">
            </div>
        </div>

        <!-- VISTA TARJETAS COMPLETA (UTILIZANDO EL 100% DEL ESPACIO DE IZQUIERDA A DERECHA) -->
        <div style="padding:16px; display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:16px;">
            @forelse($actividades as $act)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-left:4px solid var(--accent-gold); border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:12px; box-shadow:0 4px 14px rgba(0,0,0,0.25); transition:transform 0.2s ease;">
                <!-- Header: Icono + Titular a la izquierda, Badge a la derecha -->
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:18px; flex-shrink:0;">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div style="min-width:0;">
                            <a href="{{ route('actividades.show', $act->ID) }}?from_tab=tab-actividades" style="font-weight:800; font-size:15px; color:var(--text-primary); text-decoration:none; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $act->Actividad }}">
                                {{ $act->Actividad }}
                            </a>
                            <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">{{ Str::limit($act->Descripcion, 35) ?: 'Actividad de evento' }}</div>
                        </div>
                    </div>
                    <span class="badge" style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); color:var(--text-secondary); font-size:11px; padding:4px 8px; border-radius:6px; flex-shrink:0;">
                        {{ $act->Exclusiva ? '🌐 Exclusiva' : '🌐 Pública' }}
                    </span>
                </div>

                <!-- Info Grid: 2 Columnas abarcando todo el ancho -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; background:rgba(15,23,42,0.4); border:1px solid rgba(255,255,255,0.06); padding:10px 14px; border-radius:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-people-fill" style="color:var(--accent-gold); font-size:15px;"></i>
                        <div>
                            <div style="font-size:10px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Capacidad</div>
                            <div style="font-size:13px; font-weight:700; color:var(--text-primary);">{{ number_format($act->capacidad) }} pers.</div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <i class="bi bi-star-fill" style="color:var(--accent-gold); font-size:15px;"></i>
                        <div>
                            <div style="font-size:10px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Puntos</div>
                            <div style="font-size:13px; font-weight:800; color:var(--accent-gold);">{{ number_format($act->Puntos_Default) }} pts</div>
                        </div>
                    </div>
                </div>

                <!-- Footer: Acciones distribuidas a lo ancho -->
                <div style="display:flex; gap:8px; align-items:center; justify-content:space-between; border-top:1px solid rgba(255,255,255,0.06); padding-top:10px;">
                    <a href="{{ route('actividades.show', $act->ID) }}?from_tab=tab-actividades" class="btn btn-sm btn-primary" style="flex:1; justify-content:center; font-weight:700; font-size:12px; padding:7px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                        <i class="bi bi-bar-chart-line-fill"></i> Estadísticas
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" style="padding:7px 12px; border-radius:8px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);" onclick="editActividad({{ $act->ID }}, '{{ addslashes($act->Actividad) }}', '{{ addslashes($act->Descripcion) }}', {{ $act->capacidad }}, {{ $act->Puntos_Default }}, {{ $act->Exclusiva ? 1 : 0 }})" title="Editar">
                        <i class="bi bi-pencil" style="color:var(--accent-gold);"></i>
                    </button>
                    <form action="{{ route('actividades.destroy', $act) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar la actividad '{{ $act->Actividad }}'?">
                        @csrf @method('DELETE')
                        <input type="hidden" name="active_tab" value="tab-actividades">
                        <button type="submit" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444; padding:7px 12px; border-radius:8px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:32px; color:var(--text-muted); font-size:13px; grid-column:1/-1;">No hay actividades registradas.</div>
            @endforelse
        </div>
    </div>
</div>

<div id="tab-proveedores" class="tab-pane" style="display:none;">
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center; flex-wrap:wrap; gap:10px;">
            <span class="card-title" style="font-size:16px; font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="bi bi-building-gear" style="color:var(--accent-gold);"></i>
                <span>Proveedores Asignados a este Evento</span>
            </span>
            <button type="button" class="btn btn-sm btn-primary" onclick="openModal('modal-proveedor')" style="font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:8px;">
                <i class="bi bi-plus-circle"></i> Asignar Proveedor al Evento
            </button>
        </div>

        <div style="padding:14px 16px; background:rgba(10,15,30,0.3); border-bottom:1px solid rgba(255,255,255,0.08); display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
            <div style="position:relative; flex:1; min-width:240px;">
                <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:13px;"></i>
                <input type="text" id="search-proveedores" class="form-control form-control-sm" placeholder="Buscar por proveedor, contraseña o estatus..." style="padding-left:36px; font-size:12.5px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" oninput="filterTable('table-proveedores', this.value, document.getElementById('filter-estado-proveedores').value)">
            </div>
            <select id="filter-estado-proveedores" class="form-control form-control-sm" style="width:170px; font-size:12.5px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" onchange="filterTable('table-proveedores', document.getElementById('search-proveedores').value, this.value)">
                <option value="">Todos los Estados</option>
                <option value="Habilitado">🟢 Habilitado</option>
                <option value="Deshabilitado">🔴 Deshabilitado</option>
            </select>
        </div>

        <!-- VISTA TARJETAS PROVEEDORES (UTILIZANDO EL 100% DEL ESPACIO DE IZQUIERDA A DERECHA) -->
        <div style="padding:16px; display:grid; grid-template-columns:repeat(auto-fill, minmax(340px, 1fr)); gap:16px;">
            @forelse($proveedores as $prov)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-left:4px solid var(--accent-gold); border-radius:14px; padding:16px; display:flex; flex-direction:column; gap:12px; box-shadow:0 4px 14px rgba(0,0,0,0.25);">
                <!-- Header: Icono + Nombre a la izquierda, PIN a la derecha -->
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <div style="width:40px; height:40px; border-radius:10px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.3); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:18px; flex-shrink:0;">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:800; font-size:15px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $prov->NombreProveedor }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">Escaneo de Código QR</div>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="font-size:10px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Clave / PIN</div>
                        <span style="font-family:monospace; font-weight:800; font-size:13px; color:var(--accent-gold); background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.25); padding:2px 8px; border-radius:6px; display:inline-block; margin-top:2px;">
                            {{ $prov->password_visible ?: '***' }}
                        </span>
                    </div>
                </div>

                <!-- Event Box: Distribución completa en 2 filas -->
                <div style="background:rgba(15,23,42,0.4); border:1px solid rgba(255,255,255,0.06); padding:12px 14px; border-radius:10px; display:flex; flex-direction:column; gap:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
                        <span style="color:var(--accent-gold); font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                            <i class="bi bi-calendar-event"></i> {{ $evento->name_evento }}
                        </span>
                        <form action="{{ route('proveedores.update', $prov->ID) }}" method="POST" style="display:inline-flex; align-items:center; gap:4px; margin:0;">
                            @csrf @method('PUT')
                            <input type="hidden" name="active_tab" value="tab-proveedores">
                            <input type="hidden" name="Activo" value="{{ $prov->Activo ? 0 : 1 }}">
                            <button type="submit" style="border:1px solid {{ $prov->Activo ? 'rgba(34,197,94,0.4)' : 'rgba(239,68,68,0.4)' }}; cursor:pointer; font-size:11px; padding:3px 10px; font-weight:700; border-radius:6px; background:{{ $prov->Activo ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }}; color:{{ $prov->Activo ? '#4ade80' : '#f87171' }}; display:inline-flex; align-items:center; gap:4px;">
                                <i class="bi bi-power"></i> {{ $prov->Activo ? '🟢 Habilitado' : '🔴 Deshabilitado' }}
                            </button>
                        </form>
                    </div>

                    <div style="display:flex; align-items:center; justify-content:space-between; font-size:12px; border-top:1px solid rgba(255,255,255,0.06); padding-top:8px;">
                        <span style="color:var(--text-muted); font-weight:600;">Puntos otorgados por escaneo:</span>
                        <form action="{{ route('proveedores.update', $prov->ID) }}" method="POST" style="display:inline-flex; align-items:center; gap:6px; margin:0;">
                            @csrf @method('PUT')
                            <input type="hidden" name="active_tab" value="tab-proveedores">
                            <input type="number" name="Puntos" value="{{ $prov->Puntos }}" min="0" required class="form-control" style="width:65px; text-align:center; font-size:12px; font-weight:800; color:#0f172a; background:var(--accent-gold); border:none; border-radius:6px; padding:3px 6px;">
                            <button type="submit" class="btn btn-sm btn-secondary" style="padding:3px 8px; font-size:11px; font-weight:700; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15);" title="Guardar Puntos">
                                <i class="bi bi-check"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Footer Toolbar: Botones distribuidos a lo ancho -->
                <div style="display:flex; gap:8px; align-items:center; justify-content:space-between; border-top:1px solid rgba(255,255,255,0.06); padding-top:10px;">
                    @if(isset($prov->usuario_id) && $prov->usuario_id)
                    <button type="button" class="btn btn-sm btn-secondary" style="flex:1; justify-content:center; font-weight:600; font-size:12px; padding:7px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px;" onclick="openEditModalInEvent({{ $prov->usuario_id }}, '{{ addslashes($prov->NombreProveedor) }}', '{{ addslashes($prov->password_visible) }}')">
                        <i class="bi bi-pencil-square" style="color:var(--accent-gold);"></i> Modificar
                    </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-secondary" style="flex:1; justify-content:center; font-weight:600; font-size:12px; padding:7px 12px; border-radius:8px; display:inline-flex; align-items:center; gap:6px; color:var(--accent-gold); background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2);" onclick="openModal('modal-proveedor')">
                        <i class="bi bi-plus-circle"></i> Asignar Evento
                    </button>
                    <form action="{{ route('proveedores.destroy', $prov->ID) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar al proveedor '{{ $prov->NombreProveedor }}' de este evento?">
                        @csrf @method('DELETE')
                        <input type="hidden" name="active_tab" value="tab-proveedores">
                        <button type="submit" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444; padding:7px 12px; border-radius:8px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:32px; color:var(--text-muted); font-size:13px; grid-column:1/-1;">No hay proveedores asignados.</div>
            @endforelse
        </div>
    </div>
</div>

<div id="tab-premios" class="tab-pane" style="display:none;">
    
    <!-- 1. BANDA KPI COMPACTA Y HORIZONTAL -->
    <div style="display:flex; gap:10px; overflow-x:auto; padding-bottom:6px; scrollbar-width:none; -webkit-overflow-scrolling:touch; margin-bottom:14px;">
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:8px 12px; min-width:140px; flex-shrink:0;">
            <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">👑 Más Canjeado</div>
            <div style="font-size:13px; font-weight:800; color:var(--accent-gold); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                {{ $premioMasCanjeado->nombre ?? 'Sin canjes' }}
            </div>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:8px 12px; min-width:130px; flex-shrink:0;">
            <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">🎟️ Tómbola</div>
            <div style="font-size:13px; font-weight:800; color:#38bdf8;">
                {{ number_format($totalBoletosTombola) }} bol. ({{ $totalParticipantesTombola }} p.)
            </div>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:8px 12px; min-width:130px; flex-shrink:0;">
            <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">🎁 Entregados</div>
            <div style="font-size:13px; font-weight:800; color:#4ade80;">
                {{ number_format($totalPremiosEntregados) }} premios
            </div>
        </div>
        <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:8px 12px; min-width:120px; flex-shrink:0;">
            <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">📦 Stock Total</div>
            <div style="font-size:13px; font-weight:800; color:#c084fc;">
                {{ number_format($stockTotalPremios) }} uds
            </div>
        </div>
    </div>

    <!-- 2. BARRA DE 3 SUB-PESTAÑAS EN MÓVIL (CATÁLOGO, RANKING, TÓMBOLA) -->
    <div class="premios-subtab-bar" style="display:flex; gap:6px; margin-bottom:16px; background:rgba(15,23,42,0.6); padding:4px; border-radius:10px; border:1px solid rgba(255,255,255,0.08);">
        <button type="button" id="subtab-btn-catalog" onclick="switchPremiosSubtab('catalog')" style="flex:1; padding:8px 6px; font-size:12px; font-weight:800; border-radius:8px; border:none; background:var(--accent-gold); color:#0f172a; transition:all 0.2s;">
            <i class="bi bi-gift-fill me-1"></i> Catálogo
        </button>
        <button type="button" id="subtab-btn-ranking" onclick="switchPremiosSubtab('ranking')" style="flex:1; padding:8px 6px; font-size:12px; font-weight:700; border-radius:8px; border:none; background:transparent; color:var(--text-secondary); transition:all 0.2s;">
            <i class="bi bi-fire me-1"></i> Ranking
        </button>
        <button type="button" id="subtab-btn-tombola" onclick="switchPremiosSubtab('tombola')" style="flex:1; padding:8px 6px; font-size:12px; font-weight:700; border-radius:8px; border:none; background:transparent; color:var(--text-secondary); transition:all 0.2s;">
            <i class="bi bi-ticket-perforated-fill me-1"></i> Tómbola
        </button>
    </div>

    <!-- CONTENEDOR SUPERIOR EN REJILLA PARA ESCRITORIO: CATÁLOGO DE PREMIOS + RANKING DE LADO A LADO -->
    <div class="premios-split-container">
        <!-- SUB-SECCIÓN 1: CATÁLOGO DE PREMIOS (ARRIBA A LA IZQUIERDA EN ESCRITORIO) -->
        <div id="premios-sub-catalog" class="card" style="margin-bottom:0;">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; flex-wrap:wrap; gap:8px;">
                <span class="card-title" style="font-size:14px; font-weight:700; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-gift" style="color:var(--accent-gold);"></i> Catálogo Completo de Premios
                </span>
                <button type="button" class="btn btn-sm btn-primary" onclick="openAddPremioModal()" style="font-weight:700; border-radius:8px; font-size:12px; padding:5px 10px;">
                    <i class="bi bi-plus-lg"></i> Agregar Premio
                </button>
            </div>

            <div style="padding:10px 14px; background:rgba(10,15,30,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
                <div style="position:relative;">
                    <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
                    <input type="text" id="search-premios" class="form-control form-control-sm" placeholder="Buscar premio por nombre..." style="padding-left:34px; font-size:12px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" oninput="filterTable('table-premios', this.value)">
                </div>
            </div>

            <div style="padding:12px; display:flex; flex-direction:column; gap:8px; max-height:380px; overflow-y:auto;">
                @forelse($premios as $premio)
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-left:3px solid var(--accent-gold); border-radius:10px; padding:10px 14px; display:flex; justify-content:space-between; align-items:center; gap:10px;">
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <div style="width:36px; height:36px; border-radius:8px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.25); display:flex; align-items:center; justify-content:center; color:var(--accent-gold); font-size:16px; flex-shrink:0;">
                            <i class="bi bi-gift-fill"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:800; font-size:14px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="{{ $premio->NombrePremio }}">
                                {{ $premio->NombrePremio }}
                            </div>
                            <div style="display:flex; gap:8px; align-items:center; margin-top:2px; flex-wrap:wrap;">
                                <span class="badge badge-gold" style="font-size:10px; padding:2px 6px;">{{ number_format($premio->PuntosNecesarios) }} pts</span>
                                <span style="font-size:11.5px; color:var(--text-muted); font-weight:600;">Stock: {{ number_format($premio->Disponible) }} uds</span>
                                @if(($premio->TipoPremio ?? 'sorteo') === 'puntos')
                                    <span style="font-size:10px; color:#c084fc; font-weight:700;">🎫 Canje</span>
                                @else
                                    <span style="font-size:10px; color:#f97316; font-weight:700;">🎯 Sorteo</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                        <button type="button" class="btn btn-sm btn-secondary" style="font-size:11px; padding:5px 9px; border-radius:6px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);" onclick="editPremio({{ $premio->ID }}, '{{ addslashes($premio->NombrePremio) }}', '{{ $premio->TipoPremio ?? 'sorteo' }}', {{ $premio->PuntosNecesarios }}, {{ $premio->Disponible }})" title="Editar">
                            <i class="bi bi-pencil" style="color:var(--accent-gold);"></i>
                        </button>
                        <form action="{{ route('premios.destroy', $premio) }}" method="POST" style="display:inline;" class="delete-form" data-message="¿Eliminar el premio '{{ $premio->NombrePremio }}'?">
                            @csrf @method('DELETE')
                            <input type="hidden" name="active_tab" value="tab-premios">
                            <button type="submit" class="btn btn-sm btn-secondary btn-delete" style="color:#ef4444; padding:5px 9px; border-radius:6px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px; color:var(--text-muted); font-size:12.5px;">No hay premios registrados.</div>
                @endforelse
            </div>
        </div>

        <!-- SUB-SECCIÓN 2: RANKING DE LO MÁS CANJEADO (REDISEÑADO EN TARJETAS ELEGANTES) -->
        <div id="premios-sub-ranking" class="card" style="margin-bottom:0; display:none;">
            <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; flex-wrap:wrap; gap:8px;">
                <span class="card-title" style="font-size:14px; font-weight:700; display:flex; align-items:center; gap:6px;">
                    <i class="bi bi-fire" style="color:#f97316;"></i> Lo Más Canjeado (Ranking)
                </span>
                <a href="{{ route('eventos.canjes.index', $evento) }}" class="btn btn-sm btn-secondary" style="font-weight:700; font-size:11.5px; border-radius:8px; display:inline-flex; align-items:center; gap:5px; padding:5px 10px;">
                    <i class="bi bi-box-arrow-up-right"></i> Módulo Canjes
                </a>
            </div>
            <div style="padding:12px; display:flex; flex-direction:column; gap:10px; max-height:380px; overflow-y:auto;">
                @forelse($rankingCanjes as $index => $r)
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-left:4px solid {{ $index === 0 ? '#eab308' : ($index === 1 ? '#94a3b8' : ($index === 2 ? '#cd7f32' : 'var(--accent-gold)')) }}; border-radius:12px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center; gap:12px; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                    <!-- Izquierda: Medalla Rank + Nombre + Puntos + Stock -->
                    <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                        <div style="width:36px; height:36px; border-radius:10px; background:{{ $index === 0 ? 'rgba(234,179,8,0.15)' : 'rgba(212,175,55,0.12)' }}; border:1px solid {{ $index === 0 ? 'rgba(234,179,8,0.4)' : 'rgba(212,175,55,0.25)' }}; display:flex; align-items:center; justify-content:center; color:{{ $index === 0 ? '#eab308' : 'var(--accent-gold)' }}; font-weight:900; font-size:15px; flex-shrink:0;">
                            #{{ $index + 1 }}
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:800; font-size:14.5px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $r->nombre }}
                            </div>
                            <div style="display:flex; align-items:center; gap:6px; margin-top:2px;">
                                <span class="badge badge-gold" style="font-size:10.5px; padding:2px 6px;">{{ number_format($r->puntos) }} pts</span>
                                <span style="font-size:11.5px; color:var(--text-muted); font-weight:600;">Stock: {{ number_format($r->disponible) }} uds</span>
                            </div>
                        </div>
                    </div>

                    <!-- Derecha: Badge de Canjeados -->
                    <div style="flex-shrink:0; text-align:right;">
                        <div style="font-size:9.5px; text-transform:uppercase; color:var(--text-muted); font-weight:700;">Canjeados</div>
                        <span class="badge" style="background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); color:#4ade80; font-size:12px; padding:4px 10px; font-weight:800; border-radius:6px; display:inline-block; margin-top:2px;">
                            🎁 {{ number_format($r->total_unidades) }} uds
                        </span>
                    </div>
                </div>
                @empty
                <div style="text-align:center; padding:24px; color:var(--text-muted); font-size:12.5px;">Aún no hay canjes realizados.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SUB-SECCIÓN 3: BOLETOS EN TÓMBOLA (ABAJO A TODO LO ANCHO) -->
    <div id="premios-sub-tombola" class="card" style="margin-top:20px; margin-bottom:0; display:none;">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; flex-wrap:wrap; gap:8px;">
            <span class="card-title" style="font-size:14px; font-weight:700; display:flex; align-items:center; gap:6px;">
                <i class="bi bi-ticket-perforated-fill" style="color:var(--accent-gold);"></i> Boletos en Tómbola
            </span>
            <a href="{{ route('eventos.sorteo', $evento) }}" class="btn btn-sm btn-primary d-none d-md-inline-flex" style="font-weight:700; font-size:12px; border-radius:8px; align-items:center; gap:6px; padding:5px 10px;">
                <i class="bi bi-play-circle-fill"></i> Tómbola en Vivo
            </a>
        </div>

        @if(isset($boletosPorSucursal) && $boletosPorSucursal->isNotEmpty())
        <div style="padding:8px 14px; background:rgba(15,23,42,0.6); border-bottom:1px solid rgba(255,255,255,0.06); display:flex; gap:6px; overflow-x:auto; white-space:nowrap; scrollbar-width:none;">
            <span style="font-size:11px; font-weight:700; color:var(--text-muted); align-self:center; margin-right:2px;">Sucursal:</span>
            @foreach($boletosPorSucursal as $suc)
            <button type="button" class="btn btn-sm" onclick="filterTombolaTable('{{ $suc->sucursal === 'Sin Sucursal' ? '' : $suc->sucursal }}')" style="font-size:11px; padding:3px 10px; border-radius:20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:var(--text-secondary); white-space:nowrap;">
                <strong>{{ $suc->sucursal }}:</strong> <span style="color:var(--accent-gold);">{{ $suc->total_boletos }} bol.</span>
            </button>
            @endforeach
        </div>
        @endif

        <div style="padding:10px 14px; background:rgba(10,15,30,0.3); border-bottom:1px solid rgba(255,255,255,0.08);">
            <div style="position:relative;">
                <i class="bi bi-search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:12px;"></i>
                <input type="text" id="search-tombola-table" class="form-control form-control-sm" placeholder="Buscar participante o sucursal..." style="padding-left:34px; font-size:12px; border-radius:8px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12);" oninput="filterTombolaTable(this.value)">
            </div>
        </div>

        <!-- TABLA ESCRITORIO TÓMBOLA -->
        <div class="table-wrapper tombola-desktop-table" style="max-height:450px; overflow-y:auto;">
            <table id="table-tombola-boletos" class="table align-middle" style="margin:0;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.08);">
                        <th style="padding:10px 14px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Participante</th>
                        <th style="padding:10px 14px; font-size:11px; text-transform:uppercase; color:var(--text-secondary);">Sucursal</th>
                        <th style="padding:10px 14px; font-size:11px; text-transform:uppercase; color:var(--text-secondary); text-align:right;">Boletos Canjeados</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boletosTombolaData as $b)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.04);">
                        <td style="padding:10px 14px; font-weight:700; font-size:13px; color:var(--text-primary);">{{ $b->nombre }}</td>
                        <td style="padding:10px 14px; font-size:12px; color:var(--text-secondary);">{{ $b->sucursal ?: '—' }}</td>
                        <td style="padding:10px 14px; text-align:right;">
                            <span class="badge" style="background:rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; font-size:11.5px; padding:4px 8px; font-weight:800; border-radius:6px;">
                                🎟️ {{ $b->total_boletos }} boletos
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center; padding:24px; color:var(--text-muted); font-size:12.5px;">No hay boletos de tómbola canjeados aún.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- VISTA TARJETAS MÓVIL TÓMBOLA (APROVECHANDO EL 100% DEL ESPACIO DE IZQUIERDA A DERECHA) -->
        <div class="tombola-mobile-cards" style="padding:12px; display:flex; flex-direction:column; gap:10px; max-height:450px; overflow-y:auto;">
            @forelse($boletosTombolaData as $b)
            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-left:4px solid #38bdf8; border-radius:12px; padding:12px 14px; display:flex; justify-content:space-between; align-items:center; gap:12px; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                <!-- Izquierda: Icono Avatar + Nombre + Sucursal -->
                <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                    <div style="width:36px; height:36px; border-radius:10px; background:rgba(56,189,248,0.12); border:1px solid rgba(56,189,248,0.3); display:flex; align-items:center; justify-content:center; color:#38bdf8; font-size:18px; flex-shrink:0;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div style="min-width:0;">
                        <div style="font-weight:800; font-size:14.5px; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $b->nombre }}
                        </div>
                        <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                            <i class="bi bi-building me-1"></i>{{ $b->sucursal ?: 'Sin sucursal' }}
                        </div>
                    </div>
                </div>

                <!-- Derecha: Badge de Boletos Canjeados -->
                <div style="flex-shrink:0; text-align:right;">
                    <span class="badge" style="background:rgba(56,189,248,0.15); border:1px solid rgba(56,189,248,0.3); color:#38bdf8; font-size:12px; padding:6px 12px; font-weight:800; border-radius:8px; display:inline-flex; align-items:center; gap:5px;">
                        🎟️ <strong>{{ number_format($b->total_boletos) }}</strong> bol.
                    </span>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:24px; color:var(--text-muted); font-size:12.5px;">No hay boletos de tómbola canjeados aún.</div>
            @endforelse
        </div>
    </div>
</div>

<div id="tab-gafete" class="tab-pane" style="display:none;">
    <div class="card" style="align-self:stretch; width:100%; margin:0;">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-person-vcard" style="color:var(--accent-gold);margin-right:8px"></i>Diseño y Configuración de Gafete</span>
        </div>
        <div class="card-body" style="padding: 16px;">
            <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <!-- Campos ocultos requeridos por la validación -->
                <input type="hidden" name="name_evento" value="{{ $evento->name_evento }}">
                <input type="hidden" name="duracion" value="{{ $evento->duracion }}">
                <input type="hidden" name="estado" value="{{ $evento->estado }}">
                <input type="hidden" name="fecha_inicio" value="{{ $evento->fecha_inicio->format('Y-m-d') }}">
                <input type="hidden" name="fecha_fin" value="{{ $evento->fecha_fin->format('Y-m-d') }}">
                <input type="hidden" name="ubicacion" value="{{ $evento->ubicacion }}">
                <input type="hidden" name="capacidad" value="{{ $evento->capacidad }}">
                <input type="hidden" name="tipo_puntos" value="{{ $evento->tipo_puntos }}">
                <input type="hidden" name="active_tab" value="tab-gafete">

                <!-- Main designer grid layout -->
                <div style="display: flex; gap: 24px; align-items: stretch; justify-content: space-between; width: 100%;">
                    
                    <!-- Left Sidebar (Styles & Upload) -->
                    <div class="designer-sidebar" style="width: 260px; flex-shrink: 0; background: var(--bg-card); border: 1px solid rgba(255, 255, 255, 0.08); border-top: 3px solid var(--accent-gold); padding: 20px; border-radius: 12px; text-align: left; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                        <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Estilos</h4>
                        
                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; font-weight:bold; color:var(--text-primary);">Machote (Fondo)</label>
                            <input type="file" name="machote_gafete" accept="image/*" class="form-control" style="font-size:12px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary);">
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Fuente</label>
                            <select name="gafete_font_family" id="input-font-family" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                                <option value="Nexa" {{ ($evento->gafete_font_family ?? '') == 'Nexa' ? 'selected' : '' }}>Nexa</option>
                                <option value="Arial" {{ ($evento->gafete_font_family ?? '') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                <option value="Courier" {{ ($evento->gafete_font_family ?? '') == 'Courier' ? 'selected' : '' }}>Courier</option>
                                <option value="Times New Roman" {{ ($evento->gafete_font_family ?? '') == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Color de Nombre</label>
                            <input type="color" name="gafete_color_nombre" id="input-color-nombre" value="{{ $evento->gafete_color_nombre ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Color de ID</label>
                            <input type="color" name="gafete_color_id" id="input-color-id" value="{{ $evento->gafete_color_id ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                        </div>
                    </div>
                    
                    <!-- Center Canvas Area (Positioning & Preview stacked) -->
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 28px; align-items: center; justify-content: center; min-width: 0;">
                        
                        <!-- Position editor (Lienzo) -->
                        <div style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Lienzo (Arrastrar)</h4>
                            <div id="badge-editor-container" style="position:relative; display:inline-block; max-width:100%; width:540px; border-radius:8px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                                <img id="badge-template-img" src="{{ asset('storage/' . $evento->machote_gafete) }}" style="width:100%; display:block;">
                                
                                <!-- Draggable QR -->
                                <div id="draggable-qr" style="position:absolute; width:25%; aspect-ratio:1; border:2px solid var(--accent-gold); background:rgba(212,175,55,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                                    <i class="bi bi-qr-code" style="color:var(--accent-gold); font-size:20px;"></i>
                                </div>
                                
                                <!-- Draggable Name -->
                                <div id="draggable-name" style="position:absolute; width:50%; height:30px; border:2px solid #00bc8c; background:rgba(0,188,140,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                                    <span id="preview-nombre" style="color:{{ $evento->gafete_color_nombre ?? '#000000' }}; font-family:{{ $evento->gafete_font_family === 'Nexa' ? 'sans-serif' : ($evento->gafete_font_family === 'Courier' ? 'monospace' : ($evento->gafete_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:11px; font-weight:bold;">Nombre</span>
                                </div>

                                <!-- Draggable ID -->
                                <div id="draggable-id" style="position:absolute; width:20%; height:20px; border:2px solid #3b82f6; background:rgba(59,130,246,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                                    <span id="preview-id" style="color:{{ $evento->gafete_color_id ?? '#000000' }}; font-family:{{ $evento->gafete_font_family === 'Nexa' ? 'sans-serif' : ($evento->gafete_font_family === 'Courier' ? 'monospace' : ($evento->gafete_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:9px; font-weight:bold;">ID: 1234</span>
                                </div>
                            </div>
                            <small style="color:var(--text-muted); display:block; margin-top:8px; font-size:11px;">Arrastra los componentes para reubicarlos.</small>
                        </div>
                        
                        <!-- Final preview (Vista Previa) -->
                        <div style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Vista Previa Final</h4>
                            <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4); width:540px; background:var(--bg-secondary); padding:4px; border: 1px solid rgba(255,255,255,0.05);">
                                <img id="real-preview-image" src="{{ $mockGafete ? asset('storage/' . $mockGafete) . '?t=' . time() : '' }}" style="width:100%; display:block; border-radius:4px;" alt="Vista previa real del gafete generado">
                                <!-- Spinner superpuesto -->
                                <div id="preview-spinner" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
                                    <div class="spinner-border" style="color:var(--accent-gold);" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ $mockGafete ? asset('storage/' . $mockGafete) : '#' }}" download="Prueba_Gafete_{{ $evento->ID }}.jpg" class="btn btn-sm" style="margin-top:12px; font-size:12px; font-weight:700; padding:8px 16px; display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg, rgba(212,175,55,0.2) 0%, rgba(212,175,55,0.08) 100%); border:1px solid var(--accent-gold); color:var(--accent-gold); border-radius:8px; transition:all 0.2s ease;">
                                <i class="bi bi-download" style="font-size:14px;"></i> Descargar Prueba de Gafete (JPG)
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Sidebar (Sizing & Coordinates) -->
                    <div class="designer-sidebar" style="width: 280px; flex-shrink: 0; background: var(--bg-card); border: 1px solid rgba(255, 255, 255, 0.08); border-top: 3px solid var(--accent-gold); padding: 20px; border-radius: 12px; display: flex; flex-direction: column; gap: 16px; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                        <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Dimensiones</h4>
                        
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tamaño QR</span>
                                <span id="val-gafete-qr-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->gafete_qr_size ?? 25 }}%</span>
                            </label>
                            <input type="range" name="gafete_qr_size" min="10" max="50" value="{{ $evento->gafete_qr_size ?? 25 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tamaño Nombre</span>
                                <span id="val-gafete-font-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->gafete_font_size ?? 60 }}px</span>
                            </label>
                            <input type="range" name="gafete_font_size" min="20" max="150" value="{{ $evento->gafete_font_size ?? 60 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tamaño ID</span>
                                <span id="val-gafete-id-font-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->gafete_id_font_size ?? 40 }}px</span>
                            </label>
                            <input type="range" name="gafete_id_font_size" min="15" max="100" value="{{ $evento->gafete_id_font_size ?? 40 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        
                        <!-- Position Coordinates -->
                        <div style="border-top:1px solid rgba(255,255,255,0.05); padding-top:12px; margin-top:4px;">
                            <span style="font-size:10px; font-weight:bold; color:var(--text-muted); display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Coordenadas de Diseño</span>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">QR X</label><input type="number" name="gafete_qr_x" value="{{ $evento->gafete_qr_x ?? 1755 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">QR Y</label><input type="number" name="gafete_qr_y" value="{{ $evento->gafete_qr_y ?? 280 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Nombre X</label><input type="number" name="gafete_nombre_x" value="{{ $evento->gafete_nombre_x ?? 202 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Nombre Y</label><input type="number" name="gafete_nombre_y" value="{{ $evento->gafete_nombre_y ?? 1050 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">ID X</label><input type="number" name="gafete_id_x" value="{{ $evento->gafete_id_x ?? 202 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">ID Y</label><input type="number" name="gafete_id_y" value="{{ $evento->gafete_id_y ?? 1200 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary" style="width:100%; margin-top:8px; font-size:12px; font-weight:bold; padding:8px 12px;">Guardar Gafete</button>
                        <a href="{{ $mockGafete ? asset('storage/' . $mockGafete) : '#' }}" download="Prueba_Gafete_{{ $evento->ID }}.jpg" class="btn btn-sm btn-secondary" style="width:100%; margin-top:6px; font-size:11.5px; font-weight:bold; padding:7px 12px; display:inline-flex; align-items:center; justify-content:center; gap:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); color:var(--text-primary);">
                            <i class="bi bi-download"></i> Descargar Prueba
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($evento->machote_horario)
<!-- TAB 6: HORARIO -->
<div id="tab-horario" class="tab-pane" style="display:none;">
    <div class="card" style="align-self:stretch; width:100%; margin:0;">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-clock" style="color:var(--accent-gold);margin-right:8px"></i>Diseño y Configuración de Horario</span>
        </div>
        <div class="card-body" style="padding: 16px;">
            <form action="{{ route('eventos.update', $evento) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <!-- Campos ocultos requeridos por la validación -->
                <input type="hidden" name="name_evento" value="{{ $evento->name_evento }}">
                <input type="hidden" name="duracion" value="{{ $evento->duracion }}">
                <input type="hidden" name="estado" value="{{ $evento->estado }}">
                <input type="hidden" name="fecha_inicio" value="{{ $evento->fecha_inicio->format('Y-m-d') }}">
                <input type="hidden" name="fecha_fin" value="{{ $evento->fecha_fin->format('Y-m-d') }}">
                <input type="hidden" name="ubicacion" value="{{ $evento->ubicacion }}">
                <input type="hidden" name="capacidad" value="{{ $evento->capacidad }}">
                <input type="hidden" name="tipo_puntos" value="{{ $evento->tipo_puntos }}">
                <input type="hidden" name="active_tab" value="tab-horario">

                <!-- Main designer grid layout -->
                <div style="display: flex; gap: 24px; align-items: stretch; justify-content: space-between; width: 100%;">
                    
                    <!-- Left Sidebar (Styles & Upload) -->
                    <div class="designer-sidebar" style="width: 260px; flex-shrink: 0; background: var(--bg-card); border: 1px solid rgba(255, 255, 255, 0.08); border-top: 3px solid var(--accent-gold); padding: 20px; border-radius: 12px; text-align: left; display: flex; flex-direction: column; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                        <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Estilos</h4>
                        
                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; font-weight:bold; color:var(--text-primary);">Cambiar Machote</label>
                            <input type="file" name="machote_horario" accept="image/*" class="form-control" style="font-size:12px; background:var(--bg-primary); border:1px solid var(--border); color:var(--text-primary);">
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Fuente</label>
                            <select name="horario_font_family" id="input-horario-font-family" class="form-control" style="padding:4px 8px; font-size:12px; background:var(--bg-primary); color:var(--text-primary); border:1px solid var(--border);">
                                <option value="Nexa" {{ ($evento->horario_font_family ?? '') == 'Nexa' ? 'selected' : '' }}>Nexa</option>
                                <option value="Arial" {{ ($evento->horario_font_family ?? '') == 'Arial' ? 'selected' : '' }}>Arial</option>
                                <option value="Courier" {{ ($evento->horario_font_family ?? '') == 'Courier' ? 'selected' : '' }}>Courier</option>
                                <option value="Times New Roman" {{ ($evento->horario_font_family ?? '') == 'Times New Roman' ? 'selected' : '' }}>Times New Roman</option>
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Color de Nombre</label>
                            <input type="color" name="horario_color_nombre" id="input-horario-color-nombre" value="{{ $evento->horario_color_nombre ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Color de ID</label>
                            <input type="color" name="horario_color_id" id="input-horario-color-id" value="{{ $evento->horario_color_id ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                        </div>

                        <div class="form-group" style="margin-bottom:12px;">
                            <label style="font-size:11px; color:var(--text-primary);">Color de Lista</label>
                            <input type="color" name="horario_color_lista" id="input-horario-color-lista" value="{{ $evento->horario_color_lista ?? '#000000' }}" class="form-control" style="padding:0; height:30px; border:none; background:transparent;">
                        </div>
                    </div>
                    
                    <!-- Center Canvas Area (Positioning & Preview side-by-side) -->
                    <div style="flex: 1; display: flex; flex-direction: row; gap: 32px; align-items: center; justify-content: center; min-width: 0;">
                        
                        <!-- Position editor (Lienzo) -->
                        <div style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Lienzo (Arrastrar)</h4>
                            <div id="horario-editor-container" style="position:relative; display:inline-block; max-width:100%; width:400px; border-radius:8px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                                <img id="horario-template-img" src="{{ asset('storage/' . $evento->machote_horario) }}" style="width:100%; display:block;">
                                
                                <!-- Draggable Name -->
                                <div id="draggable-horario-name" style="position:absolute; width:50%; height:30px; border:2px solid #00bc8c; background:rgba(0,188,140,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                                    <span id="preview-horario-nombre" style="color:{{ $evento->horario_color_nombre ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:11px; font-weight:bold;">Nombre</span>
                                </div>

                                <!-- Draggable ID -->
                                <div id="draggable-horario-id" style="position:absolute; width:20%; height:20px; border:2px solid #3b82f6; background:rgba(59,130,246,0.2); cursor:move; display:flex; align-items:center; justify-content:center; top:0; left:0;">
                                    <span id="preview-horario-id" style="color:{{ $evento->horario_color_id ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:9px; font-weight:bold;">ID: 1234</span>
                                </div>
                                
                                <!-- Draggable Lista -->
                                <div id="draggable-horario-lista" style="position:absolute; width:60%; height:100px; border:2px dashed #f59e0b; background:rgba(245,158,11,0.2); cursor:move; display:flex; flex-direction:column; align-items:flex-start; padding:4px; top:0; left:0;">
                                    <span style="color:{{ $evento->horario_color_lista ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:8px; font-weight:bold;">09:00 - Registro</span>
                                    <span style="color:{{ $evento->horario_color_lista ?? '#000000' }}; font-family:{{ $evento->horario_font_family === 'Nexa' ? 'sans-serif' : ($evento->horario_font_family === 'Courier' ? 'monospace' : ($evento->horario_font_family === 'Times New Roman' ? 'serif' : 'Arial')) }}; font-size:8px; font-weight:bold;">10:00 - Conferencia 1</span>
                                </div>
                            </div>
                            <small style="color:var(--text-muted); display:block; margin-top:8px; font-size:11px;">Arrastra verde (Nombre), azul (ID) y naranja (Lista) para ubicarlos.</small>
                        </div>
                        
                        <!-- Final preview (Vista Previa) -->
                        <div style="text-align: center; display: flex; flex-direction: column; align-items: center;">
                            <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Vista Previa Final</h4>
                            <div style="position:relative; border-radius:8px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.4); width:400px; background:var(--bg-secondary); padding:4px; border: 1px solid rgba(255,255,255,0.05);">
                                <img id="real-preview-horario-image" src="{{ asset('storage/' . $mockHorario) }}?t={{ time() }}" style="width:100%; display:block; border-radius:4px;" alt="Vista previa real del horario generado">
                                <!-- Spinner superpuesto -->
                                <div id="preview-horario-spinner" style="display:none; position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
                                    <div class="spinner-border" style="color:var(--accent-gold);" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $mockHorario) }}" download="Prueba_Horario_{{ $evento->ID }}.jpg" class="btn btn-sm" style="margin-top:12px; font-size:12px; font-weight:700; padding:8px 16px; display:inline-flex; align-items:center; gap:6px; background:linear-gradient(135deg, rgba(212,175,55,0.2) 0%, rgba(212,175,55,0.08) 100%); border:1px solid var(--accent-gold); color:var(--accent-gold); border-radius:8px; transition:all 0.2s ease;">
                                <i class="bi bi-download" style="font-size:14px;"></i> Descargar Prueba de Horario (JPG)
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right Sidebar (Sizing & Coordinates) -->
                    <div class="designer-sidebar" style="width: 280px; flex-shrink: 0; background: var(--bg-card); border: 1px solid rgba(255, 255, 255, 0.08); border-top: 3px solid var(--accent-gold); padding: 20px; border-radius: 12px; display: flex; flex-direction: column; gap: 16px; text-align: left; box-shadow: 0 10px 30px rgba(0,0,0,0.35);">
                        <h4 style="font-size: 11px; font-weight: bold; color: var(--accent-gold); border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Dimensiones</h4>
                        
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tamaño Nombre</span>
                                <span id="val-horario-font-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->horario_font_size ?? 40 }}px</span>
                            </label>
                            <input type="range" name="horario_font_size" min="15" max="120" value="{{ $evento->horario_font_size ?? 40 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tamaño ID</span>
                                <span id="val-horario-id-font-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->horario_id_font_size ?? 30 }}px</span>
                            </label>
                            <input type="range" name="horario_id_font_size" min="10" max="80" value="{{ $evento->horario_id_font_size ?? 30 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Tam. Lista (Fuente)</span>
                                <span id="val-horario-lista-font-size" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->horario_lista_font_size ?? 24 }}px</span>
                            </label>
                            <input type="range" name="horario_lista_font_size" min="10" max="60" value="{{ $evento->horario_lista_font_size ?? 24 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Lista Ancho</span>
                                <span id="val-horario-lista-w" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->horario_lista_w ?? 800 }}px</span>
                            </label>
                            <input type="range" name="horario_lista_w" min="200" max="1500" value="{{ $evento->horario_lista_w ?? 800 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        <div class="form-group">
                            <label style="font-size:11px; color:var(--text-primary); display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span>Lista Alto</span>
                                <span id="val-horario-lista-h" style="color:var(--accent-gold); font-weight:bold;">{{ $evento->horario_lista_h ?? 1000 }}px</span>
                            </label>
                            <input type="range" name="horario_lista_h" min="200" max="1800" value="{{ $evento->horario_lista_h ?? 1000 }}" class="form-range" style="width:100%; height:6px; background:rgba(255,255,255,0.1); border-radius:4px; outline:none; -webkit-appearance:none; border:none !important; cursor:pointer;">
                        </div>
                        
                        <!-- Position Coordinates -->
                        <div style="border-top:1px solid rgba(255,255,255,0.05); padding-top:12px; margin-top:4px;">
                            <span style="font-size:10px; font-weight:bold; color:var(--text-muted); display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">Coordenadas de Diseño</span>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Nombre X</label><input type="number" name="horario_nombre_x" value="{{ $evento->horario_nombre_x ?? 202 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Nombre Y</label><input type="number" name="horario_nombre_y" value="{{ $evento->horario_nombre_y ?? 150 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">ID X</label><input type="number" name="horario_id_x" value="{{ $evento->horario_id_x ?? 202 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">ID Y</label><input type="number" name="horario_id_y" value="{{ $evento->horario_id_y ?? 250 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Lista X</label><input type="number" name="horario_lista_x" value="{{ $evento->horario_lista_x ?? 100 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                                <div class="form-group"><label style="font-size:9px; color:var(--text-muted); margin-bottom:2px; display:block;">Lista Y</label><input type="number" name="horario_lista_y" value="{{ $evento->horario_lista_y ?? 350 }}" class="form-control" style="padding:4px 6px; font-size:11px;"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary" style="width:100%; margin-top:8px; font-size:12px; font-weight:bold; padding:8px 12px;">Guardar Horario</button>
                        <a href="{{ asset('storage/' . $mockHorario) }}" download="Prueba_Horario_{{ $evento->ID }}.jpg" class="btn btn-sm btn-secondary" style="width:100%; margin-top:6px; font-size:11.5px; font-weight:bold; padding:7px 12px; display:inline-flex; align-items:center; justify-content:center; gap:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); color:var(--text-primary);">
                            <i class="bi bi-download"></i> Descargar Prueba
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modales (Nuevos) -->
<!-- Modal Actividad -->
<div id="modal-actividad" class="modal-overlay">
    <div class="modal-content" style="max-width: 580px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="bi bi-calendar-event-fill" style="color:var(--accent-gold);"></i> <span id="modal-actividad-title">Agregar Actividad</span>
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-actividad')">&times;</button>
        </div>
        <form id="form-actividad" method="POST" action="{{ route('eventos.actividades.store', $evento) }}">
            @csrf
            <input type="hidden" name="_method" id="form-actividad-method" value="POST">
            <input type="hidden" name="active_tab" value="tab-actividades">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div style="grid-column:1/-1;">
                    <label class="form-label">Nombre de la Actividad *</label>
                    <input name="Actividad" id="actividad-nombre" type="text" class="form-control" required placeholder="Ej: Conferencia Inicial">
                </div>
                <div style="grid-column:1/-1;">
                    <label class="form-label">Descripción *</label>
                    <textarea name="Descripcion" id="actividad-descripcion" class="form-control" rows="2" required placeholder="Breve descripción de la actividad"></textarea>
                </div>
                <div>
                    <label class="form-label">Capacidad *</label>
                    <input name="capacidad" id="actividad-capacidad" type="number" class="form-control" required value="100">
                </div>
                <div>
                    <label class="form-label">Puntos Default</label>
                    <input name="Puntos_Default" id="actividad-puntos" type="number" class="form-control" value="0">
                </div>
                <div style="grid-column:1/-1; display:flex; align-items:center; gap:8px; margin-top:6px;">
                    <input type="checkbox" name="Exclusiva" id="actividad-exclusiva" value="1" style="accent-color:var(--accent-gold); width:16px; height:16px;">
                    <label style="font-size:13px; margin:0; color:var(--text-primary); cursor:pointer;">¿Es Exclusiva?</label>
                </div>
            </div>
            <div style="margin-top:28px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-actividad')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Agenda -->
<div id="modal-agenda" class="modal-overlay">
    <div class="modal-content" style="max-width: 1200px; width: 95%; max-height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px; margin-bottom:16px; width:100%;">
            <h3 class="modal-title" style="margin:0; font-size:16.5px; font-weight:800; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-calendar-plus-fill"></i> Agregar Horario a la Agenda
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-agenda')" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:#94a3b8; font-size:18px; width:32px; height:32px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; line-height:1; padding:0;">&times;</button>
        </div>
        <form id="form-agenda-add" method="POST" action="{{ route('eventos.agenda.store', $evento) }}" onsubmit="document.getElementById('agenda-horario').value = document.getElementById('agenda-hora-inicio').value + '-' + document.getElementById('agenda-hora-fin').value" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
            @csrf
            <input type="hidden" name="active_tab" value="tab-general">
            
            <div class="modal-agenda-grid">
                <!-- Left Side: Form (Fixed width 360px) -->
                <div class="modal-agenda-form-col">
                    <div>
                        <label class="form-label" style="font-size:12px">Actividad *</label>
                        <select name="Actividad" id="agenda-actividad-select" class="form-control" required>
                            <option value="">Selecciona una actividad...</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->Actividad }}">{{ $act->Actividad }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label" style="font-size:12px">Fecha *</label>
                        <input name="Fecha" id="agenda-fecha-input" type="date" class="form-control" 
                               min="{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d') }}" 
                               max="{{ \Carbon\Carbon::parse($evento->fecha_fin)->format('Y-m-d') }}" 
                               required onchange="validateAgendaOverlap('add')">
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px">Hora Inicio *</label>
                            <input type="time" id="agenda-hora-inicio" class="form-control" required style="color:#fff; padding:7px 10px; font-size:13px; width:100%; box-sizing:border-box;" onchange="validateAgendaOverlap('add')">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">Hora Fin *</label>
                            <input type="time" id="agenda-hora-fin" class="form-control" required style="color:#fff; padding:7px 10px; font-size:13px; width:100%; box-sizing:border-box;" onchange="validateAgendaOverlap('add')">
                        </div>
                    </div>
                    <input type="hidden" name="Horario" id="agenda-horario">
                    
                    <div>
                        <label class="form-label" style="font-size:12px">Salón / Ubicación *</label>
                        @if($salones->isNotEmpty())
                            <select name="Salon" id="agenda-salon-select" class="form-control" required onchange="validateAgendaOverlap('add')">
                                <option value="">Selecciona un salón/espacio...</option>
                                @foreach($salones as $s)
                                    <option value="{{ $s->Nombre }}">{{ $s->Nombre }}</option>
                                @endforeach
                            </select>
                        @else
                            <input name="Salon" id="agenda-salon-input" type="text" class="form-control" list="salones-lista" required placeholder="Ej: Salón Principal" oninput="validateAgendaOverlap('add')">
                            <datalist id="salones-lista">
                                @foreach($agenda->pluck('Salon')->filter()->unique() as $salonExistente)
                                    <option value="{{ $salonExistente }}"></option>
                                @endforeach
                                @for($i = 1; $i <= $numSalones; $i++)
                                    <option value="Salón {{ $i }}"></option>
                                @endfor
                            </datalist>
                        @endif
                    </div>
                    
                    <div class="modal-footer-btns">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-agenda')">Cancelar</button>
                        <button type="submit" id="btn-agenda-add-submit" class="btn btn-primary">Guardar</button>
                    </div>
                </div>
                
                <!-- Vertical Divider -->
                <div class="desktop-only-divider" style="width: 1px; background: rgba(255,255,255,0.08);"></div>
                
                <!-- Right Side: Timeline Preview -->
                <div class="modal-agenda-preview-col" style="flex: 1; display: flex; flex-direction: column; min-width: 0; width: 100%;">
                    <h4 style="font-size: 13px; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; text-transform:uppercase; letter-spacing:0.5px;">
                        <span><i class="bi bi-clock-history"></i> Ocupación del Día (<span id="add-preview-date-text">-</span>)</span>
                        <span style="font-size: 10.5px; color: var(--text-muted); font-weight: 500; text-transform: none;"><i class="bi bi-grid-3x3-gap"></i> Vista Matriz por Salón</span>
                    </h4>
                    
                    <div id="add-agenda-overlap-warning" style="display:none; padding:10px 12px; border-radius:8px; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; font-size:12px; margin-bottom:10px; font-weight:600; line-height:1.4;">
                        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px; color:#ef4444;"></i> <span>¡El horario seleccionado se solapa en el mismo salón!</span>
                    </div>
                    
                    <div id="add-agenda-timeline-preview" style="flex: 1; overflow: auto; max-height: 440px; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; background: rgba(0,0,0,0.2); padding: 0; display: flex; flex-direction: column;">
                        <div style="color:var(--text-muted); text-align:center; padding:32px 16px; font-size:12.5px;">
                            Selecciona una fecha para ver la ocupación del día.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Agenda -->
<div id="modal-agenda-edit" class="modal-overlay">
    <div class="modal-content" style="max-width: 1200px; width: 95%; max-height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:12px; margin-bottom:16px; width:100%;">
            <h3 class="modal-title" style="margin:0; font-size:16.5px; font-weight:800; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-pencil-square"></i> Editar Horario de Agenda
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-agenda-edit')" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15); color:#94a3b8; font-size:18px; width:32px; height:32px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; line-height:1; padding:0;">&times;</button>
        </div>
        <form id="form-agenda-edit" method="POST" action="{{ route('agenda.update', 999999) }}" onsubmit="document.getElementById('edit_agenda_horario').value = document.getElementById('edit_agenda_hora_inicio').value + '-' + document.getElementById('edit_agenda_hora_fin').value" style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">
            @csrf @method('PUT')
            <input type="hidden" name="active_tab" value="tab-general">
            
            <div class="modal-agenda-grid">
                <!-- Left Side: Form (Fixed width 360px) -->
                <div class="modal-agenda-form-col">
                    <div>
                        <label class="form-label" style="font-size:12px">Actividad *</label>
                        <select id="edit_agenda_actividad" name="Actividad" class="form-control" required>
                            <option value="">Selecciona una actividad...</option>
                            @foreach($actividades as $act)
                                <option value="{{ $act->Actividad }}">{{ $act->Actividad }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label" style="font-size:12px">Fecha *</label>
                        <input id="edit_agenda_fecha" name="Fecha" type="date" class="form-control" 
                               min="{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d') }}" 
                               max="{{ \Carbon\Carbon::parse($evento->fecha_fin)->format('Y-m-d') }}" 
                               required onchange="validateAgendaOverlap('edit')">
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div>
                            <label class="form-label" style="font-size:12px">Hora Inicio *</label>
                            <input type="time" id="edit_agenda_hora_inicio" class="form-control" required style="color:#fff; padding:7px 10px; font-size:13px; width:100%; box-sizing:border-box;" onchange="validateAgendaOverlap('edit')">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:12px">Hora Fin *</label>
                            <input type="time" id="edit_agenda_hora_fin" class="form-control" required style="color:#fff; padding:7px 10px; font-size:13px; width:100%; box-sizing:border-box;" onchange="validateAgendaOverlap('edit')">
                        </div>
                    </div>
                    <input type="hidden" name="Horario" id="edit_agenda_horario">
                    
                    <div>
                        <label class="form-label" style="font-size:12px">Salón / Ubicación *</label>
                        @if($salones->isNotEmpty())
                            <select id="edit_agenda_salon" name="Salon" class="form-control" required onchange="validateAgendaOverlap('edit')">
                                <option value="">Selecciona un salón/espacio...</option>
                                @foreach($salones as $s)
                                    <option value="{{ $s->Nombre }}">{{ $s->Nombre }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="edit_agenda_salon" name="Salon" type="text" class="form-control" list="salones-lista-edit" required placeholder="Ej: Salón Principal" oninput="validateAgendaOverlap('edit')">
                            <datalist id="salones-lista-edit">
                                @foreach($agenda->pluck('Salon')->filter()->unique() as $salonExistente)
                                    <option value="{{ $salonExistente }}"></option>
                                @endforeach
                                @for($i = 1; $i <= $numSalones; $i++)
                                    <option value="Salón {{ $i }}"></option>
                                @endfor
                            </datalist>
                        @endif
                    </div>
                    
                    <div class="modal-footer-btns">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('modal-agenda-edit')">Cancelar</button>
                        <button type="submit" id="btn-agenda-edit-submit" class="btn btn-primary">Actualizar</button>
                    </div>
                </div>
                
                <!-- Vertical Divider -->
                <div class="desktop-only-divider" style="width: 1px; background: rgba(255,255,255,0.08);"></div>
                
                <!-- Right Side: Timeline Preview -->
                <div class="modal-agenda-preview-col" style="flex: 1; display: flex; flex-direction: column; min-width: 0; width: 100%;">
                    <h4 style="font-size: 13px; font-weight: 700; color: var(--accent-gold); margin-bottom: 10px; display: flex; align-items: center; justify- space-between; text-transform:uppercase; letter-spacing:0.5px;">
                        <span><i class="bi bi-clock-history"></i> Ocupación del Día (<span id="edit-preview-date-text">-</span>)</span>
                        <span style="font-size: 10.5px; color: var(--text-muted); font-weight: 500; text-transform: none;"><i class="bi bi-grid-3x3-gap"></i> Vista Matriz por Salón</span>
                    </h4>
                    
                    <div id="edit-agenda-overlap-warning" style="display:none; padding:10px 12px; border-radius:8px; background:rgba(239,68,68,0.15); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; font-size:12px; margin-bottom:10px; font-weight:600; line-height:1.4;">
                        <i class="bi bi-exclamation-triangle-fill" style="margin-right:6px; color:#ef4444;"></i> <span>¡El horario seleccionado se solapa en el mismo salón!</span>
                    </div>
                    
                    <div id="edit-agenda-timeline-preview" style="flex: 1; overflow: auto; max-height: 440px; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; background: rgba(0,0,0,0.2); padding: 0; display: flex; flex-direction: column;">
                        <div style="color:var(--text-muted); text-align:center; padding:32px 16px; font-size:12.5px;">
                            Selecciona una fecha para ver la ocupación del día.
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modificar Proveedor (Usuario / Contraseña) -->
<div id="modal-editar-proveedor-event" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="width:100%; max-width: 480px; background: var(--bg-card, #1e293b); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.7); position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:16px; margin-bottom:20px;">
            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-pencil-square"></i> Modificar Cuenta de Proveedor
            </h3>
            <button type="button" onclick="closeEditModalInEvent()" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        
        <form id="form-edit-proveedor-event" method="POST" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="active_tab" value="tab-proveedores">
            <div style="display:grid; gap:16px;">
                <div>
                    <label for="edit-username-event" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Nombre del Proveedor / Usuario *</label>
                    <input type="text" id="edit-username-event" name="username" class="form-control" required style="width:100%; font-size:14px;">
                </div>

                <div>
                    <label for="edit-password-event" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Nueva Contraseña (Opcional)</label>
                    <input type="text" id="edit-password-event" name="password" class="form-control" placeholder="Dejar en blanco para conservar la actual" style="width:100%; font-size:14px;">
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Escribe una nueva contraseña solo si deseas cambiarla.</small>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModalInEvent()" style="font-weight:600;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Proveedor -->
<div id="modal-proveedor" class="modal-overlay">
    <div class="modal-content" style="max-width: 520px; background: var(--bg-card, #1e293b); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.6);">
        <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:16px; margin-bottom:20px;">
            <h3 class="modal-title" style="margin:0; font-size:18px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-briefcase-fill"></i> Asignar Proveedor al Evento
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-proveedor')" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('eventos.proveedores.store', $evento) }}">
            @csrf
            <input type="hidden" name="active_tab" value="tab-proveedores">
            
            <div style="display:grid; gap:16px;">
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">
                        Seleccionar Cuenta de Proveedor *
                    </label>
                    <select name="NombreProveedor" id="select-proveedor-modal" class="form-control" required style="width:100%; font-size:14px;" onchange="toggleCustomProveedorInput(this)">
                        <option value="">-- Selecciona un proveedor registrado --</option>
                        @foreach($cuentasProveedores as $cProv)
                            <option value="{{ $cProv->username }}">{{ $cProv->username }}</option>
                        @endforeach
                        <option value="__NUEVO__">✍️ Ingresar Nombre Personalizado / Nuevo...</option>
                    </select>
                </div>

                <div id="custom-proveedor-container" style="display:none; background:rgba(212,175,55,0.05); padding:12px; border-radius:8px; border:1px dashed var(--accent-gold);">
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--accent-gold); margin-bottom:4px; display:block;">
                        Nombre del Nuevo Proveedor:
                    </label>
                    <input type="text" id="custom-proveedor-input" class="form-control" placeholder="Ej: Truper, Schneider, etc.">
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Se asignará este nombre como proveedor del evento.</small>
                </div>

                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">
                        Puntos que Otorgará por Escaneo *
                    </label>
                    <div style="position:relative;">
                        <input name="Puntos" type="number" class="form-control" required value="10" min="0" style="padding-left:36px; font-size:15px; font-weight:700; color:var(--accent-gold);">
                        <i class="bi bi-star-fill" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--accent-gold); font-size:14px;"></i>
                    </div>
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Puntos asignados al participante cada vez que el proveedor escanee su código QR.</small>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-proveedor')" style="font-weight:600;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Guardar Asignación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleCustomProveedorInput(select) {
        const customContainer = document.getElementById('custom-proveedor-container');
        const customInput = document.getElementById('custom-proveedor-input');
        if (select.value === '__NUEVO__') {
            customContainer.style.display = 'block';
            customInput.required = true;
            customInput.name = 'NombreProveedor';
            select.removeAttribute('name');
        } else {
            customContainer.style.display = 'none';
            customInput.required = false;
            customInput.removeAttribute('name');
            select.name = 'NombreProveedor';
        }
    }
</script>

<!-- Modal Premio -->
<div id="modal-premio" class="modal-overlay">
    <div class="modal-content" style="max-width: 540px;">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="bi bi-gift-fill" style="color:var(--accent-gold);"></i> <span id="modal-premio-title">Agregar Premio</span>
            </h3>
            <button type="button" class="modal-close" onclick="closeModal('modal-premio')">&times;</button>
        </div>
        <form id="form-premio" method="POST" action="{{ route('eventos.premios.store', $evento) }}">
            @csrf
            <input type="hidden" name="active_tab" value="tab-premios">
            <input type="hidden" name="_method" id="form-premio-method" value="POST">
            <div style="display:grid; gap:16px;">
                <div>
                    <label class="form-label">Nombre del Premio *</label>
                    <input name="NombrePremio" id="premio-nombre" type="text" class="form-control" required placeholder="Ej: Gorra Conmemorativa">
                </div>
                <div>
                    <label class="form-label">Modo de Entrega *</label>
                    <select name="TipoPremio" id="premio-tipo" class="form-control" required>
                        <option value="sorteo">🎯 Sorteo en Ruleta</option>
                        <option value="puntos">🎟️ Canje por Puntos</option>
                    </select>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label class="form-label">Puntos Necesarios *</label>
                        <input name="PuntosNecesarios" id="premio-puntos" type="number" class="form-control" required min="1" value="100">
                    </div>
                    <div>
                        <label class="form-label">Stock Disponible *</label>
                        <input name="Disponible" id="premio-stock" type="number" class="form-control" required min="0" value="10">
                    </div>
                </div>
            </div>
            <div style="margin-top:28px; display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-premio')">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Previsualización -->
<div id="previewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="background:var(--bg-secondary); border-radius:16px; width:100%; max-width:720px; max-height:85vh; display:flex; flex-direction:column; overflow:hidden; border:1px solid rgba(255,255,255,0.12); box-shadow:0 20px 50px rgba(0,0,0,0.6); position:relative;">
        <div style="padding:14px 20px; border-bottom:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; background:rgba(10,15,30,0.5);">
            <h3 id="modalTitle" style="margin:0; font-size:15px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-file-earmark-image"></i> Previsualización
            </h3>
            <button onclick="closePreview()" style="background:none; border:none; color:var(--text-secondary); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        <div style="padding:20px; overflow-y:auto; flex:1; display:flex; justify-content:center; align-items:center; background:rgba(5,8,18,0.7); min-height:300px;">
            <img id="modalImage" src="" style="max-width:100%; max-height:65vh; width:auto; height:auto; object-fit:contain; border-radius:8px; box-shadow:0 8px 30px rgba(0,0,0,0.5);">
        </div>
        <div style="padding:12px 20px; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; background:rgba(10,15,30,0.5);">
            <div style="display:flex; gap:8px;">
                <a id="modalDownloadBtn" href="" download class="btn btn-sm btn-primary" style="font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-download"></i> Descargar
                </a>
                <button type="button" onclick="printModalImage()" class="btn btn-sm btn-secondary" style="font-size:12px; font-weight:700; display:inline-flex; align-items:center; gap:6px; background:rgba(212,175,55,0.12); border:1px solid var(--accent-gold); color:var(--accent-gold);">
                    <i class="bi bi-printer-fill"></i> Imprimir Imagen
                </button>
            </div>
            <button onclick="closePreview()" class="btn btn-sm btn-secondary" style="font-size:12px;">Cerrar</button>
        </div>
    </div>
</div>

<script>
    function openPreview(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-file-earmark-image"></i> ' + title;
        document.getElementById('modalDownloadBtn').href = src;
        document.getElementById('previewModal').style.display = 'flex';
    }
    function closePreview() {
        document.getElementById('previewModal').style.display = 'none';
        document.getElementById('modalImage').src = '';
    }
    function printModalImage() {
        const imgSrc = document.getElementById('modalImage').src;
        if (!imgSrc) return;
        const printWin = window.open('', '_blank');
        printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Imprimir Documento</title>
                <style>
                    @page { margin: 0; size: auto; }
                    body { margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #fff; }
                    img { max-width: 100%; max-height: 100vh; object-fit: contain; }
                
    /* ESTILOS DE MODAL COMPACTO MÓVIL Y CABECERA LIMPIA */
    .modal-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding-bottom: 12px !important;
        margin-bottom: 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        width: 100% !important;
    }
    .modal-title {
        margin: 0 !important;
        font-size: 16.5px !important;
        font-weight: 800 !important;
        color: var(--accent-gold, #f97316) !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .modal-close {
        background: rgba(255, 255, 255, 0.08) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #94a3b8 !important;
        font-size: 18px !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        line-height: 1 !important;
        padding: 0 !important;
    }
    .modal-close:hover {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #ef4444 !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
    }

    @media (max-width: 768px) {
        .modal-agenda-preview-col,
        .desktop-only-divider {
            display: none !important;
        }
        .modal-content {
            max-height: 92vh !important;
            overflow-y: auto !important;
            padding: 16px !important;
            border-radius: 8px !important;
        }
        .modal-agenda-grid {
            flex-direction: column !important;
            min-height: auto !important;
        }
        .modal-agenda-form-col {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
    }

</style>
            </head>
            <body>
                <img src="${imgSrc}" onload="window.print(); setTimeout(() => window.close(), 500);" />
            </body>
            </html>
        `);
        printWin.document.close();
    }
    window.onclick = function(event) {
        let modal = document.getElementById('previewModal');
        if (event.target == modal) {
            closePreview();
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePreview();
    });

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formToSubmit = this;
            const msg = this.dataset.message || '¿Seguro que deseas eliminar esto?';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Confirmar eliminación?',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#475569',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    background: 'var(--bg-card, #1e293b)',
                    color: 'var(--text-primary, #f8fafc)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        formToSubmit.submit();
                    }
                });
            } else {
                if (confirm(msg)) {
                    formToSubmit.submit();
                }
            }
        });
    });

    function editActividad(id, nombre, descripcion, capacidad, puntos, exclusiva) {
        document.getElementById('modal-actividad-title').innerText = 'Editar Actividad';
        const form = document.getElementById('form-actividad');
        form.action = `{{ url('actividades') }}/${id}`;
        document.getElementById('form-actividad-method').value = 'PUT';
        
        document.getElementById('actividad-nombre').value = nombre;
        document.getElementById('actividad-descripcion').value = descripcion;
        document.getElementById('actividad-capacidad').value = capacidad;
        document.getElementById('actividad-puntos').value = puntos;
        document.getElementById('actividad-exclusiva').checked = (exclusiva == 1);
        
        openModal('modal-actividad');
    }

    function openAddActividadModal() {
        document.getElementById('modal-actividad-title').innerText = 'Agregar Actividad';
        const form = document.getElementById('form-actividad');
        form.action = `{{ route('eventos.actividades.store', $evento) }}`;
        document.getElementById('form-actividad-method').value = 'POST';
        
        document.getElementById('actividad-nombre').value = '';
        document.getElementById('actividad-descripcion').value = '';
        document.getElementById('actividad-capacidad').value = '100';
        document.getElementById('actividad-puntos').value = '0';
        document.getElementById('actividad-exclusiva').checked = false;
        
        openModal('modal-actividad');
    }

    function editPremio(id, nombre, tipo, puntos, stock) {
        document.getElementById('modal-premio-title').innerText = 'Editar Premio';
        const form = document.getElementById('form-premio');
        form.action = `{{ url('premios') }}/${id}`;
        document.getElementById('form-premio-method').value = 'PUT';
        
        document.getElementById('premio-nombre').value = nombre;
        document.getElementById('premio-tipo').value = tipo;
        document.getElementById('premio-puntos').value = puntos;
        document.getElementById('premio-stock').value = stock;
        
        openModal('modal-premio');
    }

    // Resetear form al abrir modal para agregar
    function openAddPremioModal() {
        document.getElementById('modal-premio-title').innerText = 'Agregar Premio';
        const form = document.getElementById('form-premio');
        form.action = `{{ route('eventos.premios.store', $evento) }}`;
        document.getElementById('form-premio-method').value = 'POST';
        
        document.getElementById('premio-nombre').value = '';
        document.getElementById('premio-tipo').value = 'sorteo';
        document.getElementById('premio-puntos').value = '100';
        document.getElementById('premio-stock').value = '10';
        
        openModal('modal-premio');
    }

    @if(auth()->check())
    function updateStock(id, delta) {
        fetch(`{{ url('premios') }}/${id}/stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ delta: delta })
        })
        .then(response => response.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('stock-val-' + id).innerText = data.nuevoStock;
            } else {
                alert(data.msg || 'Error al actualizar stock');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión');
        });
    }
    @endif

    // --- Buscador y Filtros Universales por Pestaña ---
    function filterTable(tableId, query = '', filterSelectVal = '') {
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.querySelectorAll('tbody tr');
        const q = query.toLowerCase().trim();
        const f = filterSelectVal.toLowerCase().trim();

        let visibleCount = 0;

        rows.forEach(row => {
            if (row.classList.contains('no-filter-matches-row')) return;

            const text = row.innerText.toLowerCase();
            const matchesQuery = !q || text.includes(q);
            const matchesFilter = !f || f === 'todos' || text.includes(f);

            if (matchesQuery && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let emptyRow = table.querySelector('.no-filter-matches-row');
        if (visibleCount === 0) {
            if (!emptyRow) {
                const tbody = table.querySelector('tbody');
                const colCount = table.querySelectorAll('thead th').length || 5;
                emptyRow = document.createElement('tr');
                emptyRow.className = 'no-filter-matches-row';
                emptyRow.innerHTML = `<td colspan="${colCount}" style="text-align:center; padding:32px; color:var(--text-muted);"><i class="bi bi-search" style="font-size:24px; display:block; margin-bottom:6px;"></i>No se encontraron resultados que coincidan con la búsqueda.</td>`;
                tbody.appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
            }
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    function filterAgendaSlots(query) {
        const q = query.toLowerCase().trim();
        const cards = document.querySelectorAll('.agenda-card, [data-actividad-id]');
        cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            if (!q || text.includes(q)) {
                card.style.opacity = '1';
                card.style.filter = 'none';
            } else {
                card.style.opacity = '0.15';
                card.style.filter = 'grayscale(1)';
            }
        });
    }

    // --- Control de Pestañas (Tabs) ---
    window.switchTab = function(btn, tabId) {
        if (tabId) {
            try {
                localStorage.setItem('evento_active_tab_' + '{{ $evento->ID }}', tabId);
            } catch(e) {}
        }
        // Desactivar estado activo y desenfocar para evitar sticky hover en móviles
        document.querySelectorAll('.tab-btn, .mobile-tab-btn').forEach(b => {
            b.classList.remove('active');
            if (b.blur) b.blur();
        });

        // Activar tanto el botón de escritorio como el de móvil de forma síncrona
        document.querySelectorAll(`.tab-btn[onclick*="${tabId}"], .mobile-tab-btn[onclick*="${tabId}"]`).forEach(b => {
            b.classList.add('active');
        });
        
        document.querySelectorAll('.tab-pane').forEach(p => p.style.display = 'none');
        const target = document.getElementById(tabId);
        if (target) target.style.display = 'block';

        // Mostrar u ocultar contenedor de KPI cards según la pestaña seleccionada
        const kpiContainer = document.getElementById('kpi-cards-container');
        if (kpiContainer) {
            if ((tabId === 'tab-general' || tabId === 'tab-participantes' || tabId === 'tab-actividades') && window.innerWidth >= 992) {
                kpiContainer.style.display = 'grid';
            } else {
                kpiContainer.style.display = 'none';
            }
        }

        if (tabId === 'tab-gafete' && typeof window.setupEditor === 'function') {
            window.setupEditor();
        }
        if (tabId === 'tab-horario' && typeof window.setupEditorHorario === 'function') {
            window.setupEditorHorario();
        }
    };

    // --- Drag and Drop Editor ---
    const container = document.getElementById('badge-editor-container');
    const img = document.getElementById('badge-template-img');
    const qr = document.getElementById('draggable-qr');
    const nameLabel = document.getElementById('draggable-name');
    const idLabel = document.getElementById('draggable-id');

    const qrXInput = document.getElementsByName('gafete_qr_x')[0];
    const qrYInput = document.getElementsByName('gafete_qr_y')[0];
    const nameXInput = document.getElementsByName('gafete_nombre_x')[0];
    const nameYInput = document.getElementsByName('gafete_nombre_y')[0];
    const idXInput = document.getElementsByName('gafete_id_x')[0];
    const idYInput = document.getElementsByName('gafete_id_y')[0];

    // Live preview elements
    const colorNombreInput = document.getElementById('input-color-nombre');
    const colorIdInput = document.getElementById('input-color-id');
    const fontSelector = document.getElementById('input-font-family');
    const previewNombre = document.getElementById('preview-nombre');
    const previewId = document.getElementById('preview-id');
    const previewSpinner = document.getElementById('preview-spinner');

    let updateTimeout;
    function updateRealPreview() {
        const form = document.querySelector('#tab-gafete form');
        if (!form) return;
        const formData = new FormData(form);
        formData.delete('machote_gafete');

        if (previewSpinner) previewSpinner.style.display = 'flex';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const realPreviewImg = document.getElementById('real-preview-image');
            if (realPreviewImg && data.mockGafeteUrl) {
                const newImg = new Image();
                newImg.onload = function() {
                    realPreviewImg.src = newImg.src;
                    if (previewSpinner) previewSpinner.style.display = 'none';
                };
                newImg.src = data.mockGafeteUrl;
            } else {
                if (previewSpinner) previewSpinner.style.display = 'none';
            }
        })
        .catch(err => {
            console.error('Error al actualizar vista previa:', err);
            if (previewSpinner) previewSpinner.style.display = 'none';
        });
    }

    if (colorNombreInput && previewNombre) {
        colorNombreInput.addEventListener('input', (e) => {
            previewNombre.style.color = e.target.value;
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(updateRealPreview, 500); // Debounce for color picker
        });
        colorIdInput.addEventListener('input', (e) => {
            previewId.style.color = e.target.value;
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(updateRealPreview, 500);
        });
        fontSelector.addEventListener('change', (e) => {
            let font = e.target.value === 'Nexa' ? 'sans-serif' : (e.target.value === 'Courier' ? 'monospace' : (e.target.value === 'Times New Roman' ? 'serif' : 'Arial'));
            previewNombre.style.fontFamily = font;
            previewId.style.fontFamily = font;
            updateRealPreview();
        });
    }

    if (img) {
        window.setupEditor = function() {
            const originalWidth = img.naturalWidth || 2000;
            const displayWidth = img.clientWidth;
            if (displayWidth === 0) return;
            const scale = displayWidth / originalWidth;

            // Posiciones iniciales (escaladas)
            const qrX = parseFloat(qrXInput.value) || 0;
            const qrY = parseFloat(qrYInput.value) || 0;
            const nameX = parseFloat(nameXInput.value) || 0;
            const nameY = parseFloat(nameYInput.value) || 0;
            const idX = parseFloat(idXInput ? idXInput.value : 0) || 0;
            const idY = parseFloat(idYInput ? idYInput.value : 0) || 0;

            const fontGafeteInput = document.getElementsByName('gafete_font_size')[0];
            const fontGafeteIdInput = document.getElementsByName('gafete_id_font_size')[0];
            const qrGafeteSizeInput = document.getElementsByName('gafete_qr_size')[0];

            const nameSize = parseFloat(fontGafeteInput.value) || 60;
            const idSize = parseFloat(fontGafeteIdInput.value) || 40;
            const qrSize = parseFloat(qrGafeteSizeInput ? qrGafeteSizeInput.value : 25) || 25;

            qr.style.left = (qrX * scale) + 'px';
            qr.style.top = (qrY * scale) + 'px';
            qr.style.width = qrSize + '%';

            nameLabel.style.left = (nameX * scale) + 'px';
            nameLabel.style.top = (nameY * scale) + 'px';
            previewNombre.style.fontSize = (nameSize * scale) + 'px';

            if (idLabel) {
                idLabel.style.left = (idX * scale) + 'px';
                idLabel.style.top = (idY * scale) + 'px';
                previewId.style.fontSize = (idSize * scale) + 'px';
            }
        };

        // Event listeners para los Sliders e Inputs de Coordenadas de Gafete (Sincronización Bidireccional)
        const fontGafeteInput = document.getElementsByName('gafete_font_size')[0];
        const fontGafeteIdInput = document.getElementsByName('gafete_id_font_size')[0];
        const qrGafeteSizeInput = document.getElementsByName('gafete_qr_size')[0];

        // Sincronizar inputs de coordenadas numéricas con el lienzo superior en tiempo real
        [qrXInput, qrYInput, nameXInput, nameYInput, idXInput, idYInput].forEach(inp => {
            if (inp) {
                inp.addEventListener('input', function() {
                    window.setupEditor();
                    clearTimeout(updateTimeout);
                    updateTimeout = setTimeout(updateRealPreview, 400);
                });
            }
        });

        if (fontGafeteInput) {
            fontGafeteInput.addEventListener('input', function(e) {
                document.getElementById('val-gafete-font-size').innerText = e.target.value + 'px';
                window.setupEditor();
            });
            fontGafeteInput.addEventListener('change', function() {
                updateRealPreview();
            });
        }
        if (fontGafeteIdInput) {
            fontGafeteIdInput.addEventListener('input', function(e) {
                document.getElementById('val-gafete-id-font-size').innerText = e.target.value + 'px';
                window.setupEditor();
            });
            fontGafeteIdInput.addEventListener('change', function() {
                updateRealPreview();
            });
        }
        if (qrGafeteSizeInput) {
            qrGafeteSizeInput.addEventListener('input', function(e) {
                document.getElementById('val-gafete-qr-size').innerText = e.target.value + '%';
                window.setupEditor();
            });
            qrGafeteSizeInput.addEventListener('change', function() {
                updateRealPreview();
            });
        }

        if (img.complete) {
            window.setupEditor();
        } else {
            img.onload = window.setupEditor;
        }

        function makeDraggable(element, onDrag) {
            let isDragging = false;
            let startX, startY;

            element.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - element.offsetLeft;
                startY = e.clientY - element.offsetTop;
                element.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                let left = e.clientX - startX;
                let top = e.clientY - startY;

                const containerRect = container.getBoundingClientRect();
                const elementRect = element.getBoundingClientRect();

                if (left < 0) left = 0;
                if (top < 0) top = 0;
                if (left + elementRect.width > containerRect.width) left = containerRect.width - elementRect.width;
                if (top + elementRect.height > containerRect.height) top = containerRect.height - elementRect.height;

                element.style.left = left + 'px';
                element.style.top = top + 'px';

                onDrag(left, top);
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    element.style.cursor = 'move';
                    updateRealPreview();
                }
            });
        }

        makeDraggable(qr, function(left, top) {
            const scale = img.naturalWidth / img.clientWidth;
            qrXInput.value = Math.round(left * scale);
            qrYInput.value = Math.round(top * scale);
        });

        makeDraggable(nameLabel, function(left, top) {
            const scale = img.naturalWidth / img.clientWidth;
            nameXInput.value = Math.round(left * scale);
            nameYInput.value = Math.round(top * scale);
        });

        if (idLabel) {
            makeDraggable(idLabel, function(left, top) {
                const scale = img.naturalWidth / img.clientWidth;
                idXInput.value = Math.round(left * scale);
                idYInput.value = Math.round(top * scale);
            });
        }
    }

    // --- Drag and Drop Editor Horario ---
    const hContainer = document.getElementById('horario-editor-container');
    const hImg = document.getElementById('horario-template-img');
    const hNameLabel = document.getElementById('draggable-horario-name');
    const hIdLabel = document.getElementById('draggable-horario-id');
    const hListaLabel = document.getElementById('draggable-horario-lista');

    const hNameXInput = document.getElementsByName('horario_nombre_x')[0];
    const hNameYInput = document.getElementsByName('horario_nombre_y')[0];
    const hIdXInput = document.getElementsByName('horario_id_x')[0];
    const hIdYInput = document.getElementsByName('horario_id_y')[0];
    const hListaXInput = document.getElementsByName('horario_lista_x')[0];
    const hListaYInput = document.getElementsByName('horario_lista_y')[0];
    const hListaWInput = document.getElementsByName('horario_lista_w')[0];
    const hListaHInput = document.getElementsByName('horario_lista_h')[0];

    const hColorNombreInput = document.getElementById('input-horario-color-nombre');
    const hColorIdInput = document.getElementById('input-horario-color-id');
    const hColorListaInput = document.getElementById('input-horario-color-lista');
    const hFontSelector = document.getElementById('input-horario-font-family');
    const hPreviewNombre = document.getElementById('preview-horario-nombre');
    const hPreviewId = document.getElementById('preview-horario-id');
    const hPreviewLista = document.getElementById('draggable-horario-lista');
    const hPreviewSpinner = document.getElementById('preview-horario-spinner');

    let hUpdateTimeout;
    function updateRealPreviewHorario() {
        const form = document.querySelector('#tab-horario form');
        const formData = new FormData(form);
        formData.delete('machote_horario');
        
        if(hPreviewSpinner) hPreviewSpinner.style.display = 'flex';

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            const realPreviewImg = document.getElementById('real-preview-horario-image');
            if(realPreviewImg) {
                const currentSrc = realPreviewImg.src.split('?')[0];
                const newImg = new Image();
                newImg.onload = function() {
                    realPreviewImg.src = newImg.src;
                    if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
                };
                newImg.src = currentSrc + '?t=' + new Date().getTime();
            } else {
                if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
            }
        }).catch(() => {
            if(hPreviewSpinner) hPreviewSpinner.style.display = 'none';
        });
    }

    if (hColorNombreInput && hPreviewNombre) {
        hColorNombreInput.addEventListener('input', (e) => {
            hPreviewNombre.style.color = e.target.value;
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hColorIdInput.addEventListener('input', (e) => {
            hPreviewId.style.color = e.target.value;
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hColorListaInput.addEventListener('input', (e) => {
            // Actualizar spans hijos
            hPreviewLista.querySelectorAll('span').forEach(span => {
                span.style.color = e.target.value;
            });
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 500);
        });
        hFontSelector.addEventListener('change', (e) => {
            let font = e.target.value === 'Nexa' ? 'sans-serif' : (e.target.value === 'Courier' ? 'monospace' : (e.target.value === 'Times New Roman' ? 'serif' : 'Arial'));
            hPreviewNombre.style.fontFamily = font;
            hPreviewId.style.fontFamily = font;
            hPreviewLista.querySelectorAll('span').forEach(span => {
                span.style.fontFamily = font;
            });
            updateRealPreviewHorario();
        });
        
        // Listeners manuales para los inputs de W y H para recargar preview
        hListaWInput.addEventListener('input', () => {
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 800);
        });
        hListaHInput.addEventListener('input', () => {
            clearTimeout(hUpdateTimeout);
            hUpdateTimeout = setTimeout(updateRealPreviewHorario, 800);
        });
    }

    if (hImg) {
        window.setupEditorHorario = function() {
            const originalWidth = hImg.naturalWidth || 2000;
            const displayWidth = hImg.clientWidth;
            if (displayWidth === 0) return;
            const scale = displayWidth / originalWidth;

            const nameX = parseFloat(hNameXInput.value) || 0;
            const nameY = parseFloat(hNameYInput.value) || 0;
            const idX = parseFloat(hIdXInput.value) || 0;
            const idY = parseFloat(hIdYInput.value) || 0;
            const listaX = parseFloat(hListaXInput.value) || 0;
            const listaY = parseFloat(hListaYInput.value) || 0;
            const listaW = parseFloat(hListaWInput.value) || 800;
            const listaH = parseFloat(hListaHInput.value) || 1000;

            const fontHorarioInput = document.getElementsByName('horario_font_size')[0];
            const fontHorarioIdInput = document.getElementsByName('horario_id_font_size')[0];
            const fontHorarioListaInput = document.getElementsByName('horario_lista_font_size')[0];

            const nameSize = parseFloat(fontHorarioInput.value) || 40;
            const idSize = parseFloat(fontHorarioIdInput.value) || 30;
            const listaSize = parseFloat(fontHorarioListaInput.value) || 24;

            hNameLabel.style.left = (nameX * scale) + 'px';
            hNameLabel.style.top = (nameY * scale) + 'px';
            hPreviewNombre.style.fontSize = (nameSize * scale) + 'px';

            if (hIdLabel) {
                hIdLabel.style.left = (idX * scale) + 'px';
                hIdLabel.style.top = (idY * scale) + 'px';
                hPreviewId.style.fontSize = (idSize * scale) + 'px';
            }
            if (hListaLabel) {
                hListaLabel.style.left = (listaX * scale) + 'px';
                hListaLabel.style.top = (listaY * scale) + 'px';
                hListaLabel.style.width = (listaW * scale) + 'px';
                hListaLabel.style.height = (listaH * scale) + 'px';
                hListaLabel.style.fontSize = (listaSize * scale) + 'px';
                hListaLabel.querySelectorAll('span').forEach(span => {
                    span.style.fontSize = (listaSize * scale) + 'px';
                });
            }
        };

        // Event listeners para los Sliders de Horario (tiempo real)
        const fontHorarioInput = document.getElementsByName('horario_font_size')[0];
        const fontHorarioIdInput = document.getElementsByName('horario_id_font_size')[0];
        const fontHorarioListaInput = document.getElementsByName('horario_lista_font_size')[0];

        if (fontHorarioInput) {
            fontHorarioInput.addEventListener('input', function(e) {
                document.getElementById('val-horario-font-size').innerText = e.target.value + 'px';
                window.setupEditorHorario();
            });
            fontHorarioInput.addEventListener('change', function() {
                updateRealPreviewHorario();
            });
        }
        if (fontHorarioIdInput) {
            fontHorarioIdInput.addEventListener('input', function(e) {
                document.getElementById('val-horario-id-font-size').innerText = e.target.value + 'px';
                window.setupEditorHorario();
            });
            fontHorarioIdInput.addEventListener('change', function() {
                updateRealPreviewHorario();
            });
        }
        if (fontHorarioListaInput) {
            fontHorarioListaInput.addEventListener('input', function(e) {
                document.getElementById('val-horario-lista-font-size').innerText = e.target.value + 'px';
                window.setupEditorHorario();
            });
            fontHorarioListaInput.addEventListener('change', function() {
                updateRealPreviewHorario();
            });
        }
        if (hListaWInput) {
            hListaWInput.addEventListener('input', function(e) {
                document.getElementById('val-horario-lista-w').innerText = e.target.value + 'px';
                window.setupEditorHorario();
            });
            hListaWInput.addEventListener('change', function() {
                updateRealPreviewHorario();
            });
        }
        if (hListaHInput) {
            hListaHInput.addEventListener('input', function(e) {
                document.getElementById('val-horario-lista-h').innerText = e.target.value + 'px';
                window.setupEditorHorario();
            });
            hListaHInput.addEventListener('change', function() {
                updateRealPreviewHorario();
            });
        }

        if (hImg.complete) {
            window.setupEditorHorario();
        } else {
            hImg.onload = window.setupEditorHorario;
        }

        function makeDraggableH(element, onDrag, containerRef) {
            let isDragging = false;
            let startX, startY;

            element.addEventListener('mousedown', function(e) {
                isDragging = true;
                startX = e.clientX - element.offsetLeft;
                startY = e.clientY - element.offsetTop;
                element.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (!isDragging) return;
                
                let left = e.clientX - startX;
                let top = e.clientY - startY;

                const containerRect = containerRef.getBoundingClientRect();
                const elementRect = element.getBoundingClientRect();

                if (left < 0) left = 0;
                if (top < 0) top = 0;
                if (left + elementRect.width > containerRect.width) left = containerRect.width - elementRect.width;
                if (top + elementRect.height > containerRect.height) top = containerRect.height - elementRect.height;

                element.style.left = left + 'px';
                element.style.top = top + 'px';

                onDrag(left, top);
            });

            document.addEventListener('mouseup', function() {
                if (isDragging) {
                    isDragging = false;
                    element.style.cursor = 'move';
                    updateRealPreviewHorario();
                }
            });
        }

        makeDraggableH(hNameLabel, function(left, top) {
            const scale = hImg.naturalWidth / hImg.clientWidth;
            hNameXInput.value = Math.round(left * scale);
            hNameYInput.value = Math.round(top * scale);
        }, hContainer);

        if (hIdLabel) {
            makeDraggableH(hIdLabel, function(left, top) {
                const scale = hImg.naturalWidth / hImg.clientWidth;
                hIdXInput.value = Math.round(left * scale);
                hIdYInput.value = Math.round(top * scale);
            }, hContainer);
        }
        
        if (hListaLabel) {
            makeDraggableH(hListaLabel, function(left, top) {
                const scale = hImg.naturalWidth / hImg.clientWidth;
                hListaXInput.value = Math.round(left * scale);
                hListaYInput.value = Math.round(top * scale);
            }, hContainer);
        }
    }

    // Cambiar Fecha de Agenda
    function switchAgendaDate(dateStr) {
        // Ocultar todas las tablas
        document.querySelectorAll('.agenda-date-pane').forEach(pane => pane.style.display = 'none');
        // Quitar active a todos los botones
        document.querySelectorAll('.tab-date-btn').forEach(btn => {
            btn.style.color = 'var(--text-muted)';
            btn.style.borderBottom = '2px solid transparent';
            btn.classList.remove('active');
        });

        // Mostrar el seleccionado
        const targetPane = document.getElementById('agenda-date-' + dateStr);
        if (targetPane) targetPane.style.display = 'block';

        // Estilizar el botón clickeado
        const targetBtn = document.querySelector(`.tab-date-btn[data-date="${dateStr}"]`);
        if (targetBtn) {
            targetBtn.style.color = 'var(--accent-gold)';
            targetBtn.style.borderBottom = '2px solid var(--accent-gold)';
            targetBtn.classList.add('active');
        }
    }

    // Live Agenda Timeline & Overlap Check
    const currentAgenda  = {!! $agendaJson !!};
    const allSalones     = {!! json_encode($salones->pluck('Nombre')->toArray()) !!};
    const allActividades = {!! json_encode($actividades->toArray()) !!};
    let editingAgendaId  = null;

    function validateAgendaOverlap(type) {
        const isEdit = (type === 'edit');
        const prefix = isEdit ? 'edit_' : 'agenda-';
        const previewPrefix = isEdit ? 'edit-' : 'add-';
        
        const dateInput = document.getElementById(`${isEdit ? 'edit_agenda_fecha' : 'agenda-fecha-input'}`);
        const startInput = document.getElementById(`${isEdit ? 'edit_agenda_hora_inicio' : 'agenda-hora-inicio'}`);
        const endInput = document.getElementById(`${isEdit ? 'edit_agenda_hora_fin' : 'agenda-hora-fin'}`);
        
        let salonInput = document.getElementById(`${isEdit ? 'edit_agenda_salon' : 'agenda-salon-select'}`);
        if (!salonInput) {
            salonInput = document.getElementById(`${isEdit ? 'edit_agenda_salon' : 'agenda-salon-input'}`);
        }
        
        const date = dateInput ? dateInput.value : '';
        const start = startInput ? startInput.value : '';
        const end = endInput ? endInput.value : '';
        const salon = salonInput ? salonInput.value.trim() : '';
        
        const previewDateText = document.getElementById(isEdit ? 'edit-preview-date-text'  : 'add-preview-date-text');
        const timelinePreview = document.getElementById(isEdit ? 'edit-agenda-timeline-preview' : 'add-agenda-timeline-preview');
        const overlapWarning  = document.getElementById(isEdit ? 'edit-agenda-overlap-warning'  : 'add-agenda-overlap-warning');
        const submitBtn       = document.getElementById(`btn-agenda-${type}-submit`);
        
        if (!date) {
            if (previewDateText) previewDateText.innerText = '-';
            if (timelinePreview) {
                timelinePreview.innerHTML = '<div style="color:var(--text-muted); text-align:center; padding:32px 16px; font-size:12.5px;">Selecciona una fecha para ver la ocupación del día.</div>';
            }
            if (overlapWarning) overlapWarning.style.display = 'none';
            if (submitBtn) submitBtn.disabled = false;
            return;
        }
        
        if (previewDateText) {
            const parts = date.split('-');
            previewDateText.innerText = `${parts[2]}/${parts[1]}/${parts[0]}`;
        }
        
        const daySlots = currentAgenda.filter(slot => {
            const slotDateStr = slot.Fecha.substring(0, 10);
            return slotDateStr === date;
        });
        
        daySlots.sort((a, b) => {
            const aStart = a.Horario.split('-')[0];
            const bStart = b.Horario.split('-')[0];
            return aStart.localeCompare(bStart);
        });
        
        // ── Determine columns: configured salones OR salones found in this day ──
        let gridSalones = allSalones.length ? [...allSalones] : [];
        daySlots.forEach(s => { if (s.Salon && !gridSalones.includes(s.Salon)) gridSalones.push(s.Salon); });
        if (salon && !gridSalones.includes(salon)) gridSalones.push(salon);
        if (!gridSalones.length) gridSalones = ['Sin Salón'];

        // ── Helper to parse "HH:MM" (or "HH:MM am/pm") to total minutes ──
        const parseMins = (tStr) => {
            if (!tStr) return 0;
            let s = tStr.trim().toLowerCase();
            let isPM = s.includes('p') || s.includes('pm');
            let isAM = s.includes('a') || s.includes('am');
            s = s.replace(/[^\d:]/g, '');
            if (!s.includes(':')) return 0;
            const p = s.split(':');
            let h = parseInt(p[0]) || 0;
            let m = parseInt(p[1]) || 0;
            if (isPM && h < 12) h += 12;
            if (isAM && h === 12) h = 0;
            return (h * 60) + m;
        };

        // ── Find min start and max end minutes ──
        let minMins = 9 * 60;
        let maxMins = 18 * 60;
        const allMins = [];

        daySlots.forEach(s => {
            if (s.Horario && s.Horario.includes('-')) {
                const parts = s.Horario.split('-');
                const sM = parseMins(parts[0]);
                const eM = parseMins(parts[1]);
                if (sM) allMins.push(sM);
                if (eM) allMins.push(eM);
            }
        });
        if (start && end) {
            const nSM = parseMins(start);
            const nEM = parseMins(end);
            if (nSM) allMins.push(nSM);
            if (nEM) allMins.push(nEM);
        }
        if (allMins.length) {
            minMins = Math.min(...allMins);
            maxMins = Math.max(...allMins);
        }

        let startHour = Math.max(0, Math.floor(minMins / 60) - 1);
        let endHour   = Math.min(24, Math.ceil(maxMins / 60) + 1);
        if (endHour - startHour < 4) endHour = Math.min(24, startHour + 4);

        // 15-minute tracks
        const totalTracks = (endHour - startHour) * 4;

        let hasOverlap = false;

        if (timelinePreview) {
            const numCols = gridSalones.length;
            const gridTpl = `80px ${Array(numCols).fill('1fr').join(' ')}`;

            let html = `<div style="min-width:${Math.max(340, numCols * 120 + 80)}px; font-size:11px; display:grid; grid-template-columns:${gridTpl}; grid-template-rows:35px repeat(${totalTracks}, 18px); background:var(--bg-secondary); border:1px solid var(--border); border-radius:12px; overflow:hidden; position:relative;">`;

            // Header row
            html += `<div class="tt-header-cell" style="grid-column:1; grid-row:1; position:sticky; top:0; left:0; z-index:20; padding:6px 8px; font-size:10px; text-transform:uppercase; letter-spacing:0.5px; font-weight:700; background:var(--bg-sidebar); color:var(--text-primary); border-bottom:2px solid var(--accent-gold); border-right:1px solid var(--border); display:flex; align-items:center;"><i class="bi bi-clock" style="color:var(--accent-gold);"></i></div>`;
            gridSalones.forEach((sNombre, sIdx) => {
                html += `<div class="tt-header-cell" style="grid-column:${sIdx+2}; grid-row:1; position:sticky; top:0; z-index:10; padding:6px 8px; font-size:10px; font-weight:700; background:var(--bg-sidebar); color:var(--text-primary); border-left:1px solid var(--border); text-align:center; border-bottom:2px solid var(--accent-gold); display:flex; align-items:center; justify-content:center; gap:4px;"><i class="bi bi-door-open" style="color:var(--accent-gold);"></i> ${sNombre}</div>`;
            });

            // Time Labels (Column 1)
            for (let h = startHour; h < endHour; h++) {
                const tIdx = (h - startHour) * 4;
                const rowStart = 2 + tIdx;
                const label = String(h).padStart(2, '0') + ':00';
                html += `<div class="tt-time-cell" style="grid-column:1; grid-row:${rowStart} / span 4; position:sticky; left:0; z-index:8; padding:0 8px; display:flex; align-items:center; background:var(--bg-sidebar); color:var(--accent-gold); border-right:1px solid var(--border); border-top:1px solid var(--border); font-weight:700; font-size:11px;">${label}</div>`;
            }

            // Background grid tracks
            for (let t = 0; t < totalTracks; t++) {
                const rIdx = 2 + t;
                const isHourBorder = (t % 4 === 0);
                const bdrTop = isHourBorder ? '1px solid var(--border)' : '1px dashed var(--border-subtle)';
                for (let sIdx = 0; sIdx < numCols; sIdx++) {
                    html += `<div style="grid-column:${sIdx+2}; grid-row:${rIdx}; border-top:${bdrTop}; border-left:1px solid rgba(255,255,255,0.04); pointer-events:none;"></div>`;
                }
            }

            // Place existing day slots
            daySlots.forEach(slot => {
                if (isEdit && slot.ID == editingAgendaId) return;

                const sName = slot.Salon || 'Sin Salón';
                const sIdx  = gridSalones.indexOf(sName);
                if (sIdx === -1) return;

                let slotStart = '', slotEnd = '';
                if (slot.Horario && slot.Horario.includes('-')) {
                    const parts = slot.Horario.split('-');
                    slotStart = parts[0];
                    slotEnd   = parts[1];
                }

                const sM = parseMins(slotStart);
                const eM = parseMins(slotEnd);
                const startTrack = Math.max(0, Math.floor((sM - startHour*60) / 15));
                let endTrack     = Math.min(totalTracks, Math.ceil((eM - startHour*60) / 15));
                if (endTrack <= startTrack) endTrack = startTrack + 2;

                const rStart = 2 + startTrack;
                const rEnd   = 2 + endTrack;
                const colIdx = 2 + sIdx;

                // Check overlap with the NEW input time
                let isSameSalon = (salon && sName.toLowerCase().trim() === salon.toLowerCase().trim());
                let isTimeOverlap = false;
                if (start && end && slotStart && slotEnd) {
                    const nSM = parseMins(start);
                    const nEM = parseMins(end);
                    if (nSM < eM && sM < nEM) isTimeOverlap = true;
                }
                const isConflicting = isSameSalon && isTimeOverlap;
                if (isConflicting) hasOverlap = true;

                const actObj = allActividades.find(a => a.Actividad === slot.Actividad);
                const bg    = isConflicting ? 'rgba(239,68,68,0.25)'        : 'linear-gradient(135deg,rgba(212,175,55,0.12),rgba(255,255,255,0.03))';
                const bdr   = isConflicting ? '#ef4444'                     : 'var(--accent-gold)';
                const color = isConflicting ? '#fca5a5'                     : 'var(--text-primary)';

                const desc = actObj ? (actObj.Descripcion || '') : '';
                const cap  = actObj ? (actObj.capacidad || '—') : '—';

                html += `<div class="timetable-cell-card"
                              style="grid-column:${colIdx}; grid-row:${rStart} / ${rEnd}; z-index:12; margin:2px; background:${bg}; border:1px solid ${bdr}; border-left:3px solid ${bdr}; border-radius:5px; padding:4px 6px; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer;"
                              data-tt-nombre="${slot.Actividad.replace(/"/g, '&quot;')}"
                              data-tt-horario="${slot.Horario}"
                              data-tt-salon="${sName.replace(/"/g, '&quot;')}"
                              data-tt-capacidad="${cap}"
                              data-tt-desc="${desc.replace(/"/g, '&quot;')}"
                              onmouseenter="showTimetableTooltip(event, this)"
                              onmouseleave="hideTimetableTooltip()"
                              onmousemove="moveTimetableTooltip(event)">`;

                html += `<div style="font-weight:700; color:${color}; font-size:10.5px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; line-height:1.2;">`;
                if (isConflicting) html += `<span style="color:#ef4444; margin-right:3px;">⚠</span>`;
                html += `${slot.Actividad}</div>`;

                html += `<div style="font-size:9px; color:rgba(255,255,255,0.6); margin-top:1px;"><i class="bi bi-clock"></i> ${slot.Horario}</div>`;

                if (isConflicting) {
                    html += `<div style="font-size:8.5px; font-weight:800; color:#ef4444; margin-top:1px; text-transform:uppercase;">¡SOLAPAMIENTO!</div>`;
                }

                html += `</div>`;
            });

            // Place NEW slot preview card ONLY if salon is selected
            if (start && end && salon) {
                const sIdx = gridSalones.findIndex(s => s.toLowerCase().trim() === salon.toLowerCase().trim());
                if (sIdx !== -1) {
                    const nSM = parseMins(start);
                    const nEM = parseMins(end);
                    if (nSM && nEM && nEM > nSM) {
                        const nSTrack = Math.max(0, Math.floor((nSM - startHour*60) / 15));
                        let nETrack   = Math.min(totalTracks, Math.ceil((nEM - startHour*60) / 15));
                        if (nETrack <= nSTrack) nETrack = nSTrack + 2;

                        const nRStart = 2 + nSTrack;
                        const nREnd   = 2 + nETrack;
                        const nColIdx = 2 + sIdx;

                        const bdrColor = hasOverlap ? '#ef4444' : 'var(--accent-gold)';
                        const bgColor  = hasOverlap ? 'rgba(239,68,68,0.2)' : 'rgba(212,175,55,0.18)';

                        html += `<div style="grid-column:${nColIdx}; grid-row:${nRStart} / ${nREnd}; z-index:15; margin:2px; background:${bgColor}; border:1.5px dashed ${bdrColor}; border-radius:5px; padding:4px 6px; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:center; box-shadow:0 0 12px rgba(212,175,55,0.2);">`;
                        html += `<span style="font-size:10px; color:${bdrColor}; font-weight:800;">✦ Nueva Actividad</span>`;
                        html += `<span style="font-size:9px; color:#fff; font-weight:600; margin-top:2px;">${start} - ${end}</span>`;
                        html += `</div>`;
                    }
                }
            }

            html += `</div>`;
            timelinePreview.innerHTML = html;
        }
        
        if (hasOverlap) {
            if (overlapWarning) overlapWarning.style.display = 'block';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
        } else {
            if (overlapWarning) overlapWarning.style.display = 'none';
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            }
        }
    }

    // Modal Edit Agenda Helper
    function openEditAgendaModal(id, actividad, fecha, horario, salon) {
        editingAgendaId = id;
        
        let baseUrl = "{{ route('agenda.update', 999999) }}";
        document.getElementById('form-agenda-edit').action = baseUrl.replace('999999', id);
        
        document.getElementById('edit_agenda_actividad').value = actividad;
        document.getElementById('edit_agenda_fecha').value = fecha;
        
        if (horario && horario.includes('-')) {
            const parts = horario.split('-');
            document.getElementById('edit_agenda_hora_inicio').value = parts[0];
            document.getElementById('edit_agenda_hora_fin').value = parts[1];
        }
        
        const salonEl = document.getElementById('edit_agenda_salon');
        if (salonEl) {
            salonEl.value = salon || '';
        }
        
        validateAgendaOverlap('edit');
        openModal('modal-agenda-edit');
    }
    /* ─── Timetable Tooltip JS ─────────────────────────────────── */
    let _ttTimer = null;

    function showTimetableTooltip(e, card) {
        clearTimeout(_ttTimer);
        _ttTimer = setTimeout(() => {
            const tt    = document.getElementById('timetable-tooltip');
            const d     = card.dataset;
            const pct   = d.ttPct   ? parseInt(d.ttPct) : null;
            const pctColor = d.ttPctColor || 'var(--accent-gold)';

            // Build inner HTML
            let html = `
                <div style="font-size:14px;font-weight:800;color:#fff;margin-bottom:10px;line-height:1.3;">
                    <i class="bi bi-calendar-event" style="color:var(--accent-gold);margin-right:6px;"></i>
                    ${d.ttNombre}
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;font-size:12px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-clock" style="color:var(--accent-gold);width:14px;text-align:center;"></i>
                        <span style="color:rgba(255,255,255,0.75);">${d.ttHorario}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i class="bi bi-door-open" style="color:var(--accent-gold);width:14px;text-align:center;"></i>
                        <span style="color:rgba(255,255,255,0.75);">${d.ttSalon}</span>
                    </div>`;

            if (d.ttCapacidad && d.ttCapacidad !== '—') {
                html += `
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                            <span style="display:flex;align-items:center;gap:6px;"><i class="bi bi-people-fill" style="color:var(--accent-gold);width:14px;text-align:center;"></i><span style="color:rgba(255,255,255,0.75);">Aforo</span></span>
                            <span style="font-weight:700;color:${pctColor};">${d.ttInscritos} / ${d.ttCapacidad}</span>
                        </div>
                        <div style="width:100%;height:5px;background:rgba(255,255,255,0.1);border-radius:99px;overflow:hidden;">
                            <div style="width:${pct ?? 0}%;height:100%;background:${pctColor};border-radius:99px;transition:width 0.4s;"></div>
                        </div>
                    </div>`;
            }

            if (d.ttDesc && d.ttDesc.trim() !== '') {
                html += `
                    <div style="margin-top:4px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.08);">
                        <div style="display:flex;align-items:flex-start;gap:8px;">
                            <i class="bi bi-info-circle" style="color:var(--accent-gold);width:14px;text-align:center;margin-top:1px;flex-shrink:0;"></i>
                            <span style="color:rgba(255,255,255,0.65);line-height:1.5;font-size:11px;">${d.ttDesc}</span>
                        </div>
                    </div>`;
            }

            const extras = [];
            if (d.ttExclusiva) extras.push({ icon: 'bi-lock-fill', label: 'Exclusiva', val: d.ttExclusiva });
            if (d.ttPuntos)    extras.push({ icon: 'bi-star-fill', label: 'Puntos',    val: d.ttPuntos });
            if (extras.length) {
                html += `<div style="margin-top:4px;padding-top:8px;border-top:1px solid rgba(255,255,255,0.08);display:flex;gap:16px;">`;
                extras.forEach(x => {
                    html += `<div style="display:flex;align-items:center;gap:5px;"><i class="bi ${x.icon}" style="color:var(--accent-gold);font-size:11px;"></i><span style="color:rgba(255,255,255,0.55);font-size:11px;">${x.label}:</span><span style="color:#fff;font-weight:700;font-size:11px;">${x.val}</span></div>`;
                });
                html += `</div>`;
            }

            html += `</div>`;
            document.getElementById('timetable-tooltip-body').innerHTML = html;

            tt.style.display = 'block';
            moveTimetableTooltip(e);
            requestAnimationFrame(() => tt.style.opacity = '1');
        }, 180);
    }

    function moveTimetableTooltip(e) {
        const tt = document.getElementById('timetable-tooltip');
        if (!tt || tt.style.display === 'none') return;
        const offsetX = 18, offsetY = 12;
        const w = tt.offsetWidth  || 280;
        const h = tt.offsetHeight || 200;
        let x = e.clientX + offsetX;
        let y = e.clientY + offsetY;
        if (x + w > window.innerWidth  - 12) x = e.clientX - w - offsetX;
        if (y + h > window.innerHeight - 12) y = e.clientY - h - offsetY;
        tt.style.left = x + 'px';
        tt.style.top  = y + 'px';
    }

    function hideTimetableTooltip() {
        clearTimeout(_ttTimer);
        const tt = document.getElementById('timetable-tooltip');
        if (!tt) return;
        tt.style.opacity = '0';
        setTimeout(() => { if (tt.style.opacity === '0') tt.style.display = 'none'; }, 200);
    }

    function openEditModalInEvent(id, username, password) {
        const form = document.getElementById('form-edit-proveedor-event');
        if (form) {
            form.action = `{{ url('proveedores/gestion') }}/${id}`;
            document.getElementById('edit-username-event').value = username;
            document.getElementById('edit-password-event').value = password || '';
            document.getElementById('modal-editar-proveedor-event').style.display = 'flex';
        }
    }
    function closeEditModalInEvent() {
        const modal = document.getElementById('modal-editar-proveedor-event');
        if (modal) modal.style.display = 'none';
    }

    function openAddAgendaModal() {
        editingAgendaId = null;
        document.getElementById('agenda-fecha-input').value = "{{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('Y-m-d') }}";
        document.getElementById('agenda-hora-inicio').value = '09:00';
        document.getElementById('agenda-hora-fin').value = '10:00';
        
        const salonEl = document.getElementById('agenda-salon-select') || document.getElementById('agenda-salon-input');
        if (salonEl) {
            salonEl.value = '';
        }
        
        // Open modal FIRST so elements are visible and rendered
        openModal('modal-agenda');
        // Then update preview on the next paint frame
        requestAnimationFrame(() => {
            requestAnimationFrame(() => validateAgendaOverlap('add'));
        });
    }

    // Modal Helpers
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    // Cerrar al hacer click fuera en los nuevos modales
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal-overlay')) {
            e.target.style.display = 'none';
        }
    });

    // Función de filtrado en tiempo real para la tabla de Tómbola (Escalable a 1,500+ participantes)
    function filterTombolaTable(query) {
        const input = document.getElementById('search-tombola-table');
        if (input && query !== undefined && input.value !== query) {
            input.value = query;
        }
        const q = (query || '').toLowerCase().trim();
        const table = document.getElementById('table-tombola-boletos');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(tr => {
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(q) ? '' : 'none';
        });
    }

    // Sweet Alert para botones de eliminar
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            const message = form.getAttribute('data-message') || '¿Estás seguro de realizar esta acción?';
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: 'var(--bg-secondary)',
                color: 'var(--text-primary)'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Auto-activar pestaña activa basada en parámetro URL, localStorage o Sesión
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('active_tab');
        if (!activeTab || activeTab === '') {
            try {
                activeTab = localStorage.getItem('evento_active_tab_' + '{{ $evento->ID }}');
            } catch(e) {}
        }
        if (!activeTab || activeTab === '') {
            activeTab = "{{ session('active_tab') }}" || "{{ old('active_tab') }}";
        }
        if (activeTab && activeTab !== '') {
            const btn = document.querySelector(`.tab-btn[onclick*="${activeTab}"]`);
            if (btn) {
                switchTab(btn, activeTab);
            }
        }
    });
</script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            icon: 'success',
            background: 'var(--bg-secondary)',
            color: 'var(--text-primary)',
            confirmButtonColor: 'var(--accent-gold)'
        });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: 'Error',
            text: "{{ session('error') }}",
            icon: 'error',
            background: 'var(--bg-secondary)',
            color: 'var(--text-primary)',
            confirmButtonColor: 'var(--accent-gold)'
        });
    });
</script>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11">
<script>
window.switchPremiosSubtab = function(tabName) {
    const catalogSec = document.getElementById('premios-subsection-catalog');
    const tombolaSec = document.getElementById('premios-subsection-tombola');
    const btnCatalog = document.getElementById('subtab-btn-catalog');
    const btnTombola = document.getElementById('subtab-btn-tombola');

    if (!catalogSec || !tombolaSec) return;

    if (tabName === 'catalog') {
        catalogSec.style.display = 'block';
        tombolaSec.style.display = 'none';

        btnCatalog.style.background = 'var(--accent-gold)';
        btnCatalog.style.color = '#0f172a';
        btnCatalog.style.fontWeight = '800';

        btnTombola.style.background = 'transparent';
        btnTombola.style.color = 'var(--text-secondary)';
        btnTombola.style.fontWeight = '700';
    } else {
        catalogSec.style.display = 'none';
        tombolaSec.style.display = 'flex';

        btnTombola.style.background = 'var(--accent-gold)';
        btnTombola.style.color = '#0f172a';
        btnTombola.style.fontWeight = '800';

        btnCatalog.style.background = 'transparent';
        btnCatalog.style.color = 'var(--text-secondary)';
        btnCatalog.style.fontWeight = '700';
    }
};
</script>

</script>
@endpush

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- TIMETABLE TOOLTIP — fixed position, follows cursor, glassmorphism --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div id="timetable-tooltip"
     style="display:none; opacity:0; position:fixed; z-index:9999; pointer-events:none;
            width:290px; max-width:290px;
            background: rgba(16,22,46,0.96);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 14px;
            padding: 16px 18px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 0 0 1px rgba(212,175,55,0.08);
            transition: opacity 0.18s ease;">
    <!-- Gold accent top line -->
    <div style="position:absolute;top:0;left:18px;right:18px;height:2px;background:linear-gradient(90deg,transparent,rgba(212,175,55,0.7),transparent);border-radius:99px;"></div>
    <div id="timetable-tooltip-body"></div>
</div>

@endsection
