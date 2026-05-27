<?php $aparienciaConfig = \App\Models\Apariencia::getConfig(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ascencio Connect') — Sistema de Congresos</title>
    <meta name="description" content="Sistema de gestión de congresos Grupo Ascencio">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary:     {{ $aparienciaConfig['bg_primary'] }};
            --bg-secondary:   {{ $aparienciaConfig['bg_secondary'] }};
            --bg-card:        rgba(15, 32, 68, 0.85); /* Si quieres, esto también se podría calcular luego con alpha */
            --bg-sidebar:     {{ $aparienciaConfig['bg_sidebar'] }};
            --accent-gold:    {{ $aparienciaConfig['tema_gold'] }};
            --accent-blue:    {{ $aparienciaConfig['tema_blue'] }};
            --accent-green:   #10b981;
            --accent-red:     #ef4444;
            --accent-orange:  #f97316;
            --text-primary:   {{ $aparienciaConfig['text_primary'] }};
            --text-secondary: #94a3b8;
            --text-muted:     #64748b;
            --border:         {{ str_replace(')', ', 0.2)', str_replace('rgb', 'rgba', App\Models\Apariencia::getConfig()['tema_gold'])) }}; /* Fallback simplificado */
            --border-subtle:  rgba(255,255,255,0.06);
            --sidebar-width:  260px;
            --radius:         12px;
            --shadow:         0 8px 32px rgba(0,0,0,0.4);
        }

        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid var(--border);
        }
        .brand-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent-gold);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-logo i { font-size: 22px; }
        .brand-sub {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 12px 8px 6px;
        }

        .nav-item { list-style: none; margin-bottom: 2px; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all .2s ease;
            position: relative;
        }
        .nav-link:hover {
            background: rgba(201,162,39,.1);
            color: var(--accent-gold);
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(201,162,39,.2), rgba(59,130,246,.15));
            color: var(--accent-gold);
            border-left: 3px solid var(--accent-gold);
        }
        .nav-link i { font-size: 16px; min-width: 20px; }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--border-subtle);
        }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent-gold), var(--accent-blue));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px;
            color: #000;
            flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name  { font-size: 13px; font-weight: 600; color: var(--text-primary); truncate: ellipsis; white-space: nowrap; overflow: hidden; }
        .user-role  { font-size: 11px; color: var(--accent-gold); font-weight: 500; }

        /* ── MAIN CONTENT ── */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-subtle);
            padding: 0 28px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            backdrop-filter: blur(10px);
        }
        .topbar-title { font-size: 18px; font-weight: 700; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px; font-weight: 600;
            border: none; cursor: pointer;
            transition: all .2s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-gold), #a07c1a);
            color: #000;
        }
        .btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(201,162,39,.3); }
        .btn-secondary {
            background: var(--border-subtle);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        .btn-secondary:hover { background: var(--border); }
        .btn-danger { background: rgba(239,68,68,.15); color: var(--accent-red); border: 1px solid rgba(239,68,68,.3); }
        .btn-danger:hover { background: rgba(239,68,68,.25); }
        .btn-sm { padding: 5px 12px; font-size: 12px; }

        /* ── PAGE CONTENT ── */
        .page-content { padding: 28px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 15px; font-weight: 700; }
        .card-body  { padding: 24px; }

        /* ── KPI CARDS ── */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius);
            padding: 20px 24px;
            display: flex; align-items: center; gap: 16px;
            position: relative; overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: var(--kpi-color, var(--accent-gold));
        }
        .kpi-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            background: rgba(255,255,255,.06);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            color: var(--kpi-color, var(--accent-gold));
            flex-shrink: 0;
        }
        .kpi-value { font-size: 28px; font-weight: 800; line-height: 1; }
        .kpi-label { font-size: 12px; color: var(--text-muted); margin-top: 4px; font-weight: 500; }

        /* ── TABLE ── */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            padding: 12px 16px;
            text-align: left;
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-subtle);
            white-space: nowrap;
        }
        td {
            padding: 13px 16px;
            font-size: 13.5px;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-primary);
        }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-success  { background: rgba(16,185,129,.15); color: var(--accent-green); }
        .badge-warning  { background: rgba(249,115,22,.15); color: var(--accent-orange); }
        .badge-secondary{ background: rgba(100,116,139,.15); color: var(--text-muted); }
        .badge-primary  { background: rgba(59,130,246,.15); color: var(--accent-blue); }
        .badge-gold     { background: rgba(201,162,39,.15); color: var(--accent-gold); }

        /* ── FORMS ── */
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; letter-spacing: .5px; }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-primary);
            padding: 10px 14px;
            font-size: 13.5px;
            font-family: inherit;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent-gold);
            box-shadow: 0 0 0 3px rgba(201,162,39,.15);
        }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control { cursor: pointer; }

        /* ── ALERTS ── */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 16px; }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #6ee7b7; }
        .alert-danger   { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; }
        .alert-warning  { background: rgba(249,115,22,.1); border: 1px solid rgba(249,115,22,.3); color: #fdba74; }

        /* ── PAGINATION ── */
        .pagination { display: flex; gap: 4px; list-style: none; }
        .page-item .page-link {
            padding: 6px 12px; border-radius: 6px;
            font-size: 13px; font-weight: 500;
            color: var(--text-secondary);
            background: var(--border-subtle);
            border: 1px solid var(--border-subtle);
            text-decoration: none;
            transition: all .2s;
        }
        .page-item.active .page-link,
        .page-item .page-link:hover {
            background: var(--accent-gold);
            color: #000;
            border-color: var(--accent-gold);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .kpi-grid { grid-template-columns: 1fr 1fr; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
        .page-content > * { animation: fadeIn .3s ease forwards; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">
            <i class="bi bi-lightning-charge-fill"></i>
            Ascencio Connect
        </div>
        <div class="brand-sub">Sistema de Congresos</div>
    </div>

    <nav class="sidebar-nav">
        <ul style="list-style:none">
            @if(auth()->check() && auth()->user()->Rol === 'Evento')
            <li class="nav-section-title">Asistencia</li>
            <li class="nav-item">
                <a href="{{ route('eventos.show', auth()->user()->ID_Evento ?? 0) }}" class="nav-link active">
                    <i class="bi bi-calendar-event"></i> Mi Evento
                </a>
            </li>
            @else
            @if(auth()->check() && auth()->user()->Rol !== 'Vendedor')
            <li class="nav-section-title">Principal</li>
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            @endif

            <li class="nav-section-title" style="margin-top:8px">Gestión</li>
            @if(auth()->check() && auth()->user()->Rol !== 'Vendedor')
            <li class="nav-item">
                <a href="{{ route('eventos.index') }}" class="nav-link {{ request()->routeIs('eventos.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-event"></i> Eventos
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('participantes.index') }}" class="nav-link {{ request()->routeIs('participantes.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Participantes
                </a>
            </li>

            @if(auth()->check() && auth()->user()->Rol !== 'Vendedor')
            <li class="nav-section-title" style="margin-top:8px">Comercial</li>
            <li class="nav-item">
                <a href="{{ route('proveedores.gestion') }}" class="nav-link {{ request()->routeIs('proveedores.gestion') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Proveedores
                </a>
            </li>
            @endif

            @if(auth()->check() && auth()->user()->Rol !== 'Vendedor')
            <li class="nav-section-title" style="margin-top:8px">Configuración</li>
            <li class="nav-item">
                <a href="{{ route('ubicaciones.index') }}" class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i> Ubicaciones
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Usuarios
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> Roles & Permisos
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('apariencia.index') }}" class="nav-link {{ request()->routeIs('apariencia.*') ? 'active' : '' }}">
                    <i class="bi bi-palette"></i> Apariencia CSS
                </a>
            </li>
            @endif
            @endif
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->username }}</div>
                <div class="user-role">{{ auth()->user()->Rol }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm" style="background:none;color:var(--text-muted);padding:6px" title="Cerrar sesión">
                    <i class="bi bi-box-arrow-right" style="font-size:16px"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
        <div class="topbar-actions">
            @yield('topbar-actions')
        </div>
    </div>

    <!-- CONTENT -->
    <div class="page-content">
        @if(session('success'))
            <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
