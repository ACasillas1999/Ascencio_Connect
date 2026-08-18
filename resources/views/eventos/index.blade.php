@extends('layouts.app')

@section('title', 'Eventos')
@section('page-title', 'Eventos')

@section('topbar-actions')
    <button class="btn btn-primary" onclick="openCreateModal()" id="btn-nuevo-evento" title="Nuevo Evento">
        <i class="bi bi-plus-lg"></i> <span style="display:inline-block;">Nuevo</span>
    </button>
@endsection

@push('styles')
<style>
    /* Modal Overlay with Frosted Glass Effect */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(8, 14, 28, 0.75); /* Darker backdrop */
        z-index: 10000; /* Super high index to be on top of everything */
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    
    /* Modal Box (Premium Glassmorphism Design) */
    .modal-content {
        background: linear-gradient(145deg, rgba(15, 32, 68, 0.95), rgba(10, 20, 45, 0.98));
        padding: 30px;
        border-radius: 16px;
        width: 90%;
        max-width: 680px;
        max-height: 90vh;
        overflow-y: auto;
        border: 1px solid rgba(201, 162, 39, 0.2); /* Accent gold border */
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.8), 
                    inset 0 1px 1px rgba(255, 255, 255, 0.1),
                    0 0 30px rgba(201, 162, 39, 0.05); /* Soft outer gold glow */
        position: relative;
        animation: premiumScaleIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    
    .modal-content::-webkit-scrollbar {
        width: 6px;
    }
    .modal-content::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.02);
        border-radius: 99px;
    }
    .modal-content::-webkit-scrollbar-thumb {
        background: rgba(201, 162, 39, 0.3);
        border-radius: 99px;
    }
    
    @keyframes premiumScaleIn {
        from {
            transform: scale(0.92) translateY(20px);
            opacity: 0;
        }
        to {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }
    
    /* Header layout */
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    
    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--accent-gold);
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .modal-title i {
        font-size: 20px;
        filter: drop-shadow(0 2px 4px rgba(201, 162, 39, 0.3));
    }
    
    /* Elegant close button */
    .modal-close {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05);
        color: var(--text-secondary);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .modal-close:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.3);
        color: #fca5a5;
        transform: rotate(90deg);
    }

    /* Form spacing and typography adjustments */
    .form-group {
        margin-bottom: 22px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 14px;
        color: #fff;
        transition: all 0.25s ease;
    }

    .form-control:focus {
        border-color: var(--accent-gold);
        background: rgba(0, 0, 0, 0.4);
        box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.15), 
                    inset 0 1px 1px rgba(0, 0, 0, 0.2);
    }
    
    /* Buttons inside modal */
    .modal-actions {
        display: flex;
        gap: 12px;
        margin-top: 28px;
    }

    .btn-submit {
        flex: 2;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(201, 162, 39, 0.2);
    }

    .btn-cancel {
        flex: 1;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: var(--text-secondary);
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
    }

    
    
    
    /* ========================================================= */
    /* DISEÑO PREMIUM ULTRA-MODERNO DE TARJETAS DE EVENTOS MÓVIL */
    /* ========================================================= */
    .mobile-events-list {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            display: none !important; /* Oculta la tabla clásica en celular */
        }
        .mobile-events-list {
            display: flex !important; /* Muestra las tarjetas móviles avanzadas en celular */
        }
    }

    .mobile-event-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.7) 0%, rgba(10, 22, 50, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    [data-theme="light"] .mobile-event-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .mec-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding-bottom: 12px;
    }

    [data-theme="light"] .mec-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .mec-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mec-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: rgba(201, 162, 39, 0.15);
        color: var(--accent-gold);
        display: flex; align-items: center; justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .mec-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--accent-gold);
        text-decoration: none;
        line-height: 1.2;
    }

    [data-theme="light"] .mec-title {
        color: #b45309 !important;
    }

    .mec-sub {
        font-size: 11px;
        color: var(--text-muted);
        margin-top: 4px;
        font-weight: 500;
    }

    .mec-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 14px;
    }

    .mec-detail-item {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .mec-detail-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .mec-detail-val {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .mec-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding-top: 12px;
    }

    [data-theme="light"] .mec-actions {
        border-top: 1px solid #e2e8f0;
    }

    .mec-btn-primary {
        flex: 1;
        justify-content: center;
        font-weight: 700;
        padding: 8px 14px;
    }


        /* ========================================================= */
        /* MODALES ULTRA-RESPONSIVOS MÓVILES PARA TODA LA APLICACIÓN  */
        /* ========================================================= */
        .modal-overlay {
            padding: 16px !important;
            box-sizing: border-box !important;
        }

        @media (max-width: 768px) {
            .modal-content,
            .modal-card {
                width: 95% !important;
                max-width: 480px !important;
                max-height: 88vh !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                padding: 22px 18px !important;
                border-radius: 20px !important;
                margin: auto !important;
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.7) !important;
            }

            .modal-header {
                margin-bottom: 18px !important;
                padding-bottom: 12px !important;
            }

            .modal-title {
                font-size: 17px !important;
            }

            /* Apilar campos de cuadrícula de modales en 1 sola columna */
            .modal-content form div[style*="grid-template-columns"],
            .modal-card form div[style*="grid-template-columns"],
            .modal-content div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            .modal-content .form-group,
            .modal-card .form-group {
                grid-column: 1 / -1 !important;
                margin-bottom: 14px !important;
            }

            .modal-content .form-control,
            .modal-card .form-control {
                font-size: 16px !important; /* Previene auto-zoom en iOS */
                padding: 12px 14px !important;
                min-height: 46px !important;
            }

            .modal-actions,
            .modal-footer {
                display: flex !important;
                flex-direction: column !important;
                gap: 10px !important;
                margin-top: 22px !important;
            }

            .modal-actions button,
            .modal-actions a,
            .modal-actions input[type="submit"],
            .modal-footer button,
            .btn-submit,
            .btn-cancel {
                width: 100% !important;
                flex: none !important;
                min-height: 48px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 15px !important;
                font-weight: 700 !important;
                border-radius: 12px !important;
            }
        }

