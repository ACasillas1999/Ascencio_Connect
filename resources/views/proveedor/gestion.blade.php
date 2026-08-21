@extends('layouts.app')


@push('styles')
<style>
    /* ========================================================= */
    /* VISTA DE TARJETAS MÓVILES PERFECTA Y RESPONSIVA           */
    /* ========================================================= */
    .mobile-proveedores-list {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
        box-sizing: border-box;
        width: 100%;
    }

    @media (max-width: 991px) {
        body, .main, .page-content {
            overflow-y: auto !important;
        }
        div[style*="height: calc(100vh - 130px)"],
        div[style*="height:calc(100vh - 130px)"] {
            height: auto !important;
            overflow: visible !important;
        }
        .card {
            height: auto !important;
            overflow: visible !important;
        }
        .table-wrapper {
            display: none !important;
        }
        .mobile-proveedores-list {
            display: flex !important;
            width: 100% !important;
        }
        .card-header {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
            padding: 14px 16px !important;
        }
        .card-header > div {
            width: 100% !important;
            display: flex !important;
            gap: 8px !important;
        }
        .card-header button {
            flex: 1 !important;
            padding: 8px 10px !important;
            font-size: 12px !important;
            justify-content: center !important;
        }
        div[style*="background:rgba(15,23,42,0.4)"] {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            padding: 14px 16px !important;
        }
        div[style*="background:rgba(15,23,42,0.4)"] > div {
            width: 100% !important;
            min-width: 100% !important;
        }
        #filter-evento-gestion {
            width: 100% !important;
        }
    }

    .mpv-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.75) 0%, rgba(10, 22, 50, 0.9) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 14px;
        width: 100%;
        box-sizing: border-box;
    }

    [data-theme="light"] .mpv-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .mpv-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding-bottom: 12px;
    }

    [data-theme="light"] .mpv-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .mpv-user-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mpv-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: rgba(201, 162, 39, 0.15);
        color: var(--accent-gold);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .mpv-username {
        font-size: 16px;
        font-weight: 800;
        color: var(--accent-gold);
        line-height: 1.2;
    }

    [data-theme="light"] .mpv-username {
        color: #b45309 !important;
    }

    .mpv-pass-pill {
        font-family: monospace;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .mpv-events-sec {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .mpv-sec-title {
        font-size: 10.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mpv-asig-card {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 10px 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    [data-theme="light"] .mpv-asig-card {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }

    .mpv-asig-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
    }

    .mpv-asig-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--accent-gold);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .mpv-asig-ctrls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        padding-top: 6px;
    }

    .mpv-actions-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding-top: 12px;
    }

    [data-theme="light"] .mpv-actions-bar {
        border-top: 1px solid #e2e8f0;
    }
</style>
@endpush










@section('title', 'Gestión de Proveedores')
@section('page-title', 'Proveedores')

