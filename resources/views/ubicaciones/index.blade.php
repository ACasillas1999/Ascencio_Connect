@extends('layouts.app')

@section('title', 'Gestionar Ubicaciones')
@section('page-title', 'Ubicaciones')

@section('content')
<div style="display:grid; grid-template-columns:1fr 350px; gap:24px;">

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
    </div>

    <!-- FORMULARIO (NUEVO / EDITAR) -->
    <div class="card" style="align-self:start;">
        <div class="card-header">
            <span class="card-title" id="formTitle"><i class="bi bi-plus-circle" style="color:var(--accent-gold);margin-right:8px"></i>Nueva Ubicación</span>
        </div>
        <div class="card-body">
            <form id="ubicacionForm" action="{{ route('ubicaciones.store') }}" method="POST">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                
                <div class="form-group">
                    <label for="Nombre">Nombre de la Ubicación</label>
                    <input type="text" id="Nombre" name="Nombre" class="form-control" placeholder="Ej. Hotel Hyatt" required>
                </div>

                <div class="form-group">
                    <label for="Direccion">Dirección</label>
                    <input type="text" id="Direccion" name="Direccion" class="form-control" placeholder="Ej. Av. Reforma 123" required>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label for="Salones">No. Salones</label>
                        <input type="number" id="Salones" name="Salones" class="form-control" value="1" min="1" required oninput="calcTotal()">
                    </div>
                    <div class="form-group">
                        <label for="Capacidad_por_salon">Cap. por Salón</label>
                        <input type="number" id="Capacidad_por_salon" name="Capacidad_por_salon" class="form-control" value="50" min="1" required oninput="calcTotal()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="capacidad_total">Capacidad Total</label>
                    <input type="number" id="capacidad_total" name="capacidad_total" class="form-control" value="50" readonly style="background:var(--bg-primary); color:var(--accent-gold); font-weight:700;">
                    <small style="color:var(--text-muted); font-size:11px;">Se calcula automáticamente (Salones x Capacidad p/s).</small>
                </div>

                <div style="display:flex; gap:10px; margin-top:20px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">Guardar</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary" style="display:none;" onclick="resetForm()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function calcTotal() {
        let salones = document.getElementById('Salones').value;
        let cap = document.getElementById('Capacidad_por_salon').value;
        document.getElementById('capacidad_total').value = salones * cap;
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
        
        document.getElementById('cancelBtn').style.display = 'inline-block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
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
        
        document.getElementById('cancelBtn').style.display = 'none';
    }
</script>
@endsection
