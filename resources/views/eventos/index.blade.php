@extends('layouts.app')

@section('title', 'Eventos')
@section('page-title', 'Eventos')

@section('topbar-actions')
    <a href="{{ route('eventos.create') }}" class="btn btn-primary" id="btn-nuevo-evento">
        <i class="bi bi-plus-lg"></i> Nuevo Evento
    </a>
@endsection

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
                            <a href="{{ route('eventos.edit', $evento) }}" class="btn btn-sm btn-secondary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
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

    @if($eventos->hasPages())
    <div style="padding:16px 24px;border-top:1px solid var(--border-subtle)">
        {{ $eventos->links() }}
    </div>
    @endif
</div>

@endsection