@section('content')
<div style="height: calc(100vh - 130px); display: flex; flex-direction: column; overflow: hidden; margin: -10px -10px 0 -10px;">

    <!-- CARD PRINCIPAL: OCUPA TODO EL ALTO Y ANCHO SIN ESPACIOS EN BLANCO -->
    <div class="card" style="flex:1; display:flex; flex-direction:column; overflow:hidden; background:var(--bg-card, #1e293b); border:1px solid var(--border-subtle, rgba(255,255,255,0.08)); border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,0.3); margin:0;">
        
        <!-- HEADER DE LA TARJETA -->
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-bottom:1px solid rgba(255,255,255,0.08); background:rgba(10,15,30,0.3); flex-shrink:0;">
            <span class="card-title" style="font-size:16px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:10px;">
                <i class="bi bi-building-gear" style="font-size:20px;"></i> Cuentas de Proveedores y Asignaciones por Evento
            </span>
            <div style="display:flex; gap:10px; align-items:center;">
                <button type="button" class="btn btn-sm btn-primary" onclick="openCreateModal()" style="font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:8px;">
                    <i class="bi bi-person-plus-fill"></i> Crear Nueva Cuenta
                </button>
                <button type="button" class="btn btn-sm btn-secondary" onclick="openAssignModal()" style="font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; padding:8px 16px; border-radius:8px; background:rgba(212,175,55,0.12); border:1px solid var(--accent-gold); color:var(--accent-gold);">
                    <i class="bi bi-calendar-plus-fill"></i> Asignar a Evento
                </button>
            </div>
        </div>

        <!-- BARRA DE BÚSQUEDA Y FILTROS INTEGRADA -->
        <div style="padding:12px 24px; background:rgba(15,23,42,0.4); border-bottom:1px solid rgba(255,255,255,0.08); display:flex; gap:16px; align-items:center; flex-wrap:wrap; flex-shrink:0;">
            <div style="position:relative; flex:1; min-width:280px;">
                <i class="bi bi-search" style="position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--text-muted); font-size:14px;"></i>
                <input type="text" id="search-gestion-proveedores" class="form-control" placeholder="Buscar por nombre de proveedor, contraseña o evento asignado..." style="padding-left:40px; font-size:13.5px;" oninput="filterMainTable()">
            </div>
            
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:12px; font-weight:700; color:var(--text-secondary); white-space:nowrap;"><i class="bi bi-funnel-fill" style="color:var(--accent-gold);"></i> Filtrar por Evento:</label>
                <select id="filter-evento-gestion" class="form-control" style="width:220px; font-size:13px;" onchange="filterMainTable()">
                    <option value="">Todos los Proveedores</option>
                    <option value="SIN_EVENTO">⚠️ Sin Eventos Asignados</option>
                    <option value="CON_EVENTO">✅ Con Eventos Asignados</option>
                    @foreach($eventos as $ev)
                        <option value="{{ $ev->name_evento }}">{{ $ev->name_evento }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- TABLA DE PROVEEDORES -->
        <div style="padding:0; display:flex; flex-direction:column; width:100%; flex:1; min-height:0; overflow-y:auto;">
        <div class="table-wrapper" style="flex:1; padding:0; overflow-y:auto;">
            <table id="table-main-proveedores" style="width:100%; border-collapse:collapse;">
                <thead style="position:sticky; top:0; z-index:10; background:rgba(15,23,42,0.95); backdrop-filter:blur(4px);">
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.08); text-align:left;">
                        <th style="padding:14px 20px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">Usuario (Proveedor)</th>
                        <th style="padding:14px 20px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">Contraseña</th>
                        <th style="padding:14px 20px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary);">Eventos Asignados</th>
                        <th style="padding:14px 20px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $prov)
                    <tr class="proveedor-row" data-has-events="{{ $prov->asignaciones->count() > 0 ? 'CON_EVENTO' : 'SIN_EVENTO' }}" style="border-bottom:1px solid rgba(255,255,255,0.05); transition:background 0.15s;" onmouseenter="this.style.background='rgba(255,255,255,0.02)'" onmouseleave="this.style.background='transparent'">
                        <td style="padding:16px 20px; font-weight:700; font-size:14px; color:var(--text-primary);">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; border-radius:8px; background:rgba(212,175,55,0.12); border:1px solid rgba(212,175,55,0.25); display:flex; align-items:center; justify-content:center; color:var(--accent-gold);">
                                    <i class="bi bi-person-badge-fill" style="font-size:16px;"></i>
                                </div>
                                {{ $prov->username }}
                            </div>
                        </td>
                        <td style="padding:16px 20px; font-family:monospace; color:var(--accent-gold); font-weight:600; font-size:14px;">
                            {{ $prov->password_visible ?: '********' }}
                        </td>
                        <td style="padding:16px 20px;">
                            @if($prov->asignaciones->count() > 0)
                                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                    @foreach($prov->asignaciones as $asig)
                                        <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(15,23,42,0.5); border:1px solid rgba(255,255,255,0.1); padding:4px 10px; border-radius:8px; margin-bottom:4px;">
                                            <!-- Nombre de Evento -->
                                            <span style="color:var(--accent-gold); font-size:12.5px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                                <i class="bi bi-calendar-event"></i> {{ $asig->name_evento }}
                                            </span>

                                            <!-- Puntos Editable -->
                                            <form action="{{ route('proveedores.update', $asig->id_asignacion) }}" method="POST" style="display:inline-flex; align-items:center; gap:4px; margin:0;">
                                                @csrf @method('PUT')
                                                <input type="number" name="Puntos" value="{{ $asig->Puntos }}" min="0" required class="form-control" style="width:65px; text-align:center; font-size:12px; font-weight:800; color:#0f172a; background:var(--accent-gold); border:none; border-radius:4px; padding:2px 4px;">
                                                <button type="submit" class="btn btn-sm btn-secondary" style="padding:2px 6px; font-size:11px; font-weight:700; background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15);" title="Guardar Puntos">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                            </form>

                                            <span style="color:rgba(255,255,255,0.2);">|</span>

                                            <!-- Botón Separado para Habilitar / Deshabilitar -->
                                            <form action="{{ route('proveedores.update', $asig->id_asignacion) }}" method="POST" style="display:inline; margin:0;">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="Activo" value="{{ $asig->Activo ? 0 : 1 }}">
                                                <button type="submit" style="border:1px solid {{ $asig->Activo ? 'rgba(34,197,94,0.4)' : 'rgba(239,68,68,0.4)' }}; cursor:pointer; font-size:11px; padding:3px 8px; font-weight:700; border-radius:6px; background:{{ $asig->Activo ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }}; color:{{ $asig->Activo ? '#4ade80' : '#f87171' }}; display:inline-flex; align-items:center; gap:4px;" title="Clic para {{ $asig->Activo ? 'deshabilitar' : 'habilitar' }} la cuenta en {{ $asig->name_evento }}">
                                                    <i class="bi bi-power"></i> {{ $asig->Activo ? '🟢 Habilitado' : '🔴 Deshabilitado' }}
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span style="color:var(--text-muted); font-size:12px; font-style:italic;">Sin eventos asignados</span>
                            @endif
                        </td>
                        <td style="padding:16px 20px; text-align:right;">
                            <div style="display:inline-flex; gap:8px;">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="openEditModal({{ $prov->ID }}, '{{ addslashes($prov->username) }}', '{{ addslashes($prov->password_visible) }}')" style="font-size:12px; padding:6px 12px; border-radius:6px; font-weight:600; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12);" title="Modificar usuario o contraseña">
                                    <i class="bi bi-pencil-square" style="color:var(--accent-gold);"></i> Modificar
                                </button>

                                <button type="button" class="btn btn-sm btn-secondary" onclick="openAssignModal('{{ $prov->username }}')" style="font-size:12px; padding:6px 12px; border-radius:6px; font-weight:600; background:rgba(212,175,55,0.08); border:1px solid rgba(212,175,55,0.2); color:var(--accent-gold);" title="Asignar a un evento">
                                    <i class="bi bi-plus-circle"></i> Asignar Evento
                                </button>

                                <form action="{{ route('proveedores.destroyUsuario', $prov->ID) }}" method="POST" class="delete-form" data-message="¿Eliminar la cuenta de proveedor '{{ $prov->username }}'?" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary" style="color:#ef4444; padding:6px 10px; border-radius:6px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);" title="Eliminar cuenta">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:60px; color:var(--text-muted);">
                            <i class="bi bi-building-exclamation" style="font-size:42px; display:block; margin-bottom:12px; color:var(--accent-gold);"></i>
                            No hay proveedores registrados actualmente.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div> <!-- CIERRE DE TABLE-WRAPPER -->

        <!-- VISTA DE TARJETAS MÓVILES AVANZADAS DE PROVEEDORES -->
        <div class="mobile-proveedores-list">
            @forelse($proveedores as $prov)
            <div class="mpv-card proveedor-row" data-has-events="{{ $prov->asignaciones->count() > 0 ? 'CON_EVENTO' : 'SIN_EVENTO' }}">
                <!-- HEADER: ICONO, USUARIO Y CONTRASEÑA -->
                <div class="mpv-header">
                    <div class="mpv-user-wrap">
                        <div class="mpv-icon"><i class="bi bi-building-gear"></i></div>
                        <div>
                            <div class="mpv-username">{{ $prov->username }}</div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">Cuenta de Proveedor</div>
                        </div>
                    </div>
                    <span class="mpv-pass-pill" title="Contraseña">
                        <i class="bi bi-key-fill" style="color:var(--accent-gold);"></i> {{ $prov->password_visible ?: '********' }}
                    </span>
                </div>

                <!-- EVENTOS ASIGNADOS -->
                <div class="mpv-events-sec">
                    <div class="mpv-sec-title"><i class="bi bi-calendar-check"></i> Eventos Asignados ({{ $prov->asignaciones->count() }})</div>
                    @forelse($prov->asignaciones as $asig)
                    <div class="mpv-asig-card">
                        <div class="mpv-asig-top">
                            <span class="mpv-asig-name">
                                <i class="bi bi-calendar-event"></i> {{ Str::limit($asig->name_evento, 26) }}
                            </span>
                            <form action="{{ route('proveedores.update', $asig->id_asignacion) }}" method="POST" style="display:inline; margin:0;">
                                @csrf @method('PUT')
                                <input type="hidden" name="Activo" value="{{ $asig->Activo ? 0 : 1 }}">
                                <button type="submit" style="border:1px solid {{ $asig->Activo ? 'rgba(34,197,94,0.4)' : 'rgba(239,68,68,0.4)' }}; cursor:pointer; font-size:10.5px; padding:2px 8px; font-weight:700; border-radius:6px; background:{{ $asig->Activo ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' }}; color:{{ $asig->Activo ? '#4ade80' : '#f87171' }}; display:inline-flex; align-items:center; gap:4px;" title="Clic para {{ $asig->Activo ? 'deshabilitar' : 'habilitar' }}">
                                    <i class="bi bi-power"></i> {{ $asig->Activo ? 'Habilitado' : 'Deshabilitado' }}
                                </button>
                            </form>
                        </div>

                        <div class="mpv-asig-ctrls">
                            <form action="{{ route('proveedores.update', $asig->id_asignacion) }}" method="POST" style="display:inline-flex; align-items:center; gap:6px; margin:0;">
                                @csrf @method('PUT')
                                <span style="font-size:11px; color:var(--text-muted); font-weight:600;">Puntos:</span>
                                <input type="number" name="Puntos" value="{{ $asig->Puntos }}" min="0" required class="form-control" style="width:60px; text-align:center; font-size:12px; font-weight:800; color:#0f172a; background:var(--accent-gold); border:none; border-radius:4px; padding:2px 4px;">
                                <button type="submit" class="btn btn-sm btn-secondary" style="padding:2px 6px; font-size:11px;" title="Guardar Puntos">
                                    <i class="bi bi-check"></i>
                                </button>
                            </form>

                            <form action="{{ route('proveedores.destroy', $asig->id_asignacion) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Desasignar este proveedor del evento?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" style="padding:3px 8px; font-size:11px; display:inline-flex; align-items:center; gap:4px;" title="Desasignar">
                                    <i class="bi bi-x-lg"></i> Quitar
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div style="font-size:12px; color:var(--text-muted); font-style:italic; padding:4px 0;">
                        Sin eventos asignados
                    </div>
                    @endforelse
                </div>

                <!-- ACCIONES INFERIORES -->
                <div class="mpv-actions-bar">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="openEditModal({{ $prov->ID }}, '{{ addslashes($prov->username) }}', '{{ addslashes($prov->password_visible) }}')" style="flex:1; font-size:12px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                        <i class="bi bi-pencil-square" style="color:var(--accent-gold);"></i> Modificar
                    </button>

                    <button type="button" class="btn btn-sm btn-secondary" onclick="openAssignModal('{{ $prov->username }}')" style="flex:1; font-size:12px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; gap:6px; background:rgba(212,175,55,0.12); border:1px solid var(--accent-gold); color:var(--accent-gold);" title="Asignar a evento">
                        <i class="bi bi-plus-circle"></i> Asignar Evento
                    </button>

                    <form action="{{ route('proveedores.destroyUsuario', $prov->ID) }}" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Eliminar la cuenta de proveedor '{{ $prov->username }}'?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" style="padding:6px 10px; font-size:12px;" title="Eliminar cuenta">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center; padding:40px; color:var(--text-muted)">
                <i class="bi bi-building-x" style="font-size:32px; display:block; margin-bottom:8px"></i>
                No hay proveedores registrados
            </div>
            @endforelse
        </div>

       

        <!-- MODAL 1: CREAR NUEVA CUENTA DE PROVEEDOR -->
