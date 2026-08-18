<?php $config = \App\Models\Apariencia::getConfig(); ?>
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
    <title>Ascencio Connect — Iniciar Sesión</title>
    <meta name="description" content="Sistema de gestión de congresos de Grupo Ascencio">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-color: #020617;
        }

        * { margin:0; padding:0; box-sizing:border-box; user-select: none; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            overflow: hidden;
            height: 100vh;
            width: 100vw;
            display: flex;
            background-image: linear-gradient(to left, {{ $config['fade_gradient_start'] }} 0%, {{ $config['fade_gradient_end'] }} 80%, {{ $config['fade_gradient_end'] }} 100%);
        }

        /* ── TREE BACKGROUND ── */
        .bg-tree {
            position: fixed;
            top: 0;
            left: -5vw; /* Acercar el árbol hacia el centro */
            width: 70vw; /* Ajustar el área */
            height: 100vh;
            z-index: 0;
            cursor: pointer;
            opacity: 0.45; /* Árbol más oscuro en escritorio */
            transition: opacity 0.3s;
        }
        @media (max-width: 900px) {
            .bg-tree {
                left: 0;
                width: 100vw;
                opacity: 0.3; /* Fade on mobile so form is readable */
            }
        }

        #treeCanvas {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 30px rgba(37, 99, 235, 0.15));
        }

        /* ── FOREGROUND LAYOUT ── */
        .layout-container {
            position: relative;
            z-index: 10;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center; /* Agrupa el logo y la tarjeta en el centro */
            align-items: center;
            gap: 16vw; /* Distancia aumentada para empujar la tarjeta a la derecha */
            pointer-events: none; /* Let clicks pass through to canvas */
        }
        @media (max-width: 900px) {
            .layout-container {
                flex-direction: column;
                gap: 40px;
                padding: 20px;
            }
        }

        /* Contenedor frontal para tu logo grande */
        .brand-showcase {
            display: flex;
            justify-content: center;
            align-items: center;
            pointer-events: auto; /* Para que el logo pueda ser un enlace si lo deseas */
            opacity: 0;
            animation: fadeInLogo 1.2s ease-out 0.3s forwards;
        }
        
        @keyframes fadeInLogo {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        @media (max-width: 900px) {
            .brand-showcase {
                display: flex; 
                margin-top: 30px;
            }
        }

        /* ── LOGIN CARD ── */
        .login-card {
            pointer-events: auto; /* Enable clicks in card */
            background: #ffffff;
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255,255,255,0.1);
            animation: slideUp .6s ease forwards;
            position: relative;
            overflow: hidden;
        }

        /* Gradient accent line on the left */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 8px; height: 100%;
            background: linear-gradient(180deg, #f97316, #2563eb);
        }

        @keyframes slideUp {
            from { opacity:0; transform: translateY(24px); }
            to   { opacity:1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 36px;
        }
        .logo-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #f97316, #c2410c);
            border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 28px; color: #fff;
            margin-bottom: 16px;
            box-shadow: 0 8px 24px rgba(249,115,22,.3);
        }
        .logo-title {
            font-size: 24px; font-weight: 800;
            color: #0f172a;
            letter-spacing: -.5px;
        }
        .logo-subtitle {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .form-group { margin-bottom: 20px; text-align: left; }
        .form-label {
            display: block;
            font-size: 11px; font-weight: 700;
            color: #475569;
            margin-bottom: 8px;
            letter-spacing: .8px;
            text-transform: uppercase;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 14px; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: color .2s;
        }
        .form-control {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #0f172a;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            background: #ffffff;
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,.15);
        }
        .form-control:focus + .input-icon { color: #f97316; }
        .form-control::placeholder { color: #94a3b8; }

        .toggle-pass {
            position: absolute; right: 14px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; font-size: 16px;
            cursor: pointer; padding: 0;
            transition: color .2s;
        }
        .toggle-pass:hover { color: #f97316; }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #f97316, #ea580c);
            border: none;
            border-radius: 30px; /* Pill shape */
            color: #fff;
            font-size: 15px; font-weight: 700;
            padding: 14px;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: .3px;
            transition: all .2s;
            margin-top: 8px;
            box-shadow: 0 8px 20px rgba(249,115,22,.25);
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(249,115,22,.35);
        }
        .btn-login:active { transform: translateY(0); }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 11px;
            color: #64748b;
        }
        .login-footer span { color: #2563eb; font-weight: 600; }
        
        
        
        .app-brand-logo {
            max-height: 85px;
            width: auto;
            margin-bottom: 12px;
            filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.15));
            transition: transform 0.3s ease;
        }
        .app-brand-logo:hover {
            transform: scale(1.04);
        }

        
        .app-brand-logo {
            max-height: 70px;
            width: auto;
            margin-bottom: 8px;
            filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.15));
        }

        
        /* --- ESTILO FUTURISTA DARK GLASSMOPRHISM PARA LOGIN MÓVIL --- */
        @media (max-width: 900px) {
            body {
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: auto !important;
                min-height: 100vh !important;
                background: radial-gradient(circle at 15% 15%, rgba(249, 115, 22, 0.4) 0%, transparent 45%),
                            radial-gradient(circle at 85% 85%, rgba(37, 99, 235, 0.4) 0%, transparent 50%),
                            radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, #020617 100%) !important;
                background-attachment: fixed !important;
            }

            .bg-tree {
                display: none !important; /* Oculta árbol en móvil para el fondo glassmorphic líquido */
            }

            .brand-showcase {
                display: none !important;
            }

            .layout-container {
                flex-direction: column !important;
                gap: 0 !important;
                padding: 20px 14px !important;
                height: auto !important;
                min-height: 100vh !important;
                justify-content: center !important;
                align-items: center !important;
            }

            /* TARJETA DE CRISTAL OSCURO TRASLÚCIDO */
            .login-card {
                padding: 38px 24px 30px !important;
                border-radius: 28px !important;
                max-width: 390px !important;
                width: 94% !important;
                background: rgba(15, 23, 42, 0.88) !important; backdrop-filter: blur(10px) !important; -webkit-backdrop-filter: blur(10px) !important; will-change: transform; transform: translateZ(0);
                border: 1px solid rgba(255, 255, 255, 0.18) !important;
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
                margin: auto !important;
            }

            .login-card::before {
                display: none !important;
            }

            /* LOGO Y TIPOGRAFÍA BLANCA CRISTALINA */
            .app-brand-logo {
                max-height: 95px !important;
                margin-bottom: 12px !important;
                filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.4)) !important;
            }

            .login-logo {
                margin-bottom: 24px !important;
            }

            .logo-title {
                font-size: 22px !important;
                font-weight: 800 !important;
                color: #ffffff !important;
                letter-spacing: -0.3px !important;
            }

            .logo-subtitle {
                color: rgba(255, 255, 255, 0.7) !important;
                font-size: 11px !important;
                letter-spacing: 2px !important;
                text-transform: uppercase !important;
            }

            /* ETIQUETAS Y CAMPOS PILL EN MÓVIL */
            .form-label {
                color: rgba(255, 255, 255, 0.85) !important;
                font-size: 11px !important;
                letter-spacing: 1px !important;
            }

            .form-group {
                margin-bottom: 18px !important;
            }

            .form-control {
                background: rgba(255, 255, 255, 0.08) !important;
                border: 1px solid rgba(255, 255, 255, 0.22) !important;
                border-radius: 99px !important; /* Forma redondeada tipo Pill */
                color: #ffffff !important;
                padding: 13px 18px 13px 44px !important;
                font-size: 16px !important;
                box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2) !important;
            }

            .form-control:focus {
                background: rgba(255, 255, 255, 0.14) !important;
                border-color: #f97316 !important;
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.3) !important;
            }

            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.45) !important;
            }

            .input-icon {
                color: rgba(255, 255, 255, 0.75) !important;
            }

            .toggle-pass {
                color: rgba(255, 255, 255, 0.75) !important;
                right: 14px !important;
            }

            .toggle-pass:hover {
                color: #f97316 !important;
            }

            /* BOTÓN INGRESAR TIPO PILL NARANJA INTENSO */
            .btn-login {
                background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
                color: #ffffff !important;
                border-radius: 99px !important; /* Forma Pill */
                padding: 14px !important;
                font-size: 16px !important;
                font-weight: 800 !important;
                letter-spacing: 0.5px !important;
                box-shadow: 0 10px 25px rgba(249, 115, 22, 0.45) !important;
                margin-top: 6px !important;
            }

            .login-footer {
                color: rgba(255, 255, 255, 0.6) !important;
                margin-top: 24px !important;
            }

            .login-footer span {
                color: #f97316 !important;
                font-weight: 700 !important;
            }

            /* BOTÓN PWA EN MÓVIL */
            #pwaInstallWrap {
                margin-top: 14px !important;
            }

            #btnPwaInstall {
                background: rgba(255, 255, 255, 0.1) !important;
                border: 1px solid rgba(255, 255, 255, 0.25) !important;
                color: #ffffff !important;
                border-radius: 99px !important;
                padding: 10px 18px !important;
            }
        }


        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

