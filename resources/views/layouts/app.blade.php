<?php $aparienciaConfig = \App\Models\Apariencia::getConfig(); ?>
<!DOCTYPE html>
<html lang="es">
<head>

    <!-- PWA Manifest & App Icons -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ascencio">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-pwa.svg') }}">

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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('app_theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.addEventListener('DOMContentLoaded', function() {
                updateThemeUI(savedTheme);
            });
        })();

        function updateThemeUI(theme) {
            const icon = document.getElementById('theme-toggle-icon');
            if (icon) {
                icon.className = theme === 'light' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            }
        }

        function toggleAppTheme() {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('app_theme', next);
            updateThemeUI(next);
        }
    </script>

    <style>
        :root {
            --bg-primary:     {{ $aparienciaConfig['bg_primary'] }};
            --bg-secondary:   {{ $aparienciaConfig['bg_secondary'] }};
            --bg-card:        rgba(15, 32, 68, 0.85);
            --bg-sidebar:     {{ $aparienciaConfig['bg_sidebar'] }};
            --accent-gold:    {{ $aparienciaConfig['tema_gold'] }};
            --accent-blue:    {{ $aparienciaConfig['tema_blue'] }};
            --accent-green:   #10b981;
            --accent-red:     #ef4444;
            --accent-orange:  #f97316;
            --text-primary:   {{ $aparienciaConfig['text_primary'] }};
            --text-secondary: #94a3b8;
            --text-muted:     #64748b;
            --border:         rgba(255, 255, 255, 0.08);
            --border-subtle:  rgba(255, 255, 255, 0.06);
            --sidebar-width:  260px;
            --radius:         12px;
            --shadow:         0 8px 32px rgba(0,0,0,0.4);
        }

        /* --- BOTÓN DE TEMA MINIMALISTA UNICOLOR FLOTANTE (SIN CUADRO) --- */
        .btn-icon-theme {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 4px !important;
            width: auto !important;
            height: auto !important;
            font-size: 21px !important;
            color: var(--accent-gold) !important;
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease;
            outline: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-icon-theme:hover {
            transform: scale(1.15);
            opacity: 0.85;
        }

        .designer-sidebar {
            background: var(--bg-card);
            border-radius: 12px;
        }

        /* --- HERO BANNER DE ACTIVIDAD E IMPRESION --- */
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 16px !important;
            padding: 20px 24px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3) !important;
        }

        .hero-banner h2,
        .hero-banner h2 * {
            color: #ffffff !important;
        }

        .hero-banner .hero-sub-text {
            color: #f59e0b !important;
        }

        /* TIMETABLE GRID STYLES IN DARK MODE */
        .tt-header-cell {
            background-color: #0f172a !important;
            background: #0f172a !important;
            color: #ffffff !important;
        }

        .tt-time-cell {
            background-color: #0f172a !important;
            background: #0f172a !important;
            color: var(--accent-gold) !important;
        }

        /* ========================================== */
        /*  ESTILOS UNIFICADOS Y ELEGANTES DE TABLAS  */
        /* ========================================== */
        table, .table-modern, table.table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: var(--radius);
        }

        .table-modern th, table.table th, table th {
            padding: 12px 16px;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background-color: rgba(255, 255, 255, 0.04);
            color: var(--text-muted);
        }

        .table-modern td, table.table td, table td {
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 500;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-primary);
        }

        .table-modern tbody tr:hover, table.table tbody tr:hover, table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        /* --- MODO CLARO DE ALTO CONTRASTE --- */
        [data-theme="light"] {
            --bg-primary:     #f1f5f9;
            --bg-secondary:   #ffffff;
            --bg-card:        #ffffff;
            --bg-sidebar:     #ffffff;
            --accent-gold:    #b45309; /* Ámbar cobrizo oscuro de alto contraste */
            --accent-blue:    #0284c7;
            --text-primary:   #0f172a;
            --text-secondary: #1e293b;
            --text-muted:     #475569;
            --border:         #cbd5e1;
            --border-subtle:  rgba(0, 0, 0, 0.08);
            --shadow:         0 4px 20px rgba(0, 0, 0, 0.06);
        }

        [data-theme="light"] body {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }

        [data-theme="light"] body::before,
        [data-theme="light"] body::after {
            display: none !important;
        }

        /* HERO BANNER EN MODO CLARO (CONSERVA SU CONTENEDOR SLATE ELEGANTE) */
        [data-theme="light"] .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15) !important;
        }

        [data-theme="light"] .hero-banner h2,
        [data-theme="light"] .hero-banner h2 * {
            color: #ffffff !important;
        }

        [data-theme="light"] .hero-banner .hero-sub-text {
            color: #fbbf24 !important;
        }

        [data-theme="light"] .hero-banner .btn-secondary {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
        }

        /* CONFIGURACIÓN DEL SORTEO EN MODO CLARO */
        [data-theme="light"] #setup-view {
            color: #0f172a !important;
        }

        [data-theme="light"] #setup-view h2 {
            color: #0f172a !important;
        }

        [data-theme="light"] #setup-view div[class*="max-w-[1600px]"] {
            background-color: #ffffff !important;
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05) !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-900/80"] {
            background-color: #f8fafc !important;
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04) !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-900/80"] h3 {
            color: #b45309 !important;
            border-color: #cbd5e1 !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-900/80"] h3 span {
            color: #b45309 !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-800/40"],
        [data-theme="light"] #setup-view div[class*="bg-slate-800/60"] {
            background-color: #ffffff !important;
            background: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-800/60"]:hover {
            background-color: #f1f5f9 !important;
        }

        [data-theme="light"] #setup-view span[class*="text-slate-100"] {
            color: #0f172a !important;
        }

        [data-theme="light"] #setup-view span[class*="text-slate-400"] {
            color: #475569 !important;
        }

        [data-theme="light"] #setup-view .text-slate-400 {
            color: #475569 !important;
        }

        [data-theme="light"] #setup-view .text-slate-500 {
            color: #64748b !important;
        }

        [data-theme="light"] #setup-view div[class*="bg-slate-900/50"] {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
        }

        [data-theme="light"] #setup-view button[class*="bg-slate-800"] {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        /* BOTÓN TEMA UNICOLOR SIN CUADRO */
        [data-theme="light"] .btn-icon-theme {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
            color: #0f172a !important;
        }

        /* SIDEBAR Y NAVEGACIÓN HIGH CONTRAST */
        [data-theme="light"] .sidebar {
            background-color: #ffffff !important;
            border-right: 1px solid #cbd5e1 !important;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.04) !important;
        }

        [data-theme="light"] .sidebar-brand {
            border-bottom: 1px solid #cbd5e1 !important;
        }

        [data-theme="light"] .brand-logo {
            color: #b45309 !important;
        }

        [data-theme="light"] .brand-sub {
            color: #475569 !important;
            font-weight: 700 !important;
        }

        [data-theme="light"] .nav-section-title {
            color: #334155 !important;
            font-weight: 800 !important;
        }

        [data-theme="light"] .nav-link {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        [data-theme="light"] .nav-link:hover {
            background-color: #f1f5f9 !important;
            color: #b45309 !important;
        }

        [data-theme="light"] .nav-link.active {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border-left: 4px solid #b45309 !important;
            font-weight: 700 !important;
        }

        /* TOPBAR HIGH CONTRAST */
        [data-theme="light"] .topbar {
            background-color: #ffffff !important;
            border-bottom: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04) !important;
            padding: 0 32px !important;
        }

        [data-theme="light"] .topbar-title {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }

        [data-theme="light"] .btn-primary {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(180, 83, 9, 0.25) !important;
            font-weight: 700 !important;
        }

        [data-theme="light"] .btn-primary:hover {
            background: linear-gradient(135deg, #b45309 0%, #78350f 100%) !important;
        }

        /* CARDS, PANELES Y DISEÑADORES DE GAFETE/HORARIO CON ALTO CONTRASTE */
        [data-theme="light"] .card,
        [data-theme="light"] .card-modern,
        [data-theme="light"] .stat-card,
        [data-theme="light"] .designer-sidebar {
            background-color: #ffffff !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05) !important;
        }

        [data-theme="light"] .card-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        [data-theme="light"] .card-title,
        [data-theme="light"] h1, [data-theme="light"] h2, [data-theme="light"] h3,
        [data-theme="light"] h4, [data-theme="light"] h5, [data-theme="light"] h6 {
            color: #b45309 !important;
            font-weight: 800 !important;
            text-shadow: none !important;
        }

        /* FUERZA TEXTOS DE ETIQUETAS Y DETALLES EN MODO CLARO */
        [data-theme="light"] label,
        [data-theme="light"] .form-label,
        [data-theme="light"] span,
        [data-theme="light"] p,
        [data-theme="light"] small,
        [data-theme="light"] .designer-sidebar h4,
        [data-theme="light"] .designer-sidebar label,
        [data-theme="light"] .designer-sidebar span,
        [data-theme="light"] .designer-sidebar small,
        [data-theme="light"] .designer-sidebar div {
            color: #0f172a !important;
            font-weight: 600 !important;
        }

        /* TIMETABLE GRID EN MODO CLARO */
        [data-theme="light"] .tt-header-cell {
            background-color: #f1f5f9 !important;
            background: #f1f5f9 !important;
            color: #0f172a !important;
            border-bottom: 2px solid #b45309 !important;
        }

        [data-theme="light"] .tt-header-cell * {
            color: #0f172a !important;
        }

        [data-theme="light"] .tt-time-cell {
            background-color: #ffffff !important;
            background: #ffffff !important;
            color: #b45309 !important;
            font-weight: 800 !important;
            border-right: 1px solid #cbd5e1 !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        [data-theme="light"] .tt-time-cell * {
            color: #b45309 !important;
        }

        [data-theme="light"] .kpi-card {
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04) !important;
        }

        [data-theme="light"] .kpi-value {
            color: #0f172a !important;
        }

        [data-theme="light"] .kpi-label {
            color: #475569 !important;
            font-weight: 600 !important;
        }

        [data-theme="light"] .user-card {
            background-color: #f1f5f9 !important;
            border: 1px solid #cbd5e1 !important;
        }

        [data-theme="light"] .user-name {
            color: #0f172a !important;
            font-weight: 700 !important;
        }

        [data-theme="light"] .user-role {
            color: #b45309 !important;
            font-weight: 700 !important;
        }

        /* TABLAS Y COLUMNAS STICKY EN MODO CLARO */
        [data-theme="light"] table,
        [data-theme="light"] .table-modern,
        [data-theme="light"] table.table {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
        }

        [data-theme="light"] .table-modern th,
        [data-theme="light"] table.table th,
        [data-theme="light"] table th,
        [data-theme="light"] th[style*="sticky"] {
            background-color: #f1f5f9 !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border-bottom: 2px solid #cbd5e1 !important;
        }

        [data-theme="light"] .table-modern tr:hover,
        [data-theme="light"] table.table tr:hover,
        [data-theme="light"] table tr:hover {
            background-color: #f8fafc !important;
        }

        [data-theme="light"] .table-modern td,
        [data-theme="light"] table.table td,
        [data-theme="light"] table td,
        [data-theme="light"] td[style*="sticky"] {
            background-color: #ffffff !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        [data-theme="light"] td[style*="sticky"] *,
        [data-theme="light"] th[style*="sticky"] * {
            color: #0f172a !important;
        }

        /* INPUTS Y FORMULARIOS */
        [data-theme="light"] input.form-control,
        [data-theme="light"] select.form-control,
        [data-theme="light"] textarea.form-control {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 500 !important;
        }

        [data-theme="light"] input.form-control::placeholder {
            color: #64748b !important;
        }

        /* MODALES MODO CLARO */
        [data-theme="light"] .modal-content,
        [data-theme="light"] .modal-card {
            background-color: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.15) !important;
        }

        [data-theme="light"] .btn-secondary {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 600 !important;
        }

        [data-theme="light"] .btn-secondary:hover {
            background-color: #cbd5e1 !important;
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

        /* --- SIDEBAR --- */
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

        /* --- MAIN CONTENT --- */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
            box-sizing: border-box;
        }

        /* --- TOPBAR --- */
        .topbar {
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3), inset 0 -1px 0 rgba(255,255,255,0.02);
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        .topbar-title {
            font-size: 19px;
            font-weight: 700;
            background: linear-gradient(135deg, var(--text-primary) 0%, color-mix(in srgb, var(--text-primary) 80%, var(--accent-gold)) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
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

        /* --- PAGE CONTENT --- */
        .page-content { padding: 24px 32px; flex: 1; position: relative; z-index: 1; overflow-x: hidden; box-sizing: border-box; max-width: 100%; }

        /* --- AMBIENT GLOWS --- */
        body::before {
            content: "";
            position: fixed;
            top: -10%;
            right: -10%;
            width: 60%;
            height: 60%;
            background: radial-gradient(circle, rgba(201, 162, 39, 0.05) 0%, rgba(59, 130, 246, 0.02) 55%, transparent 100%);
            z-index: -2;
            pointer-events: none;
            filter: blur(80px);
        }
        body::after {
            content: "";
            position: fixed;
            bottom: -10%;
            left: var(--sidebar-width);
            width: 50%;
            height: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, rgba(201, 162, 39, 0.02) 55%, transparent 100%);
            z-index: -2;
            pointer-events: none;
            filter: blur(80px);
        }

        /* --- PREMIUM CARDS (SIN LÍNEAS NARANJAS EN MODO OSCURO) --- */
        .card {
            background: linear-gradient(135deg, rgba(15, 32, 68, 0.6) 0%, rgba(10, 22, 50, 0.75) 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: var(--radius);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            max-width: 100%;
            box-sizing: border-box;
            /* Do NOT set overflow here - it breaks inner scroll containers (timetable) */
        }
        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(255, 255, 255, 0.015);
            display: flex; align-items: center; justify-content: space-between;
            border-top-left-radius: var(--radius);
            border-top-right-radius: var(--radius);
        }
        .card-title { font-size: 15px; font-weight: 700; color: var(--accent-gold); }
        .card-body  { padding: 24px; }

        /* --- KPI CARDS --- */
        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .kpi-card {
            background: linear-gradient(135deg, rgba(15, 32, 68, 0.5) 0%, rgba(10, 22, 50, 0.65) 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: var(--radius);
            padding: 20px 24px;
            display: flex; align-items: center; gap: 16px;
            position: relative; overflow: hidden;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
        }
        .kpi-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.15) !important;
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

        /* --- GLOBAL MODALS OVERRIDE --- */
        .modal-overlay {
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            background: rgba(0, 0, 0, 0.5) !important;
        }

        .modal-content, .modal-card {
            background: var(--bg-secondary) !important;
            border: 1px solid var(--border) !important;
            border-radius: 16px !important;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5) !important;
        }

        /* --- FORM CONTROL --- */
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }
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

        /* --- ALERTS --- */
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-bottom: 16px; }
        .alert-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.3); color: #6ee7b7; }
        .alert-danger   { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; }
        .alert-warning  { background: rgba(249,115,22,.1); border: 1px solid rgba(249,115,22,.3); color: #fdba74; }

        /* --- PAGINATION --- */
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

        /* --- RESPONSIVE --- */
        /* VISTA TABLETA Y MÓVIL (<= 1400px): SIDEBAR OCULTO POR DEFECTO */
        @media (max-width: 1600px) {
            .sidebar {
                transform: translateX(-100%) !important;
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 1000 !important;
                transition: transform 0.3s ease !important;
                box-shadow: 10px 0 40px rgba(0, 0, 0, 0.6) !important;
            }
            .sidebar.open {
                transform: translateX(0) !important;
            }
            .main {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }

        /* --- ANIMATIONS --- */
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
        .page-content > * { animation: fadeIn .3s ease forwards; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 99px; }
    
        /* --- OVERLAY Y DRAWER MÓVIL (MENÚ HAMBURGUESA) --- */
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 90;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        #sidebar-toggle-btn {
            display: none;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-size: 26px !important;
            color: var(--text-primary) !important;
            cursor: pointer;
            padding: 2px 6px !important;
            border-radius: 6px !important;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease, opacity 0.2s ease !important;
            flex-shrink: 0 !important;
        }

        #sidebar-toggle-btn:hover {
            color: var(--accent-gold) !important;
            background: rgba(255, 255, 255, 0.05) !important;
        }

        @media (max-width: 1600px) {
            #sidebar-toggle-btn {
                display: inline-flex !important;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%) !important;
                box-shadow: 0 0 40px rgba(0, 0, 0, 0.6) !important;
            }
            .sidebar.open {
                transform: translateX(0) !important;
            }
            .main {
                margin-left: 0 !important;
            }
            .topbar {
                padding: 0 16px !important;
            }
            #sidebar-toggle-btn {
                display: inline-flex !important;
            }
            .page-content {
                padding: 16px !important;
            }
            .btn-icon-theme {
                font-size: 19px !important;
            }
        }


        /* ========================================================= */
        /* TARJETAS RESPONSIVAS MÓVILES PARA TODAS LAS TABLAS (NO SCROLL) */
        /* ========================================================= */
        @media (max-width: 768px) {
            /* Exception: timetable-grid stays as horizontal-scroll table */
            .table-wrapper:has(.timetable-grid) {
                display: block !important;
                overflow-x: auto !important;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-x !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            /* Also target by class directly */
            .timetable-grid,
            .table-wrapper .timetable-grid {
                display: table !important;
                width: max-content !important;
                min-width: 100% !important;
            }
            .timetable-grid thead,
            .timetable-grid thead tr,
            .timetable-grid thead th {
                display: table-header-group !important;
                display: revert !important;
            }
            .timetable-grid thead tr { display: table-row !important; }
            .timetable-grid thead th { display: table-cell !important; }
            .timetable-grid tbody { display: table-row-group !important; }
            .timetable-grid tbody tr { display: table-row !important; background: none !important; border: none !important; border-radius: 0 !important; padding: 0 !important; box-shadow: none !important; }
            .timetable-grid tbody td { display: table-cell !important; flex-direction: unset !important; justify-content: unset !important; padding: 8px !important; border: none !important; width: auto !important; }
            .timetable-grid tbody td::before { display: none !important; }

            /* General table-wrapper: overflow-x: auto for scrollable tables */
            .table-wrapper {
                overflow-x: auto !important;
                overflow-y: visible !important;
                -webkit-overflow-scrolling: touch !important;
                touch-action: pan-x pan-y !important;
                padding: 0 !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
            }

            .table-wrapper table:not(.timetable-grid),
            table.table-modern,
            table.table {
                display: block !important;
                width: 100% !important;
                border: none !important;
            }

            .table-wrapper table:not(.timetable-grid) thead,
            table.table-modern thead,
            table.table thead {
                display: none !important; /* Oculta cabecera plana de tabla en teléfonos */
            }

            .table-wrapper table:not(.timetable-grid) tbody,
            table.table-modern tbody,
            table.table tbody {
                display: flex !important;
                flex-direction: column !important;
                gap: 12px !important;
                width: 100% !important;
            }

            .table-wrapper table:not(.timetable-grid) tr,
            table.table-modern tr,
            table.table tr {
                display: flex !important;
                flex-direction: column !important;
                background: rgba(255, 255, 255, 0.03) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 14px !important;
                padding: 14px 16px !important;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15) !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            [data-theme="light"] .table-wrapper table tr,
            [data-theme="light"] table.table-modern tr,
            [data-theme="light"] table.table tr {
                background: #ffffff !important;
                border: 1px solid #cbd5e1 !important;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04) !important;
            }

            .table-wrapper table:not(.timetable-grid) td,
            table.table-modern td,
            table.table td {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                padding: 6px 0 !important;
                border: none !important;
                font-size: 13px !important;
                width: 100% !important;
            }

            .table-wrapper table td::before,
            table.table-modern td::before,
            table.table td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
                margin-right: 12px;
            }

            .table-wrapper table td[data-label="Nombre"]::before,
            .table-wrapper table td[data-label="Evento"]::before,
            .table-wrapper table td[data-label="#"]::before {
                display: none !important;
            }

            .table-wrapper table td[data-label="Nombre"],
            .table-wrapper table td[data-label="Evento"] {
                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                padding-bottom: 10px !important;
                margin-bottom: 6px !important;
            }

            [data-theme="light"] .table-wrapper table td[data-label="Nombre"],
            [data-theme="light"] .table-wrapper table td[data-label="Evento"] {
                border-bottom: 1px solid #cbd5e1 !important;
            }

            .table-wrapper table td[data-label="Acciones"] {
                border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
                padding-top: 10px !important;
                margin-top: 6px !important;
                justify-content: space-between !important;
            }

            [data-theme="light"] .table-wrapper table td[data-label="Acciones"] {
                border-top: 1px solid #cbd5e1 !important;
            }
        }


        /* ========================================================= */
        /* MODALES ULTRA-RESPONSIVOS MÓVILES PARA TODA LA APLICACIÓN  */
        /* ========================================================= */
        .modal-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100vh !important;
            z-index: 9999 !important;
            display: none;
            justify-content: center !important;
            align-items: center !important;
            padding: 16px !important;
            box-sizing: border-box !important;
            overflow-y: auto !important;
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


            /* Agenda card header search input responsive fix */
            .card-header input.form-control {
                min-width: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }
            .card-header div[style*="width:220px"],
            .card-header div[style*="width: 220px"] {
                width: auto !important;
                flex: 1 !important;
                min-width: 80px !important;
                max-width: 200px !important;
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


        /* ========================================================= */
                /* ================================================================ */
        /* PREVENCION GLOBAL DE DESBORDAMIENTO HORIZONTAL EN MOVIL       */
        /* ================================================================ */

        /* Global overflow prevention */
        html, body { max-width: 100%; overflow-x: hidden; }

        @media (max-width: 991px) {
            .main {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                /* No overflow-x:hidden - it clips position:fixed modals */
            }
            .page-content {
                padding: 16px !important;
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
                overflow-x: clip !important;
            }
            .kpi-grid,
            /* Cards must never exceed viewport on mobile */
            .card {
                max-width: 100% !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            #kpi-cards-container {
                grid-template-columns: 1fr 1fr !important;
                gap: 10px !important;
                overflow: hidden !important;
                margin-bottom: 16px !important;
            }
            .kpi-card {
                min-width: 0 !important;
                box-sizing: border-box !important;
                padding: 12px 14px !important;
            }
            .kpi-value { font-size: 20px !important; }
            .kpi-label { font-size: 11px !important; }
        }

        @media (max-width: 600px) {
            .topbar {
                height: 60px !important;
                padding: 0 10px !important;
                gap: 6px !important;
                overflow: hidden !important;
                flex-wrap: nowrap !important;
            }
            .topbar-title {
                flex: 1 !important;
                font-size: 14px !important;
                max-width: none !important;
                min-width: 0 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            .topbar-actions {
                gap: 4px !important;
                flex-shrink: 0 !important;
            }
            .topbar-actions .btn {
                padding: 5px 9px !important;
                font-size: 11.5px !important;
                white-space: nowrap !important;
                border-radius: 6px !important;
            }
            .page-content {
                padding: 12px !important;
            }
        }


        /* TOPBAR ACTIONS RESPONSIVIDAD (SIN DESBORDAMIENTO EN MÓVIL) */
        .topbar-actions {
            flex-shrink: 0;
            display: flex;
            gap: 6px;
            align-items: center;
            max-width: 100%;
        }

        @media (max-width: 991px) {
            .topbar {
                overflow-x: hidden !important;
                padding: 0 10px !important;
            }
            .topbar-actions {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                white-space: nowrap !important;
                padding-bottom: 2px !important;
                -ms-overflow-style: none !important;
                scrollbar-width: none !important;
            }
            .topbar-actions::-webkit-scrollbar {
                display: none !important;
            }
        }
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
            @php
                $userRol = auth()->check() ? auth()->user()->Rol : '';
                $normRol = \App\Helpers\Permisos::normalizar($userRol);
            @endphp

            {{-- Rol Proveedor / Vendedor --}}
            @if($normRol === 'Proveedor' || $normRol === 'Vendedor')
                <li class="nav-section-title">Escáner</li>
                <li class="nav-item">
                    <a href="{{ route('proveedor.index') }}" class="nav-link {{ request()->routeIs('proveedor.*') ? 'active' : '' }}">
                        <i class="bi bi-qr-code-scan"></i> Escanear QR
                    </a>
                </li>

            {{-- Rol Evento --}}
            @elseif($normRol === 'Evento')
                <li class="nav-section-title">Asistencia</li>
                <li class="nav-item">
                    <a href="{{ route('eventos.show', auth()->user()->ID_Evento ?? 0) }}" class="nav-link active">
                        <i class="bi bi-calendar-event"></i> Mi Evento
                    </a>
                </li>

            {{-- Rol Admin / Gerente / Roles con permisos dinámicos --}}
            @else
                @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'dashboard'))
                    <li class="nav-section-title">Principal</li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="bi bi-grid-1x2"></i> Dashboard
                        </a>
                    </li>
                @endif

                @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'eventos') || \App\Helpers\Permisos::tieneAcceso($userRol, 'participantes'))
                    <li class="nav-section-title" style="margin-top:8px">Gestión</li>
                    @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'eventos'))
                        <li class="nav-item">
                            <a href="{{ route('eventos.index') }}" class="nav-link {{ request()->routeIs('eventos.*') ? 'active' : '' }}">
                                <i class="bi bi-calendar-event"></i> Eventos
                            </a>
                        </li>
                    @endif
                    @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'participantes'))
                        <li class="nav-item">
                            <a href="{{ route('participantes.index') }}" class="nav-link {{ request()->routeIs('participantes.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i> Participantes
                            </a>
                        </li>
                    @endif
                @endif

                @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'proveedores'))
                    <li class="nav-section-title" style="margin-top:8px">Comercial</li>
                    <li class="nav-item">
                        <a href="{{ route('proveedores.gestion') }}" class="nav-link {{ request()->routeIs('proveedores.gestion') ? 'active' : '' }}">
                            <i class="bi bi-building"></i> Proveedores
                        </a>
                    </li>
                @endif

                @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'ubicaciones') || \App\Helpers\Permisos::tieneAcceso($userRol, 'usuarios') || \App\Helpers\Permisos::tieneAcceso($userRol, 'roles'))
                    <li class="nav-section-title" style="margin-top:8px">Configuración</li>
                    @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'ubicaciones'))
                        <li class="nav-item">
                            <a href="{{ route('ubicaciones.index') }}" class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}">
                                <i class="bi bi-geo-alt"></i> Ubicaciones
                            </a>
                        </li>
                    @endif
                    @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'usuarios'))
                        <li class="nav-item">
                            <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i> Usuarios
                            </a>
                        </li>
                    @endif
                    @if(\App\Helpers\Permisos::tieneAcceso($userRol, 'roles'))
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="bi bi-shield-lock"></i> Roles & Permisos
                            </a>
                        </li>
                    @endif
                    @if($normRol === 'Admin')
                        <li class="nav-item">
                            <a href="{{ route('apariencia.index') }}" class="nav-link {{ request()->routeIs('apariencia.*') ? 'active' : '' }}">
                                <i class="bi bi-palette"></i> Apariencia CSS
                            </a>
                        </li>
                    @endif
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
<div id="sidebar-overlay" onclick="toggleMobileSidebar(false)" class="sidebar-overlay"></div>
<div class="main">
    <!-- TOPBAR -->
    <div class="topbar" style="gap: 16px;">
        <button type="button" id="sidebar-toggle-btn" onclick="window.toggleMobileSidebar()" title="Abrir Menú" aria-label="Abrir Menú"><i class="bi bi-list"></i></button>
        <div class="topbar-title" style="flex-shrink: 0; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="@yield('page-title')">@yield('page-title', 'Dashboard')</div>
        <div style="flex: 1; min-width: 0; display: flex; justify-content: center; align-items: center; height: 100%;">
            @yield('topbar-center')
        </div>
        <div class="topbar-actions" style="flex-shrink: 0; display: flex; gap: 8px; align-items: center;">
            <button type="button" id="theme-toggle-btn" onclick="toggleAppTheme()" class="btn-icon-theme" title="Cambiar Tema" aria-label="Cambiar Tema">
                <i id="theme-toggle-icon" class="bi bi-moon-fill"></i>
            </button>
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

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('{{ asset("sw.js") }}')
                .then(function(reg) {
                    console.log('Ascencio Connect PWA ServiceWorker registrado:', reg.scope);
                })
                .catch(function(err) {
                    console.log('Error ServiceWorker:', err);
                });
        });
    }
</script>


<script>
    window.toggleMobileSidebar = function(forceState) {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        if (!sidebar || !overlay) return;

        const isOpen = sidebar.classList.contains('open');
        const nextState = forceState !== undefined ? forceState : !isOpen;

        if (nextState) {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Auto-close drawer on mobile when clicking nav links
    document.addEventListener('DOMContentLoaded', function() {
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1600) {
                    window.window.toggleMobileSidebar(false);
                }
            });
        });
    });
</script>

</body>
</html>
