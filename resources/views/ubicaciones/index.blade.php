@extends('layouts.app')

@section('title', 'Gestionar Ubicaciones')
@section('page-title', 'Ubicaciones')

@section('topbar-actions')
    <button class="btn btn-primary" onclick="openCreateModal()">
        <i class="bi bi-plus-lg"></i> Nueva Ubicación
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
        max-width: 520px;
        border: 1px solid rgba(201, 162, 39, 0.2); /* Accent gold border */
        box-shadow: 0 24px 60px rgba(0, 0, 0, 0.8), 
                    inset 0 1px 1px rgba(255, 255, 255, 0.1),
                    0 0 30px rgba(201, 162, 39, 0.05); /* Soft outer gold glow */
        position: relative;
        animation: premiumScaleIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
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
    
    /* Styling read-only total capacity field */
    .form-control-readonly {
        background: rgba(201, 162, 39, 0.04) !important;
        border: 1px dashed rgba(201, 162, 39, 0.3) !important;
        color: var(--accent-gold) !important;
        font-weight: 700;
        font-size: 16px;
        letter-spacing: 0.5px;
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
    /* TARJETAS MÓVILES PREMIUM DE UBICACIONES Y MODALES        */
    /* ========================================================= */
    .mobile-ubicaciones-list {
        display: none;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
    }

    @media (max-width: 768px) {
        .table-wrapper {
            display: none !important;
        }
        .mobile-ubicaciones-list {
            display: flex !important;
        }
        .modal-content, .modal-card {
            width: 95% !important;
            max-width: 480px !important;
            padding: 20px 16px !important;
            border-radius: 18px !important;
            max-height: 88vh !important;
            overflow-y: auto !important;
        }
        .modal-actions {
            flex-direction: column !important;
            gap: 10px !important;
        }
        .modal-actions .btn-submit,
        .modal-actions .btn-cancel {
            width: 100% !important;
            flex: none !important;
        }
    }

    .mobile-ubic-card {
        background: linear-gradient(135deg, rgba(15, 32, 68, 0.7) 0%, rgba(10, 22, 50, 0.85) 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    [data-theme="light"] .mobile-ubic-card {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
    }

    .mub-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        padding-bottom: 10px;
    }

    [data-theme="light"] .mub-header {
        border-bottom: 1px solid #e2e8f0;
    }

    .mub-title-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mub-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: rgba(201, 162, 39, 0.15);
        color: var(--accent-gold);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .mub-title {
        font-size: 15.5px;
        font-weight: 800;
        color: var(--accent-gold);
        line-height: 1.2;
    }

    [data-theme="light"] .mub-title {
        color: #b45309 !important;
    }

    .mub-sub {
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .mub-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px 12px;
    }

    .mub-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .mub-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
    }

    .mub-val {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .mub-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        padding-top: 10px;
    }

    [data-theme="light"] .mub-actions {
        border-top: 1px solid #e2e8f0;
    }

    .mub-btn-salones {
        flex: 1;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        padding: 7px 12px;
        color: var(--accent-gold) !important;
    }

</style>
@endpush

@section('content')
<div>
    <!-- LISTA DE UBICACIONES -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-geo-alt" style="color:var(--accent-gold);margin-right:8px"></i>Ubicaciones Registradas</span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Salones</th>
                        <th>Capacidad p/s</th>
                        <th>Capacidad Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ubicaciones as $ub)
                    <tr>
                        <td style="font-weight:600; font-size:14px;">{{ $ub->Nombre }}</td>
                        <td style="color:var(--text-muted); font-size:13px;">{{ $ub->Direccion }}</td>
                        <td><span class="badge badge-primary">{{ $ub->Salones }}</span></td>
                        <td>{{ number_format($ub->Capacidad_por_salon) }}</td>
                        <td><span class="badge badge-gold">{{ number_format($ub->capacidad_total) }}</span></td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <button class="btn btn-sm btn-secondary" style="color:var(--accent-gold); font-weight:600;" onclick="openSalonesModal({{ $ub->ID }}, '{{ addslashes($ub->Nombre) }}')">
                                    <i class="bi bi-door-open"></i> Salones
                                </button>
                                <button class="btn btn-sm btn-secondary" onclick="editUbicacion({{ json_encode($ub) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('ubicaciones.destroy', $ub) }}" method="POST" onsubmit="return confirm('¿Eliminar esta ubicación?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-secondary" style="color:#ef4444;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">
                            <i class="bi bi-geo-alt" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                            No hay ubicaciones registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <!-- VISTA MÓVIL DE TARJETAS DE UBICACIONES -->
    <div class="mobile-ubicaciones-list">
        @forelse($ubicaciones as $ub)
        <div class="mobile-ubic-card">
            <div class="mub-header">
                <div class="mub-title-wrap">
                    <div class="mub-icon"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <div class="mub-title">{{ $ub->Nombre }}</div>
                        <div class="mub-sub"><i class="bi bi-pin-map"></i> {{ $ub->Direccion ?? 'Sin dirección' }}</div>
                    </div>
                </div>
                <span class="badge badge-gold">{{ number_format($ub->capacidad_total) }} pers.</span>
            </div>

            <div class="mub-grid">
                <div class="mub-item">
                    <span class="mub-label"><i class="bi bi-door-open"></i> Salones</span>
                    <span class="mub-val"><span class="badge badge-primary">{{ $ub->Salones }} salones</span></span>
                </div>

                <div class="mub-item">
                    <span class="mub-label"><i class="bi bi-people"></i> Capacidad p/ Salón</span>
                    <span class="mub-val">{{ number_format($ub->Capacidad_por_salon) }} pers.</span>
                </div>
            </div>

            <div class="mub-actions">
                <button class="btn btn-sm btn-secondary mub-btn-salones" onclick="openSalonesModal({{ $ub->ID }}, '{{ addslashes($ub->Nombre) }}')">
                    <i class="bi bi-door-open"></i> Ver Salones
                </button>
                <button class="btn btn-sm btn-secondary" onclick="editUbicacion({{ json_encode($ub) }})" title="Editar">
                    <i class="bi bi-pencil"></i>
                </button>
                <form action="{{ route('ubicaciones.destroy', $ub) }}" method="POST" onsubmit="return confirm('¿Eliminar esta ubicación?');" style="display:inline; margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:40px; color:var(--text-muted)">
            <i class="bi bi-geo-alt" style="font-size:32px; display:block; margin-bottom:8px"></i>
            No hay ubicaciones registradas
        </div>
        @endforelse
    </div>

    </div>

    <!-- MODAL FORMULARIO (NUEVO / EDITAR) -->
    <div id="ubicacionModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title" id="formTitle"><i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nueva Ubicación</span>
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
                <form id="ubicacionForm" action="{{ route('ubicaciones.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    
                    <div class="form-group">
                        <label for="Nombre" class="form-label">Nombre de la Ubicación</label>
                        <input type="text" id="Nombre" name="Nombre" class="form-control" placeholder="Ej. Hotel Hyatt" required>
                    </div>

                    <div class="form-group">
                        <label for="Direccion" class="form-label">Dirección</label>
                        <input type="text" id="Direccion" name="Direccion" class="form-control" placeholder="Ej. Av. Reforma 123" required>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div class="form-group">
                            <label for="Salones" class="form-label">No. Salones</label>
                            <input type="number" id="Salones" name="Salones" class="form-control" value="1" min="1" required oninput="calcTotal()">
                        </div>
                        <div class="form-group">
                            <label for="Capacidad_por_salon" class="form-label">Cap. por Salón</label>
                            <input type="number" id="Capacidad_por_salon" name="Capacidad_por_salon" class="form-control" value="50" min="1" required oninput="calcTotal()">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="capacidad_total" class="form-label">Capacidad Total</label>
                        <input type="number" id="capacidad_total" name="capacidad_total" class="form-control form-control-readonly" value="50" readonly>
                        <small style="color:var(--text-muted); font-size:11px; margin-top:6px; display:block;">Se calcula automáticamente (Salones x Capacidad p/s).</small>
                    </div>

                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary btn-submit">Guardar</button>
                        <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function calcTotal() {
        let salones = document.getElementById('Salones').value;
        let cap = document.getElementById('Capacidad_por_salon').value;
        document.getElementById('capacidad_total').value = salones * cap;
    }

    function openCreateModal() {
        resetForm();
        document.getElementById('ubicacionModal').style.display = 'flex';
    }

    function editUbicacion(ub) {
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-pencil" style="color:var(--accent-gold);margin-right:8px"></i>Editar Ubicación';
        document.getElementById('ubicacionForm').action = "{{ url('ubicaciones') }}/" + ub.ID;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('Nombre').value = ub.Nombre;
        document.getElementById('Direccion').value = ub.Direccion;
        document.getElementById('Salones').value = ub.Salones;
        document.getElementById('Capacidad_por_salon').value = ub.Capacidad_por_salon;
        document.getElementById('capacidad_total').value = ub.capacidad_total;
        
        document.getElementById('ubicacionModal').style.display = 'flex';
    }

    function resetForm() {
        document.getElementById('formTitle').innerHTML = '<i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nueva Ubicación';
        document.getElementById('ubicacionForm').action = "{{ route('ubicaciones.store') }}";
        document.getElementById('formMethod').value = 'POST';
        
        document.getElementById('Nombre').value = '';
        document.getElementById('Direccion').value = '';
        document.getElementById('Salones').value = 1;
        document.getElementById('Capacidad_por_salon').value = 50;
        document.getElementById('capacidad_total').value = 50;
    }

    function closeModal() {
        document.getElementById('ubicacionModal').style.display = 'none';
        resetForm();
    }

    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        let modal = document.getElementById('ubicacionModal');
        let salonesModal = document.getElementById('salonesModal');
        if (event.target == modal) {
            closeModal();
        }
        if (event.target == salonesModal) {
            closeSalonesModal();
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Move modal to body to avoid parent transformation issues
        const modal = document.getElementById('ubicacionModal');
        if (modal) {
            document.body.appendChild(modal);
        }
        const salonesModal = document.getElementById('salonesModal');
        if (salonesModal) {
            document.body.appendChild(salonesModal);
        }
    });

    let currentUbicacionId = null;

    function openSalonesModal(ubicacionId, venueName) {
        currentUbicacionId = ubicacionId;
        document.getElementById('salonesVenueName').innerText = venueName;
        document.getElementById('newSalonName').value = '';
        loadSalones();
        document.getElementById('salonesModal').style.display = 'flex';
    }

    function closeSalonesModal() {
        document.getElementById('salonesModal').style.display = 'none';
        currentUbicacionId = null;
    }

    function loadSalones() {
        const listContainer = document.getElementById('salonesList');
        listContainer.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:24px; color:var(--text-muted);">Cargando salones...</td></tr>';
        
        fetch(`{{ url('ubicaciones') }}/${currentUbicacionId}/salones`)
            .then(res => res.json())
            .then(data => {
                listContainer.innerHTML = '';
                if (data.length === 0) {
                    listContainer.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:24px; color:var(--text-muted);"><i class="bi bi-info-circle" style="display:block; font-size:24px; margin-bottom:8px;"></i>No hay salones registrados.</td></tr>';
                    return;
                }
                data.forEach(s => {
                    const tr = document.createElement('tr');
                    tr.style.borderBottom = '1px solid rgba(255,255,255,0.04)';
                    tr.id = `salon-row-${s.ID}`;
                    tr.innerHTML = `
                        <td style="padding:12px 16px; font-weight:500; font-size:14.5px; text-align:left;" id="salon-name-cell-${s.ID}">${s.Nombre}</td>
                        <td style="padding:12px 16px; text-align:right;" id="salon-actions-cell-${s.ID}">
                            <button class="btn btn-sm btn-secondary" style="margin-right:6px;" onclick="startEditSalon(${s.ID}, '${escapeJsString(s.Nombre)}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-secondary" style="color:#ef4444;" onclick="deleteSalon(${s.ID})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    listContainer.appendChild(tr);
                });
            })
            .catch(err => {
                console.error(err);
                listContainer.innerHTML = '<tr><td colspan="2" style="text-align:center; padding:24px; color:#ef4444;">Error al cargar salones.</td></tr>';
            });
    }

    function escapeJsString(str) {
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }

    function startEditSalon(id, currentName) {
        const nameCell = document.getElementById(`salon-name-cell-${id}`);
        const actionsCell = document.getElementById(`salon-actions-cell-${id}`);
        
        nameCell.innerHTML = `<input type="text" id="edit-salon-input-${id}" class="form-control" value="${currentName}" style="padding:4px 8px; font-size:13.5px; width:100%;">`;
        actionsCell.innerHTML = `
            <button class="btn btn-sm btn-secondary" style="color:#00bc8c; margin-right:6px;" onclick="saveEditSalon(${id})">
                <i class="bi bi-check-lg"></i>
            </button>
            <button class="btn btn-sm btn-secondary" onclick="loadSalones()">
                <i class="bi bi-x-lg"></i>
            </button>
        `;
    }

    function saveEditSalon(id) {
        const newName = document.getElementById(`edit-salon-input-${id}`).value.trim();
        if (!newName) return;

        fetch(`{{ url('salones') }}/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ Nombre: newName })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadSalones();
                Swal.fire({
                    title: '¡Actualizado!',
                    text: data.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        })
        .catch(err => console.error(err));
    }

    function addSalon(e) {
        e.preventDefault();
        const nombreInput = document.getElementById('newSalonName');
        const nombre = nombreInput.value.trim();
        if (!nombre) return;

        fetch(`{{ url('ubicaciones') }}/${currentUbicacionId}/salones`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ Nombre: nombre })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                nombreInput.value = '';
                loadSalones();
            }
        })
        .catch(err => console.error(err));
    }

    function deleteSalon(id) {
        Swal.fire({
            title: '¿Eliminar este salón?',
            text: "Esta acción no se puede revertir.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: 'var(--border)',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ url('salones') }}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadSalones();
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => console.error(err));
            }
        });
    }