</style>
@endpush

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">
            <i class="bi bi-calendar-event" style="color:var(--accent-gold);margin-right:8px"></i>
            Todos los Eventos
            <span style="font-size:12px;color:var(--text-muted);font-weight:400;margin-left:8px">({{ $eventos->total() }} registros)</span>
        </span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre del Evento</th>
                    <th>Fechas</th>
                    <th>Ubicación</th>
                    <th>Capacidad</th>
                    <th>Participantes</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eventos as $evento)
                <tr onclick="if(event.target.closest('a') || event.target.closest('button')) return; window.location='{{ route('eventos.show', $evento) }}'" style="cursor: pointer;" class="hover-row">
                    <td style="color:var(--text-muted);font-size:12px">{{ $evento->ID }}</td>
                    <td>
                        <a href="{{ route('eventos.show', $evento) }}" style="color:var(--text-primary);text-decoration:none;font-weight:600">
                            {{ $evento->name_evento }}
                        </a>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
                            <i class="bi bi-clock" style="margin-right:3px"></i>{{ $evento->duracion }}
                            &middot; Puntos: {{ $evento->tipo_puntos }}
                        </div>
                    </td>
                    <td style="color:var(--text-secondary);font-size:12px;white-space:nowrap">
                        {{ $evento->fecha_inicio->format('d/m/Y') }}
                        @if($evento->fecha_inicio != $evento->fecha_fin)
                            <br>→ {{ $evento->fecha_fin->format('d/m/Y') }}
                        @endif
                    </td>
                    <td style="color:var(--text-secondary);font-size:12.5px">{{ $evento->ubicacion }}</td>
                    <td style="text-align:center;font-weight:600">{{ number_format($evento->capacidad) }}</td>
                    <td style="text-align:center">
                        <span class="badge badge-primary">{{ number_format($evento->participantes_count) }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $evento->badge_color }}">{{ $evento->estado }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px">
                            <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sm btn-secondary" title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-secondary" onclick="editEvento({{ json_encode($evento) }})" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('eventos.destroy', $evento) }}" onsubmit="return confirm('¿Eliminar este evento?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        No hay eventos registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- VISTA DE TARJETAS MÓVILES AVANZADAS -->
    <div class="mobile-events-list">
        @forelse($eventos as $evento)
        <div class="mobile-event-card">
            <div class="mec-header">
                <div class="mec-title-wrap">
                    <div class="mec-icon"><i class="bi bi-calendar-event-fill"></i></div>
                    <div>
                        <a href="{{ route('eventos.show', $evento) }}" class="mec-title">{{ $evento->name_evento }}</a>
                        <div class="mec-sub">
                            <i class="bi bi-clock"></i> {{ $evento->duracion }} &middot; Puntos: {{ $evento->tipo_puntos }}
                        </div>
                    </div>
                </div>
                <span class="badge {{ $evento->badge_color }}">{{ $evento->estado }}</span>
            </div>

            <div class="mec-details-grid">
                <div class="mec-detail-item">
                    <span class="mec-detail-label"><i class="bi bi-calendar3"></i> Fechas</span>
                    <span class="mec-detail-val">
                        {{ $evento->fecha_inicio->format('d/m/Y') }}
                        @if($evento->fecha_inicio != $evento->fecha_fin)
                            &rarr; {{ $evento->fecha_fin->format('d/m/Y') }}
                        @endif
                    </span>
                </div>

                <div class="mec-detail-item">
                    <span class="mec-detail-label"><i class="bi bi-geo-alt-fill"></i> Ubicación</span>
                    <span class="mec-detail-val">{{ $evento->ubicacion ?? 'N/A' }}</span>
                </div>

                <div class="mec-detail-item">
                    <span class="mec-detail-label"><i class="bi bi-people-fill"></i> Participantes</span>
                    <span class="mec-detail-val"><span class="badge badge-primary">{{ number_format($evento->participantes_count) }} reg.</span></span>
                </div>

                <div class="mec-detail-item">
                    <span class="mec-detail-label"><i class="bi bi-pie-chart-fill"></i> Capacidad</span>
                    <span class="mec-detail-val">{{ number_format($evento->capacidad) }} pers.</span>
                </div>
            </div>

            <div class="mec-actions">
                <a href="{{ route('eventos.show', $evento) }}" class="btn btn-sm btn-primary mec-btn-primary">
                    <i class="bi bi-eye-fill"></i> Ver Evento
                </a>
                <button type="button" class="btn btn-sm btn-secondary" onclick="editEvento({{ json_encode($evento) }})" title="Editar">
                    <i class="bi bi-pencil"></i>
                </button>
                <form method="POST" action="{{ route('eventos.destroy', $evento) }}" onsubmit="return confirm('¿Eliminar este evento?')" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:40px;color:var(--text-muted)">
            <i class="bi bi-calendar-x" style="font-size:32px;display:block;margin-bottom:8px"></i>
            No hay eventos registrados
        </div>
        @endforelse
    </div>


    @if($eventos->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--border-subtle)">
        {{ $eventos->links() }}
    </div>
    @endif
</div>

<!-- MODAL FORMULARIO (NUEVO / EDITAR) -->
<div id="eventoModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-title" id="formTitle"><i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nuevo Evento</span>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="card-body" style="padding: 0;">
            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 20px; font-size: 13.5px; border-radius: 8px; background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; padding: 12px 16px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="eventoForm" action="{{ route('eventos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label for="name_evento" class="form-label">Nombre del Evento *</label>
                        <input type="text" id="name_evento" name="name_evento" class="form-control" placeholder="Ej: CONEXIÓN ASCENCIO 2026" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio *</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required onchange="calcDuracion()">
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin" class="form-label">Fecha Fin *</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required onchange="calcDuracion()">
                    </div>

                    <div class="form-group">
                        <label for="duracion" class="form-label">Duración</label>
                        <input type="text" id="duracion" name="duracion" class="form-control" placeholder="Ej: 2 días" value="1 día">
                    </div>

                    <div class="form-group">
                        <label for="capacidad" class="form-label">Capacidad *</label>
                        <input type="number" id="capacidad" name="capacidad" class="form-control" value="100" min="1" required>
                    </div>

                    <div class="form-group" style="grid-column:1/-1;">
                        <label for="ubicacion" class="form-label">Ubicación / Venue *</label>
                        <select id="ubicacion" name="ubicacion" class="form-control" required>
                            <option value="">Seleccione una ubicación</option>
                            @foreach($ubicaciones as $ub)
                                <option value="{{ $ub->Nombre }}">{{ $ub->Nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado" class="form-label">Estado *</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <option value="PRÓXIMO">PRÓXIMO</option>
                            <option value="EN CURSO">EN CURSO</option>
                            <option value="FINALIZADO">FINALIZADO</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tipo_puntos" class="form-label">Sistema de Puntos</label>
                        <select id="tipo_puntos" name="tipo_puntos" class="form-control">
                            <option value="ninguno">Sin puntos</option>
                            <option value="individual">Individual</option>
                            <option value="grupal">Grupal</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top:16px; border-top:1px solid rgba(255,255,255,0.08); padding-top:16px;">
                    <h5 style="color:var(--accent-gold); margin-bottom:12px; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;"><i class="bi bi-gear"></i> Configuración Adicional</h5>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                        
                        <div class="form-group">
                            <label for="machote_gafete" class="form-label">Machote Gafete (JPG/PNG)</label>
                            <input id="machote_gafete" name="machote_gafete" type="file" class="form-control" accept="image/*">
                            <div id="machote_gafete_current" style="display:none; font-size:11px; margin-top:4px; color:var(--text-secondary);">
                                Actual: <a id="machote_gafete_link" href="#" target="_blank" style="color:var(--accent-gold); text-decoration:none;">Ver imagen</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="machote_horario" class="form-label">Machote Horario (JPG/PNG)</label>
                            <input id="machote_horario" name="machote_horario" type="file" class="form-control" accept="image/*">
                            <div id="machote_horario_current" style="display:none; font-size:11px; margin-top:4px; color:var(--text-secondary);">
                                Actual: <a id="machote_horario_link" href="#" target="_blank" style="color:var(--accent-gold); text-decoration:none;">Ver imagen</a>
                            </div>
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <input type="checkbox" id="enviar_whatsapp_auto" name="enviar_whatsapp_auto" value="1" style="width:18px;height:18px;accent-color:var(--accent-gold); cursor:pointer;">
                            <label for="enviar_whatsapp_auto" style="margin:0;cursor:pointer; font-size:12px; color:var(--text-secondary);">Enviar WhatsApp al registrar</label>
                        </div>

                        <div class="form-group" style="display:flex; align-items:center; gap:8px; margin-top:8px;">
                            <input type="checkbox" id="clases_obligatorias" name="clases_obligatorias" value="1" style="width:18px;height:18px;accent-color:var(--accent-gold); cursor:pointer;">
                            <label for="clases_obligatorias" style="margin:0;cursor:pointer; font-size:12px; color:var(--text-secondary);">Clases Obligatorias</label>
                        </div>

                        <div class="form-group" style="grid-column:1/-1; margin-bottom:0;">
                            <label for="wa_template_name" class="form-label">Plantilla de WhatsApp (opcional)</label>
                            <input id="wa_template_name" name="wa_template_name" type="text" class="form-control" placeholder="Ej: ascencio_day_len_2026">
                        </div>

                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary btn-submit">Guardar</button>
                    <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calcDuracion() {
        let startVal = document.getElementById('fecha_inicio').value;
        let endVal = document.getElementById('fecha_fin').value;
        
        if (startVal && endVal) {
            let start = new Date(startVal);
            let end = new Date(endVal);
            
            let diffTime = end - start;
            if (diffTime >= 0) {
                let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                document.getElementById('duracion').value = diffDays + (diffDays === 1 ? ' día' : ' días');
            } else {
                document.getElementById('duracion').value = '';
            }
        }
    }

    function openCreateModal() {
        resetForm();
        document.getElementById('eventoModal').style.display = 'flex';
    }

    function editEvento(evento) {
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-pencil" style="color:var(--accent-gold);margin-right:8px"></i>Editar Evento';
        document.getElementById('eventoForm').action = "{{ url('eventos') }}/" + evento.ID;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('name_evento').value = evento.name_evento;
        
        if (evento.fecha_inicio) {
            document.getElementById('fecha_inicio').value = evento.fecha_inicio.substring(0, 10);
        } else {
            document.getElementById('fecha_inicio').value = '';
        }
        
        if (evento.fecha_fin) {
            document.getElementById('fecha_fin').value = evento.fecha_fin.substring(0, 10);
        } else {
            document.getElementById('fecha_fin').value = '';
        }
        
        document.getElementById('duracion').value = evento.duracion || '';
        document.getElementById('capacidad').value = evento.capacidad || 100;
        document.getElementById('ubicacion').value = evento.ubicacion || '';
        document.getElementById('estado').value = evento.estado || 'PRÓXIMO';
        document.getElementById('tipo_puntos').value = evento.tipo_puntos || 'ninguno';
        
        document.getElementById('enviar_whatsapp_auto').checked = !!evento.enviar_whatsapp_auto;
        document.getElementById('clases_obligatorias').checked = !!evento.clases_obligatorias;
        
        document.getElementById('wa_template_name').value = evento.wa_template_name || '';

        if (evento.machote_gafete) {
            document.getElementById('machote_gafete_current').style.display = 'block';
            document.getElementById('machote_gafete_link').href = "/storage/" + evento.machote_gafete;
        } else {
            document.getElementById('machote_gafete_current').style.display = 'none';
        }
        
        if (evento.machote_horario) {
            document.getElementById('machote_horario_current').style.display = 'block';
            document.getElementById('machote_horario_link').href = "/storage/" + evento.machote_horario;
        } else {
            document.getElementById('machote_horario_current').style.display = 'none';
        }
        
        document.getElementById('eventoModal').style.display = 'flex';
    }

    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nuevo Evento';
        document.getElementById('eventoForm').action = "{{ route('eventos.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('name_evento').value = '';
        document.getElementById('fecha_inicio').value = '';
        document.getElementById('fecha_fin').value = '';
        document.getElementById('duracion').value = '1 día';
        document.getElementById('capacidad').value = 100;
        document.getElementById('ubicacion').value = '';
        document.getElementById('estado').value = 'PRÓXIMO';
        document.getElementById('tipo_puntos').value = 'ninguno';
        
        document.getElementById('enviar_whatsapp_auto').checked = false;
        document.getElementById('clases_obligatorias').checked = false;
        
        document.getElementById('wa_template_name').value = 'ascencio_day_len_2026';
        
        document.getElementById('machote_gafete').value = '';
        document.getElementById('machote_horario').value = '';
        document.getElementById('machote_gafete_current').style.display = 'none';
        document.getElementById('machote_horario_current').style.display = 'none';
    }

    function closeModal() {
        document.getElementById('eventoModal').style.display = 'none';
        resetForm();
    }

    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        let modal = document.getElementById('eventoModal');
        if (event.target == modal) {
            closeModal();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Move modal to body to avoid parent transformation issues
        const modal = document.getElementById('eventoModal');
        if (modal) {
            document.body.appendChild(modal);
        }
    });
</script>

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('eventoModal').style.display = 'flex';
    });
</script>
@endif
@endsection
