@extends('layouts.app')

@section('title', 'Registrar Participante')
@section('page-title', 'Registrar Participante')

@section('topbar-actions')
    <a href="{{ route('participantes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@push('styles')
<style>
    select.form-control option {
        background-color: var(--bg-card, #1e293b);
        color: var(--text-primary, #f8fafc);
    }
</style>
@endpush

@section('content')
<div style="max-width:1100px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-person-plus" style="color:var(--accent-gold);margin-right:8px"></i>Nuevo Participante</span>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
                    <ul style="margin:0; padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('participantes.store') }}" id="form-participante">
                @csrf

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="ID_Evento">Seleccionar Evento *</label>
                    <select id="ID_Evento" name="ID_Evento" class="form-control" required>
                        <option value="">-- Selecciona un Evento --</option>
                        @foreach($eventos as $ev)
                            <option value="{{ $ev->ID }}" {{ old('ID_Evento', request('evento')) == $ev->ID ? 'selected' : '' }} data-obligatorias="{{ $ev->clases_obligatorias ? '1' : '0' }}">
                                {{ $ev->name_evento }} ({{ $ev->fecha_inicio->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 32px; align-items: start;">
                    <!-- LEFT COLUMN -->
                    <div>
                        <h5 style="color:var(--accent-gold); margin-bottom:16px; border-bottom:1px solid #334155; padding-bottom:8px;"><i class="bi bi-person-lines-fill"></i> Datos del Participante</h5>
                        <div style="display:grid;grid-template-columns:1fr;gap:16px">
                            <div class="form-group">
                                <label class="form-label" for="Nombre">Nombre Completo *</label>
                                <input id="Nombre" name="Nombre" type="text" class="form-control"
                                       value="{{ old('Nombre') }}" placeholder="Ej: Juan Pérez" required>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div class="form-group">
                                    <label class="form-label" for="RFC">RFC *</label>
                                    <input id="RFC" name="RFC" type="text" class="form-control"
                                           value="{{ old('RFC') }}" required>
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="Telefono">Teléfono (WhatsApp) *</label>
                                    <input id="Telefono" name="Telefono" type="text" class="form-control"
                                           value="{{ old('Telefono') }}" placeholder="10 dígitos" required>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div class="form-group">
                                    <label class="form-label" for="Sucursal">Sucursal</label>
                                    <input id="Sucursal" name="Sucursal" type="text" class="form-control"
                                           value="{{ old('Sucursal') }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="Vendedor">Vendedor</label>
                                    <input id="Vendedor" name="Vendedor" type="text" class="form-control"
                                           value="{{ old('Vendedor') }}">
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                                <div class="form-group">
                                    <label class="form-label" for="Proveedor">Proveedor</label>
                                    <input id="Proveedor" name="Proveedor" type="text" class="form-control"
                                           value="{{ old('Proveedor') }}">
                                </div>

                                <div class="form-group">
                                    <label class="form-label" for="Puesto">Puesto</label>
                                    <select id="Puesto" name="Puesto" class="form-control">
                                        <option value="">Selecciona un puesto</option>
                                        <option value="Ingeniero" {{ old('Puesto') == 'Ingeniero' ? 'selected' : '' }}>Ingeniero</option>
                                        <option value="Electricista" {{ old('Puesto') == 'Electricista' ? 'selected' : '' }}>Electricista</option>
                                        <option value="Ayudante" {{ old('Puesto') == 'Ayudante' ? 'selected' : '' }}>Ayudante</option>
                                        <option value="Supervisor" {{ old('Puesto') == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                        <option value="Compras" {{ old('Puesto') == 'Compras' ? 'selected' : '' }}>Compras</option>
                                        <option value="Mantenimiento" {{ old('Puesto') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                        <option value="Jefe de Área" {{ old('Puesto') == 'Jefe de Área' ? 'selected' : '' }}>Jefe de Área</option>
                                        <option value="Otro" {{ old('Puesto') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div>
                        <h5 style="color:var(--accent-gold); margin-bottom:12px; border-bottom:1px solid #334155; padding-bottom:8px;"><i class="bi bi-calendar-check"></i> Registro a Actividades</h5>
                        <p style="font-size: 13px; color: #94a3b8; margin-bottom:16px;">
                            Selecciona las clases/actividades a las que asistirá el participante. 
                            <span id="clases-req-text" style="color: #fbbf24; display: none;">(Obligatorio para este evento)</span>
                        </p>

                        <!-- Lista de actividades dinámicas por evento (Requiere AJAX) -->
                        <div id="actividades-container" style="background: #1e293b; padding: 16px; border-radius: 8px; max-height: 500px; overflow-y: auto;">
                            <p style="color:#94a3b8; margin:0;">Por favor, selecciona un evento primero para ver las actividades disponibles.</p>
                        </div>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:24px">
                    <button type="submit" class="btn btn-primary" id="btn-guardar">
                        <i class="bi bi-check-lg"></i> Registrar Participante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventoSelect = document.getElementById('ID_Evento');
        const reqText = document.getElementById('clases-req-text');
        
        const actividadesContainer = document.getElementById('actividades-container');

        // Filtrar títulos en tiempo real
        const nombreInput = document.getElementById('Nombre');
        if (nombreInput) {
            nombreInput.addEventListener('input', function() {
                const regex = /\b(lic|ing|ingeniero|licenciad[oa]|arq|arquitect[oa]|dr|doctor[a]?|mtra|mtro|maestr[oa])\b\.?\s*/gi;
                if (regex.test(this.value)) {
                    this.value = this.value.replace(regex, '');
                }
            });
        }

        function updateUI() {
            const selectedOpt = eventoSelect.options[eventoSelect.selectedIndex];
            if (!selectedOpt.value) {
                reqText.style.display = 'none';
                actividadesContainer.innerHTML = '<p style="color:#94a3b8; margin:0;">Por favor, selecciona un evento primero para ver las actividades disponibles.</p>';
                return;
            }

            const isRequired = selectedOpt.getAttribute('data-obligatorias') === '1';
            reqText.style.display = isRequired ? 'inline' : 'none';

            actividadesContainer.innerHTML = '<p style="color:#94a3b8; margin:0;">Cargando actividades...</p>';

            const url = `{{ url('eventos') }}/${selectedOpt.value}/agenda-json`;
            
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        actividadesContainer.innerHTML = '<p style="color:#94a3b8; margin:0;">No hay actividades registradas en la agenda de este evento.</p>';
                        return;
                    }

                    let html = '<div style="display:grid; grid-template-columns: 1fr; gap: 12px;">';
                    data.forEach(item => {
                        let fechaArr = item.Fecha.split('-');
                        let fechaFormat = fechaArr.length === 3 ? `${fechaArr[2]}/${fechaArr[1]}/${fechaArr[0]}` : item.Fecha;
                        
                        let badgeExclusiva = item.Exclusiva == 1 
                            ? '<span class="badge" style="background:#fbbf24; color:#000; font-size:10px; margin-left:8px; padding:3px 6px;">Exclusiva</span>' 
                            : '';

                        html += `
                            <label class="actividad-label" style="display:flex; align-items:center; gap:12px; background:rgba(255,255,255,0.05); padding:12px; border-radius:8px; cursor:pointer; border:1px solid rgba(255,255,255,0.1); transition:all 0.2s;">
                                <input type="checkbox" name="actividades[]" value="${item.ID}" class="actividad-checkbox" data-fecha="${item.Fecha}" data-horario="${item.Horario}" style="accent-color:var(--accent-gold); width:18px; height:18px;">
                                <div style="display:flex; flex-direction:column;">
                                    <span style="font-size:14px; font-weight:600; color:var(--text-primary);">${item.Actividad}${badgeExclusiva}</span>
                                    <span style="font-size:12px; color:var(--text-muted); margin-top:4px;"><i class="bi bi-calendar"></i> ${fechaFormat} <i class="bi bi-clock"></i> ${item.Horario} | <i class="bi bi-geo-alt"></i> ${item.Salon}</span>
                                </div>
                            </label>
                        `;
                    });
                    html += '</div>';
                    actividadesContainer.innerHTML = html;

                    // Agregar validación de solapamiento en frontend
                    const checkboxes = document.querySelectorAll('.actividad-checkbox');
                    
                    const parseTime = (str) => {
                        let p = str.split(':');
                        return parseInt(p[0]||0) * 60 + parseInt(p[1]||0);
                    };

                    const parseHorario = (horarioStr) => {
                        let parts = horarioStr.split('-');
                        let inicio = parseTime(parts[0].trim());
                        let fin = parts.length > 1 ? parseTime(parts[1].trim()) : inicio + 60;
                        return { inicio, fin };
                    };

                    const checkOverlaps = () => {
                        const checked = Array.from(checkboxes).filter(cb => cb.checked).map(cb => {
                            let range = parseHorario(cb.dataset.horario);
                            return { id: cb.value, fecha: cb.dataset.fecha, inicio: range.inicio, fin: range.fin };
                        });

                        checkboxes.forEach(cb => {
                            if (cb.checked) {
                                cb.disabled = false;
                                cb.closest('label').style.background = 'rgba(16,185,129,0.15)'; // Verde
                                cb.closest('label').style.borderColor = 'rgba(16,185,129,0.5)';
                                cb.closest('label').style.opacity = '1';
                                cb.closest('label').title = '';
                                return;
                            }
                            
                            let range = parseHorario(cb.dataset.horario);
                            let overlap = checked.some(c => c.fecha === cb.dataset.fecha && range.inicio < c.fin && range.fin > c.inicio);
                            
                            if (overlap) {
                                cb.disabled = true;
                                cb.closest('label').style.background = 'rgba(239,68,68,0.15)'; // Rojo
                                cb.closest('label').style.borderColor = 'rgba(239,68,68,0.5)';
                                cb.closest('label').style.opacity = '0.6';
                                cb.closest('label').title = 'Se solapa con otra actividad seleccionada';
                            } else {
                                cb.disabled = false;
                                cb.closest('label').style.background = 'rgba(255,255,255,0.05)'; // Normal
                                cb.closest('label').style.borderColor = 'rgba(255,255,255,0.1)';
                                cb.closest('label').style.opacity = '1';
                                cb.closest('label').title = '';
                            }
                        });
                    };

                    checkboxes.forEach(cb => cb.addEventListener('change', checkOverlaps));
                })
                .catch(err => {
                    console.error('Error al obtener la agenda:', err);
                    actividadesContainer.innerHTML = '<p style="color:#ef4444; margin:0;">Error al cargar las actividades.</p>';
                });
        }

        eventoSelect.addEventListener('change', updateUI);
        updateUI();

        // Frontend Validation for form submission
        const form = document.getElementById('form-participante');
        form.addEventListener('submit', function(e) {
            const selectedOpt = eventoSelect.options[eventoSelect.selectedIndex];
            if (selectedOpt && selectedOpt.getAttribute('data-obligatorias') === '1') {
                const checkedActivities = document.querySelectorAll('.actividad-checkbox:checked');
                if (checkedActivities.length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Actividades obligatorias',
                            text: 'Este evento requiere que selecciones al menos una actividad/clase.',
                            confirmButtonColor: '#fbbf24',
                            background: '#1e293b',
                            color: '#f8fafc'
                        });
                    } else {
                        alert('Este evento requiere que selecciones al menos una actividad/clase.');
                    }
                }
            }
        });
    });
</script>
@endsection