@if($config['fondo_login'] === 'arbol' || $config['fondo_login'] === 'particulas')
    <div id="app-container" class="bg-tree">
        <canvas id="treeCanvas"></canvas>
    </div>
@endif

<!-- TARJETA FLOTANTE Y LOGO FRONTAL -->
<div class="layout-container">
    
    <!-- Espacio para tu Logo Frontal -->
    <div class="brand-showcase">
        <!-- Reemplaza la siguiente línea con tu imagen de logo real -->
        <img src="{{ asset($config['logo_path']) }}" alt="Logo Frontal" style="max-width: 350px; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.5));">
    </div>

    <div class="login-card">
        <div class="login-logo">
            <img src="{{ asset($config['logo_path']) }}" alt="Conexión Ascencio" class="app-brand-logo">
            <div class="logo-title">Ascencio Connect</div>
            <div class="logo-subtitle">Sistema de Congresos</div>
        </div>

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            @if($errors->has('username'))
                <div class="alert" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ $errors->first('username') }}
                </div>
            @endif

            <div class="form-group">
                <label class="form-label" for="username">Usuario</label>
                <div class="input-wrap">
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Ingresa tu usuario"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        autofocus
                    >
                    <i class="bi bi-person input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <div class="input-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Ingresa tu contraseña"
                        autocomplete="current-password"
                    >
                    <i class="bi bi-lock input-icon"></i>
                    <button type="button" class="toggle-pass" id="togglePass" title="Mostrar contraseña">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
                <i class="bi bi-box-arrow-in-right" style="margin-right:6px"></i>
                Iniciar Sesión
            </button>
        </form>

        
        <div id="pwaInstallWrap" style="display: none; text-align: center; margin-top: 18px;">
            <button type="button" id="btnPwaInstall" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(59, 130, 246, 0.22)); border: 1px solid rgba(59, 130, 246, 0.4); color: #2563eb; border-radius: 20px; padding: 10px 20px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);">
                <i class="bi bi-download" style="font-size: 15px;"></i> Instalar Aplicación Móvil
            </button>
        </div>

        <div class="login-footer">
            Grupo Ascencio &copy; {{ date('Y') }} &mdash; <span>Congresos</span>
        </div>
    </div>