<div id="modal-crear-proveedor" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="width:100%; max-width: 480px; background: var(--bg-card, #1e293b); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.7); position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:16px; margin-bottom:20px;">
            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-person-plus-fill"></i> Crear Cuenta de Proveedor
            </h3>
            <button type="button" onclick="closeCreateModal()" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        
        <form action="{{ route('proveedores.storeUsuario') }}" method="POST">
            @csrf
            <div style="display:grid; gap:16px;">
                <div>
                    <label for="create-username" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Nombre del Proveedor / Usuario *</label>
                    <input type="text" id="create-username" name="username" class="form-control" placeholder="Ej. CocaCola, Truper, Schneider, etc." required style="width:100%; font-size:14px;">
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Este nombre se usará para iniciar sesión y escanear QR.</small>
                </div>

                <div>
                    <label for="create-password" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Contraseña *</label>
                    <input type="text" id="create-password" name="password" class="form-control" placeholder="Ej. 123456" required style="width:100%; font-size:14px;">
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Contraseña de acceso para la cuenta del proveedor.</small>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                <button type="button" class="btn btn-secondary" onclick="closeCreateModal()" style="font-weight:600;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Crear Cuenta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: MODIFICAR / EDITAR PROVEEDOR -->
<div id="modal-editar-proveedor" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="width:100%; max-width: 480px; background: var(--bg-card, #1e293b); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.7); position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:16px; margin-bottom:20px;">
            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-pencil-square"></i> Modificar Cuenta de Proveedor
            </h3>
            <button type="button" onclick="closeEditModal()" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        
        <form id="form-edit-proveedor" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="display:grid; gap:16px;">
                <div>
                    <label for="edit-username" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Nombre del Proveedor / Usuario *</label>
                    <input type="text" id="edit-username" name="username" class="form-control" required style="width:100%; font-size:14px;">
                </div>

                <div>
                    <label for="edit-password" style="display:block; margin-bottom:6px; font-size:12px; font-weight:700; color:var(--text-secondary);">Nueva Contraseña (Opcional)</label>
                    <input type="text" id="edit-password" name="password" class="form-control" placeholder="Dejar en blanco para conservar la actual" style="width:100%; font-size:14px;">
                    <small style="color:var(--text-muted); font-size:11px; display:block; margin-top:4px;">Escribe una nueva contraseña solo si deseas cambiarla.</small>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="font-weight:600;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: ASIGNAR PROVEEDOR A EVENTO -->
