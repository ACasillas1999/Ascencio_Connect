@extends('layouts.app')

@section('title', 'Editar Participante')
@section('page-title', 'Editar Participante')

@section('topbar-actions')
    <a href="{{ route('participantes.show', $participante) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Cancelar
    </a>
@endsection

@push('styles')
<style>
    select.form-control option {
        background-color: var(--bg-card, #1e293b);
        color: var(--text-primary, #f8fafc);
    }
    @media (max-width: 768px) { div[style*="grid-template-columns"] { grid-template-columns: 1fr !important; gap: 16px !important; } .form-control { font-size: 16px !important; } }
</style>
@endpush

@section('content')
<div style="max-width:700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="bi bi-pencil" style="color:var(--accent-gold);margin-right:8px"></i>{{ $participante->Nombre }}</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('participantes.update', $participante) }}">
                @csrf @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="Nombre">Nombre completo *</label>
                        <input id="Nombre" name="Nombre" type="text" class="form-control"
                               value="{{ old('Nombre', $participante->Nombre) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="RFC">RFC</label>
                        <input id="RFC" name="RFC" type="text" class="form-control" maxlength="20"
                               value="{{ old('RFC', $participante->RFC) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Telefono">Teléfono</label>
                        <input id="Telefono" name="Telefono" type="text" class="form-control" maxlength="15"
                               value="{{ old('Telefono', $participante->Telefono) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Sucursal">Sucursal</label>
                        @php $suc = old('Sucursal', $participante->Sucursal); @endphp
                        <select id="Sucursal" name="Sucursal" class="form-control">
                            <option value="" {{ $suc == '' ? 'selected' : '' }}>-- Selecciona una Sucursal --</option>
                            <option value="DIMEGSA" {{ $suc == 'DIMEGSA' ? 'selected' : '' }}>DIMEGSA</option>
                            <option value="DEASA" {{ $suc == 'DEASA' ? 'selected' : '' }}>DEASA</option>
                            <option value="AIESA" {{ $suc == 'AIESA' ? 'selected' : '' }}>AIESA</option>
                            <option value="SEGSA" {{ $suc == 'SEGSA' ? 'selected' : '' }}>SEGSA</option>
                            <option value="FESA" {{ $suc == 'FESA' ? 'selected' : '' }}>FESA</option>
                            <option value="TAPATIA" {{ $suc == 'TAPATIA' ? 'selected' : '' }}>TAPATIA</option>
                            <option value="GABSA" {{ $suc == 'GABSA' ? 'selected' : '' }}>GABSA</option>
                            <option value="ILUMINACION" {{ $suc == 'ILUMINACION' ? 'selected' : '' }}>ILUMINACION</option>
                            <option value="VALLARTA" {{ $suc == 'VALLARTA' ? 'selected' : '' }}>VALLARTA</option>
                            <option value="QUERETARO" {{ $suc == 'QUERETARO' ? 'selected' : '' }}>QUERETARO</option>
                            <option value="CODI" {{ $suc == 'CODI' ? 'selected' : '' }}>CODI</option>
                        </select>
                    </div>
                    <div class="form-group" style="position:relative;">
                        <label class="form-label" for="Vendedor">Vendedor</label>
                        <input id="Vendedor" name="Vendedor" type="text" class="form-control"
                               value="{{ old('Vendedor', $participante->Vendedor) }}" placeholder="Escribe o selecciona un vendedor..." autocomplete="off">
                        <div id="vendedor-dropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; background:var(--bg-card, #1e293b); border:1px solid var(--border-subtle, #334155); border-radius:8px; max-height:200px; overflow-y:auto; z-index:100; box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                        </div>
                    </div>
                    <div class="form-group" style="grid-column:1/-1">
                        <label class="form-label" for="Proveedor">Proveedor / Empresa</label>
                        <input id="Proveedor" name="Proveedor" type="text" class="form-control"
                               value="{{ old('Proveedor', $participante->Proveedor) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Puesto">Puesto</label>
                        @php $p = old('Puesto', $participante->Puesto); @endphp
                        <select id="Puesto" name="Puesto" class="form-control">
                            <option value="" {{ $p == '' ? 'selected' : '' }}>Selecciona un puesto</option>
                            <option value="Ingeniero" {{ $p == 'Ingeniero' ? 'selected' : '' }}>Ingeniero</option>
                            <option value="Electricista" {{ $p == 'Electricista' ? 'selected' : '' }}>Electricista</option>
                            <option value="Ayudante" {{ $p == 'Ayudante' ? 'selected' : '' }}>Ayudante</option>
                            <option value="Supervisor" {{ $p == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                            <option value="Compras" {{ $p == 'Compras' ? 'selected' : '' }}>Compras</option>
                            <option value="Mantenimiento" {{ $p == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="Jefe de Área" {{ $p == 'Jefe de Área' ? 'selected' : '' }}>Jefe de Área</option>
                            <option value="Otro" {{ $p == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="Puntos">Puntos</label>
                        <input id="Puntos" name="Puntos" type="number" min="0" class="form-control"
                               value="{{ old('Puntos', $participante->Puntos) }}">
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:8px">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
                    <a href="{{ route('participantes.show', $participante) }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const vendedorInput = document.getElementById('Vendedor');
    const vendedorDropdown = document.getElementById('vendedor-dropdown');

    if (vendedorInput && vendedorDropdown) {
        let listaVendedores = [];
        let isDeleting = false;

        fetch('{{ route("participantes.buscar-vendedores") }}')
            .then(r => r.json())
            .then(data => { listaVendedores = data; })
            .catch(err => console.error(err));

        vendedorInput.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                isDeleting = true;
            } else {
                isDeleting = false;
            }

            if ((e.key === 'Tab' || e.key === 'Enter' || e.key === 'ArrowRight') && this.selectionStart < this.selectionEnd) {
                this.setSelectionRange(this.value.length, this.value.length);
                vendedorDropdown.style.display = 'none';
                if (e.key === 'Enter') e.preventDefault();
            }
        });

        function procesarAutocompletado() {
            const fullVal = vendedorInput.value;
            const cursor = vendedorInput.selectionStart;
            const typed = fullVal.substring(0, cursor);

            if (!typed || isDeleting) {
                mostrarDropdown(typed);
                return;
            }

            const match = listaVendedores.find(v => v.toLowerCase().startsWith(typed.toLowerCase()));

            if (match) {
                const typedLen = typed.length;
                const rest = match.substring(typedLen);
                vendedorInput.value = typed + rest;
                vendedorInput.setSelectionRange(typedLen, match.length);
            }

            mostrarDropdown(typed);
        }

        function mostrarDropdown(filterText = '') {
            const matches = listaVendedores.filter(v => 
                !filterText || v.toLowerCase().includes(filterText.toLowerCase())
            ).slice(0, 10);

            if (matches.length > 0) {
                let html = '<ul style="list-style:none; margin:0; padding:0;">';
                matches.forEach(name => {
                    html += `
                        <li class="vendedor-item" style="padding:10px 14px; cursor:pointer; border-bottom:1px solid rgba(255,255,255,0.05); color:var(--text-primary); font-size:13px; display:flex; align-items:center; gap:8px; transition:background 0.15s;">
                            <i class="bi bi-person-badge" style="color:var(--accent-gold);"></i> ${name}
                        </li>
                    `;
                });
                html += '</ul>';
                vendedorDropdown.innerHTML = html;
                vendedorDropdown.style.display = 'block';

                document.querySelectorAll('.vendedor-item').forEach(li => {
                    li.addEventListener('mouseenter', function() { this.style.background = 'rgba(212,175,55,0.15)'; });
                    li.addEventListener('mouseleave', function() { this.style.background = 'transparent'; });
                    li.addEventListener('click', function() {
                        const val = this.textContent.trim();
                        vendedorInput.value = val;
                        vendedorInput.setSelectionRange(val.length, val.length);
                        vendedorDropdown.style.display = 'none';
                    });
                });
            } else {
                vendedorDropdown.style.display = 'none';
            }
        }

        vendedorInput.addEventListener('focus', function() {
            if (listaVendedores.length === 0) {
                fetch('{{ route("participantes.buscar-vendedores") }}')
                    .then(r => r.json())
                    .then(data => {
                        listaVendedores = data;
                        mostrarDropdown(vendedorInput.value.trim());
                    });
            } else {
                mostrarDropdown(vendedorInput.value.trim());
            }
        });

        vendedorInput.addEventListener('input', function() {
            procesarAutocompletado();
        });

        document.addEventListener('click', function(e) {
            if (e.target !== vendedorInput && !vendedorDropdown.contains(e.target)) {
                vendedorDropdown.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