</div>

<script>
    // --- LÓGICA DEL FORMULARIO DE LOGIN ---
    const toggle = document.getElementById('togglePass');
    const pass   = document.getElementById('password');
    const eye    = document.getElementById('eyeIcon');
    if(toggle) {
        toggle.addEventListener('click', () => {
            const show = pass.type === 'password';
            pass.type  = show ? 'text' : 'password';
            eye.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }

    const loginForm = document.getElementById('loginForm');
    if(loginForm) {
        loginForm.addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '<i class="bi bi-arrow-repeat" style="margin-right:6px;animation:spin .8s linear infinite"></i>Autenticando...';
            btn.disabled = true;
        });
    }

    // --- LÓGICA DEL ÁRBOL DE ENERGÍA ---
    (() => {
        const canvas = document.getElementById('treeCanvas');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');

        const bgCanvas = document.createElement('canvas');
        const bgCtx = bgCanvas.getContext('2d');

        const COLORS = {
            orange: '{{ $config['color_primario'] }}', orangeDark: '{{ $config['color_primario'] }}', orangeLight: '{{ $config['color_primario'] }}',
            blue: '{{ $config['color_secundario'] }}', blueDark: '{{ $config['color_secundario'] }}', blueLight: '{{ $config['color_secundario'] }}',
            sky: '{{ $config['color_secundario'] }}', skyLight: '{{ $config['color_secundario'] }}',
            glowOrange: '{{ str_replace('rgb', 'rgba', str_replace(')', ', 0.35)', $config['color_primario'])) }}', glowBlue: '{{ str_replace('rgb', 'rgba', str_replace(')', ', 0.35)', $config['color_secundario'])) }}',
            bgSolid: '#020617'
        };

        const spriteCache = { orbs: { orange: [], blue: [] }, leaves: { orange: null, blue: null } };

        let branches = [], leaves = [], particles = [];
        let globalTime = 0;
        let treeCompleted = false;
        let swing = null;
        const size = 1000;

        const ICON_DESIGNS = [
            (ctx, x, y, r) => { ctx.lineWidth = 1.3; ctx.beginPath(); ctx.moveTo(x + r*0.08, y - r*0.45); ctx.lineTo(x - r*0.22, y + r*0.05); ctx.lineTo(x + r*0.05, y + r*0.05); ctx.lineTo(x - r*0.1, y + r*0.45); ctx.lineTo(x + r*0.22, y - r*0.05); ctx.lineTo(x - r*0.05, y - r*0.05); ctx.closePath(); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); },
            (ctx, x, y, r) => { ctx.lineWidth = 1.2; ctx.beginPath(); ctx.arc(x, y - r*0.08, r*0.28, 0.7 * Math.PI, 0.3 * Math.PI, true); ctx.lineTo(x + r*0.13, y + r*0.18); ctx.lineTo(x - r*0.13, y + r*0.18); ctx.closePath(); ctx.stroke(); ctx.beginPath(); ctx.moveTo(x - r*0.08, y - r*0.02); ctx.lineTo(x - r*0.04, y - r*0.16); ctx.lineTo(x + r*0.04, y - r*0.16); ctx.lineTo(x + r*0.08, y - r*0.02); ctx.stroke(); ctx.beginPath(); ctx.moveTo(x - r*0.1, y + r*0.18); ctx.lineTo(x + r*0.1, y + r*0.18); ctx.moveTo(x - r*0.08, y + r*0.24); ctx.lineTo(x + r*0.08, y + r*0.24); ctx.moveTo(x - r*0.05, y + r*0.3); ctx.lineTo(x + r*0.05, y + r*0.3); ctx.stroke(); },
            (ctx, x, y, r) => { ctx.lineWidth = 1.2; ctx.save(); ctx.translate(x, y); ctx.rotate(-Math.PI / 4); ctx.beginPath(); ctx.rect(-r*0.12, -r*0.42, r*0.24, r*0.3); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); ctx.beginPath(); ctx.rect(-r*0.07, -r*0.12, r*0.14, r*0.04); ctx.stroke(); ctx.beginPath(); ctx.rect(-r*0.03, -r*0.08, r*0.06, r*0.38); ctx.fill(); ctx.stroke(); ctx.beginPath(); ctx.moveTo(-r*0.05, r*0.3); ctx.lineTo(r*0.05, r*0.3); ctx.lineTo(r*0.02, r*0.42); ctx.lineTo(-r*0.02, r*0.42); ctx.closePath(); ctx.fill(); ctx.stroke(); ctx.restore(); },
            (ctx, x, y, r) => { ctx.lineWidth = 1.2; ctx.save(); ctx.translate(x, y); ctx.rotate(Math.PI / 6); ctx.beginPath(); ctx.moveTo(-r*0.08, -r*0.1); ctx.quadraticCurveTo(-r*0.15, -r*0.32, -r*0.05, -r*0.35); ctx.lineTo(0, -r*0.16); ctx.quadraticCurveTo(r*0.15, -r*0.32, r*0.05, -r*0.35); ctx.lineTo(r*0.08, -r*0.1); ctx.closePath(); ctx.stroke(); ctx.beginPath(); ctx.arc(0, -r*0.05, r*0.06, 0, Math.PI*2); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); ctx.beginPath(); ctx.moveTo(-r*0.05, 0); ctx.quadraticCurveTo(-r*0.15, r*0.2, -r*0.12, r*0.42); ctx.moveTo(r*0.05, 0); ctx.quadraticCurveTo(r*0.15, r*0.2, r*0.12, r*0.42); ctx.stroke(); ctx.restore(); },
            (ctx, x, y, r) => { ctx.lineWidth = 1.2; ctx.save(); ctx.translate(x, y); ctx.rotate(Math.PI / 4); ctx.beginPath(); ctx.rect(-r*0.2, -r*0.1, r*0.4, r*0.25); ctx.stroke(); ctx.beginPath(); ctx.rect(-r*0.12, -r*0.3, r*0.05, r*0.2); ctx.rect(r*0.07, -r*0.3, r*0.05, r*0.2); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); ctx.beginPath(); ctx.moveTo(0, r*0.15); ctx.quadraticCurveTo(-r*0.1, r*0.28, 0, r*0.42); ctx.stroke(); ctx.restore(); },
            (ctx, x, y, r) => { ctx.lineWidth = 1.2; ctx.save(); ctx.translate(x, y); ctx.rotate(-Math.PI / 3); ctx.beginPath(); ctx.rect(-r*0.05, -r*0.15, r*0.1, r*0.5); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); ctx.beginPath(); ctx.arc(0, r*0.35, r*0.08, 0, Math.PI*2); ctx.stroke(); ctx.beginPath(); ctx.arc(0, -r*0.15, r*0.22, 0.15*Math.PI, 0.85*Math.PI, true); ctx.lineTo(-r*0.08, -r*0.15); ctx.lineTo(-r*0.08, -r*0.05); ctx.lineTo(r*0.08, -r*0.05); ctx.lineTo(r*0.08, -r*0.15); ctx.closePath(); ctx.fillStyle = '#ffffff'; ctx.fill(); ctx.stroke(); ctx.restore(); }
        ];

        function preRenderSprites() {
            const sizeSprite = 64; 
            const center = sizeSprite / 2;
            const r = 24; 
            const colors = ['orange', 'blue'];
            
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = sizeSprite;
            tempCanvas.height = sizeSprite;
            const tCtx = tempCanvas.getContext('2d');

            colors.forEach(col => {
                for (let i = 0; i < ICON_DESIGNS.length; i++) {
                    tCtx.clearRect(0,0,sizeSprite,sizeSprite);
                    
                    tCtx.shadowColor = col === 'orange' ? COLORS.orange : COLORS.blue;
                    tCtx.shadowBlur = 8;

                    const grad = tCtx.createRadialGradient(center - r * 0.25, center - r * 0.25, r * 0.08, center, center, r);
                    if (col === 'orange') {
                        grad.addColorStop(0, '#fedebe'); grad.addColorStop(0.55, COLORS.orange); grad.addColorStop(1, '#6a2002');
                    } else {
                        grad.addColorStop(0, '#dbeafe'); grad.addColorStop(0.55, COLORS.blue); grad.addColorStop(1, '#0f172a');
                    }

                    tCtx.beginPath();
                    tCtx.arc(center, center, r, 0, Math.PI * 2);
                    tCtx.fillStyle = grad;
                    tCtx.fill();

                    tCtx.shadowBlur = 0;
                    tCtx.beginPath();
                    tCtx.arc(center, center, r, 0, Math.PI * 2);
                    tCtx.strokeStyle = col === 'orange' ? COLORS.orangeLight : COLORS.blueLight;
                    tCtx.lineWidth = 1;
                    tCtx.stroke();

                    tCtx.beginPath();
                    tCtx.ellipse(center - r * 0.35, center - r * 0.35, r * 0.38, r * 0.18, Math.PI / 4, 0, Math.PI * 2);
                    tCtx.fillStyle = 'rgba(255, 255, 255, 0.35)';
                    tCtx.fill();

                    tCtx.strokeStyle = '#ffffff';
                    tCtx.shadowColor = 'rgba(255, 255, 255, 0.6)';
                    tCtx.shadowBlur = 3;
                    ICON_DESIGNS[i](tCtx, center, center, r);

                    const finalSprite = document.createElement('canvas');
                    finalSprite.width = sizeSprite;
                    finalSprite.height = sizeSprite;
                    finalSprite.getContext('2d').drawImage(tempCanvas, 0, 0);
                    spriteCache.orbs[col].push(finalSprite);
                }

                tCtx.clearRect(0,0,sizeSprite,sizeSprite);
                const leafGrad = tCtx.createLinearGradient(center, center, center + r * 1.5, center);
                if (col === 'orange') {
                    leafGrad.addColorStop(0, COLORS.orange); leafGrad.addColorStop(1, '#7c2d12');
                } else {
                    leafGrad.addColorStop(0, COLORS.sky); leafGrad.addColorStop(1, COLORS.blueDark);
                }

                tCtx.save();
                tCtx.translate(center - r * 0.5, center);
                tCtx.beginPath();
                tCtx.moveTo(0, 0);
                tCtx.quadraticCurveTo(r * 0.7, -r * 0.7, r * 1.5, 0);
                tCtx.quadraticCurveTo(r * 0.7, r * 0.7, 0, 0);
                tCtx.fillStyle = leafGrad;
                tCtx.fill();

                tCtx.beginPath();
                tCtx.moveTo(0, 0);
                tCtx.lineTo(r * 1.15, 0);
                tCtx.strokeStyle = 'rgba(255,255,255,0.3)';
                tCtx.lineWidth = 0.8;
                tCtx.stroke();
                tCtx.restore();

                const finalLeaf = document.createElement('canvas');
                finalLeaf.width = sizeSprite;
                finalLeaf.height = sizeSprite;
                finalLeaf.getContext('2d').drawImage(tempCanvas, 0, 0);
                spriteCache.leaves[col] = finalLeaf;
            });
        }

        function preRenderBackground() {
            bgCanvas.width = size;
            bgCanvas.height = size;

            // bgCtx.fillStyle = COLORS.bgSolid;
            // bgCtx.fillRect(0, 0, size, size);

            const bgGlow = bgCtx.createRadialGradient(size * 0.5, size * 0.55, size * 0.05, size * 0.5, size * 0.55, size * 0.45);
            bgGlow.addColorStop(0, 'rgba(30, 58, 138, 0.12)');
            bgGlow.addColorStop(0.5, 'rgba(234, 88, 12, 0.04)');
            bgGlow.addColorStop(1, 'rgba(2, 6, 23, 0)');
            bgCtx.fillStyle = bgGlow;
            bgCtx.fillRect(0, 0, size, size);

            bgCtx.save();
            const rootGrad = bgCtx.createLinearGradient(size * 0.15, size * 0.95, size * 0.85, size * 0.95);
            rootGrad.addColorStop(0, 'rgba(2,6,23,0)');
            rootGrad.addColorStop(0.3, COLORS.orangeDark);
            rootGrad.addColorStop(0.5, '#334155');
            rootGrad.addColorStop(0.7, COLORS.blueDark);
            rootGrad.addColorStop(1, 'rgba(2,6,23,0)');
            
            bgCtx.beginPath();
            bgCtx.ellipse(size * 0.5, size * 0.95, size * 0.4, 15, 0, 0, Math.PI * 2);
            bgCtx.fillStyle = rootGrad;
            bgCtx.fill();
            bgCtx.restore();
        }

        function resizeCanvas() {
            canvas.width = size;
            canvas.height = size;
        }

        class Particle {
            constructor(x, y, color, isAmbient = false) {
                this.init(x, y, color, isAmbient);
            }
            init(x, y, color, isAmbient) {
                this.x = x; this.y = y; this.color = color; this.isAmbient = isAmbient;
                this.wigglePhase = Math.random() * 100;
                if (isAmbient) {
                    this.vx = (Math.random() - 0.5) * 0.4;
                    this.vy = Math.random() * 0.5 + 0.2; 
                    this.radius = Math.random() * 2 + 1;
                    this.alpha = Math.random() * 0.5 + 0.2;
                    this.decay = Math.random() * 0.002 + 0.001;
                } else {
                    this.vx = (Math.random() - 0.5) * 2;
                    this.vy = (Math.random() - 1.2) * 2;
                    this.radius = Math.random() * 3 + 1.5;
                    this.alpha = 1;
                    this.decay = Math.random() * 0.015 + 0.008; 
                }
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                if (this.isAmbient) this.x += Math.sin(globalTime * 0.02 + this.wigglePhase) * 0.15;
                this.alpha -= this.decay;
            }
            draw(ctx) {
                ctx.save();
                ctx.globalAlpha = Math.max(0, this.alpha);
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fill();
                ctx.restore();
            }
        }

        class Branch {
            constructor(startX, startY, endX, endY, thickness, colorType, parentAngle = -Math.PI / 2, generation = 0) {
                this.startX = startX; this.startY = startY;
                this.endX = endX; this.endY = endY;
                this.thickness = thickness; this.colorType = colorType;
                this.generation = generation; this.parentAngle = parentAngle;
                this.progress = 0;
                this.speed = 0.022 + (6 - Math.min(generation, 5)) * 0.004; 
                this.complete = false;

                const distToEnd = Math.hypot(endX - startX, endY - startY);
                const cpDist = distToEnd * 0.45; 
                this.cpX = startX + Math.cos(parentAngle) * cpDist;
                this.cpY = startY + Math.sin(parentAngle) * cpDist;

                this.endThicknessRatio = this.generation === 0 ? 0.80 : 0.65;
                this.windFlexibility = Math.pow(this.generation, 1.6) * 0.85;
                this.windPhase = Math.random() * Math.PI * 2;
                
                this.shadowColor = this.colorType === 'orange' ? COLORS.glowOrange : COLORS.glowBlue;
                this.shadowBlurVal = this.generation < 2 ? 8 : 3;
            }
            getPositionAt(t) {
                const tInv = 1 - t; const tInv2 = tInv * tInv; const t2 = t * t;
                let x = tInv2 * this.startX + 2 * tInv * t * this.cpX + t2 * this.endX;
                let y = tInv2 * this.startY + 2 * tInv * t * this.cpY + t2 * this.endY;
                const windStrength = Math.sin(globalTime * 0.018 + this.windPhase) * this.windFlexibility;
                return { x: x + windStrength * t, y: y };
            }
            update() {
                if (this.progress < 1) {
                    this.progress += this.speed;
                    if (this.progress >= 1) { this.progress = 1; this.complete = true; this.spawnNext(); }
                    
                    if (Math.random() < 0.12 && particles.length < 150) {
                        const currentPos = this.getPositionAt(this.progress);
                        const color = this.colorType === 'orange' ? COLORS.orange : COLORS.sky;
                        particles.push(new Particle(currentPos.x, currentPos.y, color));
                    }
                }
            }
            draw(ctx) {
                if (this.progress <= 0.01) return;
                ctx.save();
                const startGlow = this.getPositionAt(0);
                const endGlow = this.getPositionAt(this.progress);
                const grad = ctx.createLinearGradient(startGlow.x, startGlow.y, endGlow.x, endGlow.y);
                
                if (this.colorType === 'orange') {
                    grad.addColorStop(0, COLORS.orangeDark); grad.addColorStop(0.5, COLORS.orange); grad.addColorStop(1, '#ff9e59');
                } else {
                    grad.addColorStop(0, COLORS.blueDark); grad.addColorStop(0.5, COLORS.blue); grad.addColorStop(1, COLORS.sky);
                }

                ctx.beginPath();
                ctx.moveTo(this.startX, this.startY);
                const steps = Math.max(3, Math.ceil(this.progress * (12 - this.generation)));
                for (let i = 1; i <= steps; i++) {
                    const t = (i / steps) * this.progress;
                    const pos = this.getPositionAt(t);
                    ctx.lineTo(pos.x, pos.y);
                }

                ctx.strokeStyle = grad;
                ctx.lineWidth = this.thickness * (1 - this.progress * (1 - this.endThicknessRatio));
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';
                ctx.shadowColor = this.shadowColor;
                ctx.shadowBlur = this.shadowBlurVal;
                ctx.stroke();
                ctx.restore();
            }
            spawnNext() {
                if (this.generation >= 6 || this.thickness < 2) { this.createFoliage(); return; }

                const nextGen = this.generation + 1;
                const finalTangent = Math.atan2(this.endY - this.cpY, this.endX - this.cpX);
                const currentEndThickness = this.thickness * this.endThicknessRatio;

                let branchCount = (this.generation === 0 || (this.generation < 4 && Math.random() > 0.45)) ? 3 : 2;
                let baseLen = (size * 0.115) * Math.pow(0.75, this.generation);

                let spreads = [];
                if (branchCount === 3) {
                    spreads = [-0.5 - Math.random() * 0.15, (Math.random() - 0.5) * 0.2, 0.5 + Math.random() * 0.15];
                } else {
                    spreads = [-0.4 - Math.random() * 0.15, 0.4 + Math.random() * 0.15];
                }

                for (let i = 0; i < branchCount; i++) {
                    let destAngle = finalTangent + spreads[i];
                    destAngle = Math.atan2(Math.sin(destAngle), Math.cos(destAngle));
                    if (destAngle >= 0) {
                        if (destAngle < Math.PI / 2) destAngle = -0.15; 
                        else destAngle = -Math.PI + 0.15; 
                    }

                    let finalLen = baseLen * (0.8 + Math.random() * 0.4);
                    if (this.generation > 0) { 
                         const targetAngle = -Math.PI / 2;
                         destAngle = destAngle * 0.75 + targetAngle * 0.25;
                    }

                    const nextEndX = this.endX + Math.cos(destAngle) * finalLen;
                    const nextEndY = this.endY + Math.sin(destAngle) * finalLen;
                    
                    let nextThickness = currentEndThickness * (branchCount === 3 ? 0.75 : 0.85);
                    if (branchCount === 3 && i === 1) nextThickness = currentEndThickness * 0.95; 

                    const nextColor = (Math.random() > 0.6) ? this.colorType : (this.colorType === 'orange' ? 'blue' : 'orange');
                    branches.push(new Branch(this.endX, this.endY, nextEndX, nextEndY, nextThickness, nextColor, finalTangent, nextGen));
                }

                if (this.generation >= 4 && Math.random() > 0.3) this.createFoliage();
            }
            createFoliage() {
                const count = Math.floor(Math.random() * 2) + 1; 
                for (let i = 0; i < count; i++) {
                    const angle = -Math.PI/2 + (Math.random() - 0.5) * Math.PI * 1.1; 
                    const dist = Math.sqrt(Math.random()) * 55; 

                    let leafX = this.endX + Math.cos(angle) * dist * 1.15; 
                    let leafY = this.endY + Math.sin(angle) * dist * 0.85; 

                    const finalRadius = Math.random() * 14 + 10; 
                    const isIconOrb = Math.random() > 0.15; 
                    const colorType = Math.random() > 0.5 ? 'orange' : 'blue';
                    leaves.push(new Leaf(leafX, leafY, finalRadius, colorType, isIconOrb, this.windFlexibility, this.windPhase));
                }
            }
        }

        class Leaf {
            constructor(x, y, maxRadius, colorType, isIconOrb, windFlexibility, windPhase) {
                this.x = x; this.y = y; this.maxRadius = maxRadius;
                this.colorType = colorType; this.isIconOrb = isIconOrb;
                this.radius = 0;
                this.growthSpeed = 0.25 + Math.random() * 0.25;
                this.iconScale = 0;
                this.iconIndex = Math.floor(Math.random() * ICON_DESIGNS.length);
                this.wiggleOffset = Math.random() * 100;
                this.rotation = Math.random() * Math.PI * 2;
                this.windFlexibility = windFlexibility;
                this.windPhase = windPhase;
                this.sprite = this.isIconOrb ? spriteCache.orbs[this.colorType][this.iconIndex] : spriteCache.leaves[this.colorType];
                this.spriteOffsetX = this.isIconOrb ? -32 : -16;
                this.spriteOffsetY = -32;
            }
            update() {
                if (this.radius < this.maxRadius) {
                    this.radius += this.growthSpeed;
                    if (this.radius >= this.maxRadius) this.radius = this.maxRadius;
                } else if (this.iconScale < 1) {
                    this.iconScale += 0.04;
                }
            }
            draw(ctx) {
                if (this.radius <= 0.1) return;
                const windOsc = Math.sin(globalTime * 0.018 + this.windPhase) * this.windFlexibility;
                const currentX = this.x + windOsc + Math.sin(globalTime * 0.04 + this.wiggleOffset) * 0.54; 
                const currentY = this.y + Math.abs(windOsc) * 0.2;

                ctx.save();
                ctx.translate(currentX, currentY);
                
                let scaleFactor = this.radius / 24;
                if (this.isIconOrb) {
                    scaleFactor *= this.iconScale;
                } else {
                    ctx.rotate(this.rotation + windOsc * 0.015);
                }

                if (scaleFactor > 0.01) {
                     ctx.scale(scaleFactor, scaleFactor);
                     ctx.drawImage(this.sprite, this.spriteOffsetX, this.spriteOffsetY);
                }
               
                ctx.restore();
            }
        }

        class Swing {
            constructor(branch, t) {
                this.branch = branch;
                this.t = t;
                this.targetLength = size * 0.24; 
                this.currentLength = 0; 
                this.swingPhase = Math.random() * 100;
            }
            update() {
                if (this.currentLength < this.targetLength) {
                    this.currentLength += 2.2; 
                }
            }
            draw(ctx) {
                if (this.currentLength <= 0.1) return;
                const anchorPos = this.branch.getPositionAt(this.t);
                const swayAngle = Math.sin(globalTime * 0.02 + this.swingPhase) * 0.12;

                ctx.save();
                ctx.translate(anchorPos.x, anchorPos.y);
                ctx.rotate(swayAngle);

                ctx.strokeStyle = COLORS.skyLight;
                ctx.lineWidth = 2.5; 
                ctx.shadowColor = COLORS.glowBlue;
                ctx.shadowBlur = 8;
                
                ctx.beginPath();
                ctx.moveTo(-20, 0); 
                ctx.lineTo(-28, this.currentLength); 
                ctx.moveTo(20, 0);
                ctx.lineTo(28, this.currentLength);
                ctx.stroke();

                if (this.currentLength > 10) {
                    ctx.fillStyle = '#0f172a'; 
                    ctx.fillRect(-35, this.currentLength, 70, 10); 
                    
                    ctx.strokeStyle = COLORS.orange;
                    ctx.lineWidth = 2.5; 
                    ctx.shadowColor = COLORS.glowOrange;
                    ctx.shadowBlur = 12;
                    ctx.strokeRect(-35, this.currentLength, 70, 10);
                }
                ctx.restore();
            }
        }

        function triggerBurst() {
            if (leaves.length === 0 || particles.length > 80) return; 
            const burstAmount = Math.min(15, 80 - particles.length);
            for (let i = 0; i < burstAmount; i++) {
                const randomLeaf = leaves[Math.floor(Math.random() * leaves.length)];
                const col = randomLeaf.colorType === 'orange' ? COLORS.orange : COLORS.skyLight;
                particles.push(new Particle(randomLeaf.x, randomLeaf.y, col));
            }
        }

        function initTree() {
            branches = []; leaves = []; particles = [];
            treeCompleted = false;
            swing = null;
            
            @if($config['fondo_login'] === 'arbol')
            branches.push(new Branch(size * 0.49, size * 0.95, size * 0.56, size * 0.72, 65, 'orange', -Math.PI / 2 + 0.2, 0));
            branches.push(new Branch(size * 0.51, size * 0.95, size * 0.44, size * 0.72, 65, 'blue', -Math.PI / 2 - 0.2, 0));
            @endif
        }

        let animationFrameId = null;

        function animate() {
            if (window.innerWidth <= 900) {
                animationFrameId = null;
                return;
            }
            globalTime++;
            ctx.clearRect(0, 0, size, size);
            ctx.drawImage(bgCanvas, 0, 0);

            if (globalTime % 5 === 0 && particles.length < 30 && Math.random() < 0.1) {
                const randX = Math.random() * size;
                const col = Math.random() > 0.5 ? COLORS.orange : COLORS.blueLight;
                particles.push(new Particle(randX, 50, col, true));
            }

            for (let i = 0; i < branches.length; i++) { branches[i].update(); branches[i].draw(ctx); }
            for (let i = 0; i < leaves.length; i++) { leaves[i].update(); leaves[i].draw(ctx); }
            
            for (let i = particles.length - 1; i >= 0; i--) {
                particles[i].update();
                particles[i].draw(ctx);
                if (particles[i].alpha <= 0) particles.splice(i, 1);
            }

            if (!treeCompleted && branches.length > 30) {
                if (branches.every(b => b.complete)) {
                    treeCompleted = true;
                    let candidates = branches.filter(b => (b.generation === 2 || b.generation === 3) && b.endX > size * 0.55);
                    candidates.sort((a, b) => b.endX - a.endX);
                    if (candidates.length > 0) {
                        swing = new Swing(candidates[0], 0.75);
                    }
                }
            }

            if (swing) {
                swing.update();
                swing.draw(ctx);
            }

            animationFrameId = requestAnimationFrame(animate);
        }

        const appCont = document.getElementById('app-container');
        if (appCont) appCont.addEventListener('click', triggerBurst);

        window.addEventListener('resize', () => {
            if (window.innerWidth <= 900) {
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                    animationFrameId = null;
                }
            } else {
                resizeCanvas();
                if (!animationFrameId) {
                    if (branches.length === 0) {
                        preRenderSprites();
                        preRenderBackground();
                        initTree();
                    }
                    animate();
                }
            }
        });

        if (window.innerWidth > 900) {
            preRenderSprites();
            preRenderBackground();
            resizeCanvas();
            initTree();
            animate();
        }            
    })(); 
</script>

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
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const pwaWrap = document.getElementById('pwaInstallWrap');
        if (pwaWrap) pwaWrap.style.display = 'block';
    });

    document.addEventListener('DOMContentLoaded', function() {
        const btnInstall = document.getElementById('btnPwaInstall');
        if (btnInstall) {
            btnInstall.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('PWA Install choice:', outcome);
                    deferredPrompt = null;
                    document.getElementById('pwaInstallWrap').style.display = 'none';
                }
            });
        }
    });
</script>

</body>
</html>