</script>

<!-- MODAL GESTIONAR SALONES -->
<div id="salonesModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <span class="modal-title"><i class="bi bi-door-open" style="color:var(--accent-gold);margin-right:8px"></i>Salones de <span id="salonesVenueName"></span></span>
            <button class="modal-close" onclick="closeSalonesModal()">&times;</button>
        </div>
        <div class="card-body" style="padding: 0;">
            <!-- Form to Add Salon -->
            <form id="addSalonForm" onsubmit="addSalon(event)" style="display:flex; gap:12px; margin-bottom:20px;">
                <div style="flex:1;">
                    <input type="text" id="newSalonName" class="form-control" placeholder="Nombre del Salón (Ej: Auditorio)" required style="width:100%;">
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Agregar</button>
            </form>

            <!-- List of Current Salons -->
            <div style="max-height: 300px; overflow-y: auto; border:1px solid rgba(255,255,255,0.08); border-radius:10px; background:rgba(0,0,0,0.15);">
                <table style="width:100%; border-collapse:collapse;" id="salonesTable">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.08); text-align:left; font-size:11px; text-transform:uppercase; color:var(--text-muted);">
                            <th style="padding:12px 16px;">Nombre del Salón</th>
                            <th style="padding:12px 16px; text-align:right;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="salonesList">
                        <!-- Dynamically loaded -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('ubicacionModal').style.display = 'flex';
    });
</script>
@endif
@endsection