<div id="modal-asignar-evento" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px); z-index:9999; justify-content:center; align-items:center; padding:20px;">
    <div style="width:100%; max-width: 500px; background: var(--bg-card, #1e293b); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; padding: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.7); position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:16px; margin-bottom:20px;">
            <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--accent-gold); display:flex; align-items:center; gap:8px;">
                <i class="bi bi-calendar-check-fill"></i> Asignar Proveedor a Evento
            </h3>
            <button type="button" onclick="closeAssignModal()" style="background:none; border:none; color:var(--text-muted); font-size:24px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        
        <form id="form-assign-evento" method="POST" action="">
            @csrf
            <div style="display:grid; gap:16px;">
                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">Seleccionar Evento *</label>
                    <select id="select-evento-assign" class="form-control" required style="width:100%; font-size:14px;" onchange="updateAssignFormAction(this.value)">
                        <option value="">-- Selecciona un evento --</option>
                        @foreach($eventos as $ev)
                            <option value="{{ $ev->ID }}">{{ $ev->name_evento }} ({{ $ev->estado }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">Proveedor *</label>
                    <select name="NombreProveedor" id="select-proveedor-assign" class="form-control" required style="width:100%; font-size:14px;">
                        <option value="">-- Selecciona un proveedor --</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->username }}">{{ $prov->username }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; display:block;">Puntos por Escaneo *</label>
                    <div style="position:relative;">
                        <input name="Puntos" type="number" class="form-control" required value="10" min="0" style="padding-left:36px; font-size:15px; font-weight:700; color:var(--accent-gold);">
                        <i class="bi bi-star-fill" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--accent-gold); font-size:14px;"></i>
                    </div>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                <button type="button" class="btn btn-secondary" onclick="closeAssignModal()" style="font-weight:600;">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                    <i class="bi bi-check-circle-fill"></i> Asignar a Evento
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('modal-crear-proveedor').style.display = 'flex';
    }
    function closeCreateModal() {
        document.getElementById('modal-crear-proveedor').style.display = 'none';
    }

    function openEditModal(id, username, password) {
        const form = document.getElementById('form-edit-proveedor');
        form.action = `{{ url('proveedores/gestion') }}/${id}`;
        document.getElementById('edit-username').value = username;
        document.getElementById('edit-password').value = password || '';
        document.getElementById('modal-editar-proveedor').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('modal-editar-proveedor').style.display = 'none';
    }

    function openAssignModal(username = '') {
        if (username) {
            document.getElementById('select-proveedor-assign').value = username;
        }
        document.getElementById('modal-asignar-evento').style.display = 'flex';
    }
    function closeAssignModal() {
        document.getElementById('modal-asignar-evento').style.display = 'none';
    }
    function updateAssignFormAction(eventoId) {
        if (eventoId) {
            document.getElementById('form-assign-evento').action = `{{ url('eventos') }}/${eventoId}/proveedores`;
        }
    }

    // --- Filtro Dinámico Principal de Proveedores ---
    function filterMainTable() {
        const query = document.getElementById('search-gestion-proveedores').value.toLowerCase().trim();
        const eventFilter = document.getElementById('filter-evento-gestion').value.trim();
        const rows = document.querySelectorAll('.proveedor-row');

        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const hasEventsState = row.getAttribute('data-has-events');

            const matchesQuery = !query || text.includes(query);
            let matchesFilter = true;

            if (eventFilter === 'SIN_EVENTO' || eventFilter === 'CON_EVENTO') {
                matchesFilter = (hasEventsState === eventFilter);
            } else if (eventFilter !== '') {
                matchesFilter = text.toLowerCase().includes(eventFilter.toLowerCase());
            }

            if (matchesQuery && matchesFilter) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let emptyRow = document.querySelector('#table-main-proveedores .no-filter-matches-row');
        if (visibleCount === 0) {
            if (!emptyRow) {
                const tbody = document.querySelector('#table-main-proveedores tbody');
                emptyRow = document.createElement('tr');
                emptyRow.className = 'no-filter-matches-row';
                emptyRow.innerHTML = `<td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted);"><i class="bi bi-search" style="font-size:32px; display:block; margin-bottom:8px; color:var(--accent-gold);"></i>No se encontraron proveedores que coincidan con la búsqueda o filtro seleccionados.</td>`;
                tbody.appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
            }
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        let m1 = document.getElementById('modal-crear-proveedor');
        let m2 = document.getElementById('modal-editar-proveedor');
        let m3 = document.getElementById('modal-asignar-evento');
        if (event.target == m1) closeCreateModal();
        if (event.target == m2) closeEditModal();
        if (event.target == m3) closeAssignModal();
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeAssignModal();
        }
    });

    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formToSubmit = this;
            const msg = this.dataset.message || '¿Seguro que deseas eliminar esta cuenta de proveedor?';
            
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
</script>
@endsection
