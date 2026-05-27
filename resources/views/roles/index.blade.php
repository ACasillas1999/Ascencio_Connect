@extends('layouts.app')

@section('title', 'Roles & Permisos')
@section('page-title', 'Roles & Permisos')

@section('content')

@if(session('success'))
    <div style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #10b981; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div style="background: rgba(59,130,246,0.15); border: 1px solid rgba(59,130,246,0.3); color: #3b82f6; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="bi bi-info-circle"></i> {{ session('info') }}
    </div>
@endif
@if(session('error'))
    <div style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #ef4444; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif

{{-- Tarjetas de Roles --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 14px; margin-bottom: 30px;">
    @foreach($roles as $role)
        @php
            $roleStyle = match($role->name) {
                'Admin'     => ['icon' => 'bi-shield-fill-check',    'color' => '#d4af37', 'bg' => 'rgba(212,175,55,0.08)',  'border' => 'rgba(212,175,55,0.2)'],
                'Gerente'   => ['icon' => 'bi-briefcase-fill',       'color' => '#3b82f6', 'bg' => 'rgba(59,130,246,0.08)',  'border' => 'rgba(59,130,246,0.2)'],
                'Vendedor'  => ['icon' => 'bi-cart-fill',            'color' => '#10b981', 'bg' => 'rgba(16,185,129,0.08)',  'border' => 'rgba(16,185,129,0.2)'],
                'Proveedor' => ['icon' => 'bi-truck',                'color' => '#a855f7', 'bg' => 'rgba(168,85,247,0.08)',  'border' => 'rgba(168,85,247,0.2)'],
                'Evento'    => ['icon' => 'bi-calendar-event-fill',  'color' => '#f97316', 'bg' => 'rgba(249,115,22,0.08)',  'border' => 'rgba(249,115,22,0.2)'],
                default     => ['icon' => 'bi-person',               'color' => '#94a3b8', 'bg' => 'rgba(100,116,139,0.08)','border' => 'rgba(100,116,139,0.2)'],
            };
        @endphp
        <div style="background: {{ $roleStyle['bg'] }}; border: 1px solid {{ $roleStyle['border'] }}; border-radius: 12px; padding: 20px; text-align: center;">
            <div style="font-size: 28px; color: {{ $roleStyle['color'] }}; margin-bottom: 8px;">
                <i class="bi {{ $roleStyle['icon'] }}"></i>
            </div>
            <h3 style="margin: 0 0 4px; font-size: 16px; font-weight: 700; color: var(--text-primary, #e2e8f0);">{{ $role->name }}</h3>
            <p style="margin: 0; font-size: 12px; color: var(--text-secondary, #94a3b8);">
                {{ $role->count }} usuario{{ $role->count !== 1 ? 's' : '' }} · {{ $role->activos }}/{{ $role->total }} módulos
            </p>
            @if($role->name !== 'Admin')
                <a href="{{ route('roles.edit', $role->name) }}" class="btn btn-sm btn-secondary" style="margin-top: 10px; font-size: 11px;">
                    <i class="bi bi-sliders"></i> Configurar
                </a>
            @else
                <span style="display: inline-block; margin-top: 10px; font-size: 11px; color: {{ $roleStyle['color'] }}; opacity: 0.7;">
                    <i class="bi bi-lock-fill"></i> Acceso total
                </span>
            @endif
        </div>
    @endforeach
</div>

{{-- Matriz de Permisos --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="bi bi-table"></i> Matriz de Permisos</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="padding: 14px 16px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary, #94a3b8); border-bottom: 1px solid var(--border, #1e293b); position: sticky; left: 0; background: var(--card, #0f172a); z-index: 2;">
                            Módulo
                        </th>
                        @foreach($roles as $role)
                            @php
                                $color = match($role->name) {
                                    'Admin' => '#d4af37', 'Gerente' => '#3b82f6', 'Vendedor' => '#10b981',
                                    'Proveedor' => '#a855f7', 'Evento' => '#f97316', default => '#94a3b8',
                                };
                            @endphp
                            <th style="padding: 14px 12px; text-align: center; font-size: 12px; font-weight: 700; color: {{ $color }}; border-bottom: 1px solid var(--border, #1e293b); min-width: 90px;">
                                {{ $role->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($modulos as $key => $modulo)
                        <tr style="border-bottom: 1px solid var(--border, rgba(30,41,59,0.5));">
                            <td style="padding: 12px 16px; position: sticky; left: 0; background: var(--card, #0f172a); z-index: 1;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="bi {{ $modulo['icon'] }}" style="font-size: 16px; color: var(--text-secondary, #94a3b8); width: 20px; text-align: center;"></i>
                                    <div>
                                        <div style="font-weight: 600; font-size: 13px; color: var(--text-primary, #e2e8f0);">{{ $modulo['label'] }}</div>
                                        <div style="font-size: 11px; color: var(--text-secondary, #64748b);">{{ $modulo['desc'] }}</div>
                                    </div>
                                </div>
                            </td>
                            @foreach($roles as $role)
                                @php
                                    $activo = isset($matriz[$role->name][$key]) ? $matriz[$role->name][$key] : 0;
                                    $isAdmin = $role->name === 'Admin';
                                @endphp
                                <td style="padding: 12px; text-align: center;">
                                    @if($isAdmin)
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: rgba(212,175,55,0.15); color: #d4af37; font-size: 14px;">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    @elseif($activo)
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: rgba(16,185,129,0.15); color: #10b981; font-size: 14px;">
                                            <i class="bi bi-check-lg"></i>
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; background: rgba(239,68,68,0.08); color: rgba(239,68,68,0.4); font-size: 12px;">
                                            <i class="bi bi-x-lg"></i>
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
