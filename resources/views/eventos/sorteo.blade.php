@extends('layouts.app')

@section('title', 'Tï¿½mbola - ' . $evento->name_evento)
@section('page-title', 'Tï¿½mbola - ' . $evento->name_evento)

@section('topbar-actions')
    <a href="{{ route('eventos.show', $evento) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver al Evento
    </a>
@endsection

@push('styles')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            corePlugins: {
                preflight: false,
            }
        }
    </script>
    <!-- Google Fonts: Fredoka para el estilo de interfaz redondeada -->
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .tombola-container {
            font-family: 'Fredoka', sans-serif;
            background-color: transparent;
            cursor: none; /* Se oculta el cursor para usar el puntero personalizado interactivo */
            user-select: none;
            overflow-x: hidden;
            border-radius: 12px;
            min-height: calc(100vh - 120px);
            position: relative;
        }

        /* --- OVERRIDES DE PERSONALIZACIï¿½N DEL SISTEMA --- */
        .tombola-container .bg-white, 
        .tombola-container .bg-white\/80, 
        .tombola-container .bg-white\/90 {
            background-color: var(--bg-card) !important;
            border-color: var(--border) !important;
        }
        /* Mapeo de Colores del Sistema */
        .tombola-container .text-gray-400,
        .tombola-container .text-gray-500,
        .tombola-container .text-gray-600,
        .tombola-container .text-gray-700, 
        .tombola-container .text-gray-800 { color: var(--text-primary) !important; }
        
        .tombola-container .bg-\[\#00a0e9\] { 
            background-color: var(--accent-gold) !important; 
            color: var(--bg-primary) !important;
        }
        .tombola-container .text-\[\#00a0e9\] { color: var(--accent-gold) !important; }
        .tombola-container .border-slate-200, 
        .tombola-container .border-slate-200\/50,
        .tombola-container .border-white { border-color: var(--border-subtle) !important; }
        .tombola-container .bg-gray-100 { background-color: var(--bg-secondary) !important; border-color: var(--border) !important; }
        .tombola-container .from-sky-50 { --tw-gradient-from: var(--bg-secondary) !important; }
        .tombola-container .to-white { --tw-gradient-to: var(--bg-card) !important; }
        
        /* Overrides para los badges del historial */
        .tombola-container .bg-sky-100 { background-color: var(--border-subtle) !important; }
        .tombola-container .border-sky-300 { border-color: var(--border) !important; }
        .tombola-container .text-sky-800 { color: var(--text-primary) !important; }
        /* ------------------------------------------------ */
        
        /* Animaciones para la apertura de la bola fï¿½sica de sorteo */
        .animate-sphere-bounce {
            animation: sphere-bounce 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }
        .animate-sphere-shake {
            animation: sphere-shake 0.8s ease-in-out infinite;
        }
        .animate-split-top {
            animation: split-top 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        .animate-split-bottom {
            animation: split-bottom 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
        }
        .animate-paper-unroll {
            animation: paper-unroll 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.1) forwards;
        }

        @keyframes sphere-bounce {
            0% { transform: scale(0.3) translateY(-300px); opacity: 0; }
            60% { transform: scale(1.1) translateY(15px); opacity: 1; }
            80% { transform: scale(0.9) translateY(-5px); }
            100% { transform: scale(1) translateY(0); }
        }
        @keyframes sphere-shake {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            10%, 30%, 50%, 70%, 90% { transform: translate(-4px, -2px) rotate(-3deg); }
            20%, 40%, 60%, 80% { transform: translate(4px, 2px) rotate(3deg); }
        }
        @keyframes split-top {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-95px) rotate(-12deg); opacity: 0.85; }
        }
        @keyframes split-bottom {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(95px) rotate(12deg); opacity: 0.85; }
        }
        @keyframes paper-unroll {
            0% { transform: scaleY(0) translateY(-20px); opacity: 0; }
            100% { transform: scaleY(1) translateY(0); opacity: 1; }
        }

        /* Puntero de mano clï¿½sico */
        #wii-pointer {
            position: absolute;
            width: 50px;
            height: 50px;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-30%, -10%);
            transition: transform 0.05s ease-out;
            filter: drop-shadow(2px 3px 2px rgba(0,0,0,0.3));
        }

        /* Botones estilo redondeado deportivo adaptados al sistema */
        .wii-btn {
            background: linear-gradient(to bottom, var(--bg-card) 0%, var(--bg-secondary) 100%);
            border: 2px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 4px 0px var(--border-subtle);
            transition: all 0.15s ease-in-out;
            position: relative;
            overflow: hidden;
            color: var(--text-primary);
        }

        .wii-btn:hover {
            border-color: var(--accent-gold);
            transform: translateY(-2px);
        }

        .wii-btn:active {
            transform: translateY(2px);
            box-shadow: 0 2px 0px var(--border-subtle);
        }

        .wii-btn-blue {
            background: linear-gradient(to bottom, var(--accent-gold) 0%, #a07c1a 100%);
            border: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 0px rgba(0,0,0,0.3);
            color: #000 !important;
            font-weight: 800;
        }
        .wii-btn-blue:hover {
            border-color: #fff;
            filter: brightness(1.1);
        }

        .wii-btn-orange {
            background: linear-gradient(to bottom, var(--accent-blue) 0%, #005a87 100%);
            border: 2px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 0px rgba(0,0,0,0.3);
            color: #fff !important;
            font-weight: 800;
        }
        .wii-btn-orange:hover {
            border-color: #fff;
            filter: brightness(1.1);
        }

        /* Contenedores de paneles */
        .wii-panel {
            background-color: var(--bg-card);
            border: 2px solid var(--border);
            box-shadow: var(--shadow);
            border-radius: 24px;
        }

        /* Fondo punteado retro de la interfaz */
        .wii-bg-grid {
            background-image: radial-gradient(var(--border-subtle) 20%, transparent 20%),
                              radial-gradient(var(--border-subtle) 20%, transparent 20%);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
            background-color: transparent;
        }

        /* Animaciï¿½n de flotar suave */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 4s ease-in-out infinite;
        }

        /* Animaciï¿½n del Ticker de Nombres */
        @keyframes marquee {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }
        .animate-marquee {
            animation: marquee 60s linear infinite;
            display: inline-block;
            white-space: nowrap;
            will-change: transform;
        }

        /* Estilo para los carteles de victoria deportivos */
        .strike-banner {
            background: linear-gradient(90deg, transparent 0%, rgba(0, 160, 233, 0.95) 15%, rgba(0, 160, 233, 0.95) 85%, transparent 100%);
            text-shadow: 3px 3px 0px rgba(0,0,0,0.3);
        }
    
<style>
/* ================================================= */
/* CONFIGURACIï¿½N DEL SORTEO - MODO CLARO OVERRIDES   */
/* ================================================= */
[data-theme="light"] #setup-view {
    color: #0f172a !important;
}

[data-theme="light"] #setup-view h2 {
    color: #0f172a !important;
}

[data-theme="light"] #setup-view .max-w-\[1600px\] {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05) !important;
}

[data-theme="light"] #setup-view .bg-slate-900\/80 {
    background-color: #f8fafc !important;
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04) !important;
}

[data-theme="light"] #setup-view .bg-slate-900\/80 h3 {
    color: #b45309 !important;
    border-color: #cbd5e1 !important;
}

[data-theme="light"] #setup-view .bg-slate-900\/80 h3 span {
    color: #b45309 !important;
}

[data-theme="light"] #setup-view .bg-slate-800\/40,
[data-theme="light"] #setup-view .bg-slate-800\/60 {
    background-color: #ffffff !important;
    background: #ffffff !important;
    border-color: #e2e8f0 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03) !important;
}

[data-theme="light"] #setup-view .bg-slate-800\/60:hover {
    background-color: #f1f5f9 !important;
}

[data-theme="light"] #setup-view .text-slate-100 {
    color: #0f172a !important;
}

[data-theme="light"] #setup-view .text-slate-400 {
    color: #475569 !important;
}

[data-theme="light"] #setup-view .text-slate-500 {
    color: #64748b !important;
}

[data-theme="light"] #setup-view .bg-slate-900\/50 {
    background-color: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

[data-theme="light"] #setup-view .bg-slate-900\/40 {
    background-color: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

[data-theme="light"] #setup-view .bg-slate-800 {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #cbd5e1 !important;
}
        /* ================================================= */
        /* FULLSCREEN & MODO CLARO OVERRIDES                 */
        /* ================================================= */
        .tombola-container:fullscreen {
            background-color: var(--bg-primary, #f1f5f9) !important;
            width: 100vw !important;
            height: 100vh !important;
            padding: 1.25rem !important;
            overflow-y: auto !important;
            box-sizing: border-box !important;
        }

        /* Fondo punteado en Pantalla Completa segï¿½n tema */
        .tombola-container:fullscreen.wii-bg-grid {
            background-image: radial-gradient(var(--border-subtle, #cbd5e1) 20%, transparent 20%),
                              radial-gradient(var(--border-subtle, #cbd5e1) 20%, transparent 20%) !important;
            background-size: 30px 30px !important;
            background-position: 0 0, 15px 15px !important;
        }

        /* Modo Claro Fullscreen & General Overrides */
        [data-theme="light"] .tombola-container {
            color: #0f172a !important;
        }

        [data-theme="light"] .tombola-container:fullscreen {
            background-color: #f1f5f9 !important;
            background-image: radial-gradient(#cbd5e1 20%, transparent 20%),
                              radial-gradient(#cbd5e1 20%, transparent 20%) !important;
            background-size: 30px 30px !important;
            background-position: 0 0, 15px 15px !important;
        }

        [data-theme="light"] .tombola-container header {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-color: #e2e8f0 !important;
        }

        [data-theme="light"] .tombola-container h1,
        [data-theme="light"] .tombola-container h2,
        [data-theme="light"] .tombola-container h3 {
            color: #0f172a !important;
        }

        [data-theme="light"] .tombola-container p {
            color: #475569 !important;
        }

        [data-theme="light"] .tombola-container .wii-panel {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-slate-800\/40 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-slate-800\/40 span {
            color: #1e293b !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-sky-900\/80 {
            background-color: #e0f2fe !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.25) !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-sky-900\/80 span {
            color: #0369a1 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-emerald-950\/50 {
            background-color: #ecfdf5 !important;
            border-color: #6ee7b7 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-emerald-950\/50 .text-gray-100 {
            color: #065f46 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-emerald-900\/60 {
            background-color: #d1fae5 !important;
            border-color: #a7f3d0 !important;
            color: #047857 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-emerald-900\/60 small {
            color: #059669 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-slate-900\/60 {
            background-color: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        [data-theme="light"] #tombola-available-prizes .bg-slate-900\/60:hover {
            background-color: #f1f5f9 !important;
        }

        [data-theme="light"] #tombola-available-prizes .text-slate-400 {
            color: #475569 !important;
        }

        [data-theme="light"] #tombola-available-prizes .from-emerald-900\/40 {
            background: linear-gradient(to bottom, #ecfdf5 0%, #d1fae5 100%) !important;
            border-color: #10b981 !important;
        }

        [data-theme="light"] #tombola-available-prizes .from-emerald-900\/40 h4 {
            color: #065f46 !important;
        }

        [data-theme="light"] #tombola-available-prizes .from-emerald-900\/40 p {
            color: #047857 !important;
        }

        [data-theme="light"] .tombola-container #wii-clock {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #334155 !important;
        }

        [data-theme="light"] .tombola-container .wii-btn {
            background: linear-gradient(to bottom, #ffffff 0%, #f8fafc 100%) !important;
            border-color: #cbd5e1 !important;
            color: #1e293b !important;
            box-shadow: 0 4px 0px #cbd5e1 !important;
        }

        [data-theme="light"] .tombola-container .wii-btn:hover {
            border-color: #0284c7 !important;
            color: #0284c7 !important;
        }
</style>
@endpush

@section('content')
<div class="tombola-container wii-bg-grid flex flex-col justify-between overflow-hidden relative">

    <!-- Puntero de Mano (P1) -->
    <div id="wii-pointer" style="width: 50px; height: 50px; position: absolute; pointer-events: none; z-index: 9999; margin-top: -5px; margin-left: -20px; filter: drop-shadow(2px 4px 4px rgba(0,0,0,0.4));">
        <div class="relative w-full h-full" style="transform: rotate(-15deg);">
            <i class="fa-solid fa-hand-pointer text-white" style="font-size: 45px; -webkit-text-stroke: 2.5px #333;"></i>
            <div class="absolute -bottom-1 -right-1 bg-[#00a0e9] border-[2px] border-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm" style="transform: rotate(15deg);">
                <span class="text-[9px] font-black text-white font-['Nunito'] tracking-tighter" style="margin-top: 1px;">P1</span>
            </div>
        </div>
    </div>

    <header class="w-full bg-white/80 border-b-4 border-white shadow-sm py-4 px-6 flex justify-between items-center z-10">
        <div class="flex items-center space-x-4">
            <div class="bg-[#00a0e9] text-white font-bold px-4 py-2 rounded-full text-lg shadow-inner flex items-center gap-2">
                <i class="fa-solid fa-circle-nodes"></i>
                <span>Plaza de Sorteos</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-700 tracking-wide">Tï¿½mbola Ascencio</h1>
        </div>
        
        <!-- Controladores y Sonido -->
        <div class="flex items-center space-x-3">
            <!-- Botï¿½n de mï¿½sica -->
            <button onclick="toggleMusic()" id="music-btn" class="wii-btn px-4 py-2 flex items-center gap-2 text-gray-700 font-medium text-sm">
                <i id="music-icon" class="fa-solid fa-volume-xmark text-red-500"></i>
                <span id="music-text">Mï¿½sica: OFF</span>
            </button>
            <!-- Interruptor para cursor de mano -->
            <button onclick="toggleWiiCursor()" class="wii-btn px-4 py-2 flex items-center gap-2 text-gray-700 font-medium text-sm">
                <i class="fa-solid fa-hand-pointer text-[#00a0e9]"></i>
                <span>Puntero de Mano</span>
            </button>
            <!-- Botï¿½n Pantalla Completa -->
            <button onclick="toggleFullscreen()" class="wii-btn px-4 py-2 flex items-center gap-2 text-gray-700 font-medium text-sm">
                <i id="fs-icon" class="fa-solid fa-expand text-[#00a0e9]"></i>
                <span id="fs-text">Pantalla Completa</span>
            </button>
            <!-- Reloj del sistema -->
            <div class="bg-gray-100 border-2 border-gray-300 rounded-full px-4 py-1.5 text-gray-600 font-semibold text-sm" id="wii-clock">
                12:00 PM
            </div>
        </div>
    </header>

    <!-- PANTALLA PRINCIPAL DE JUEGO -->
    <main class="flex-grow flex flex-col items-center justify-center p-6 w-full h-full mx-auto z-10">
        
        <section id="tombola-view" class="w-full h-full flex flex-col items-center gap-6 flex-grow">
            <div class="w-full flex justify-between items-center px-4">
                <div class="flex items-center gap-2 bg-white/90 border-2 border-slate-200 px-4 py-2 rounded-2xl shadow-sm">
                    <span class="w-3.5 h-3.5 bg-emerald-500 rounded-full animate-ping"></span>
                    <span class="text-sm font-bold text-gray-600 uppercase tracking-wide">Tï¿½mbola Activa</span>
                </div>
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-800 tracking-wide">Tï¿½MBOLA DE ASCENCIO</h2>
                    <p class="text-sm text-gray-500">Mezcla las esferas de los participantes y extrae un ganador.</p>
                </div>
                <button onclick="switchView('setup-view')" class="wii-btn px-6 py-2.5 font-bold text-emerald-600 flex items-center gap-2">
                    <i class="fa-solid fa-cog"></i> Configurar Sorteo
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 w-full flex-grow pb-4">
                <!-- Panel Lateral Izquierdo: Lista de Participantes -->
                <div class="wii-panel p-4 flex flex-col h-[65vh] min-h-[450px] lg:col-span-1">
                    <h3 class="text-xl font-bold text-gray-700 border-b pb-2 mb-4 flex items-center justify-between">
                        <span>Participantes en Bombo</span>
                        <span id="tombola-participant-count" class="bg-[#00a0e9] text-white text-xs px-2.5 py-1 rounded-full">0</span>
                    </h3>
                    <div id="tombola-miis-list" class="flex-grow overflow-y-auto space-y-2 pr-1">
                        <!-- Generado dinï¿½micamente -->
                    </div>
                </div>

                <!-- Centro: Lienzo de la Tï¿½mbola Fï¿½sica -->
                <div class="wii-panel p-6 flex flex-col items-center justify-between lg:col-span-2 h-[65vh] min-h-[450px] relative">
                    <!-- Canvas de la tï¿½mbola -->
                    <div class="relative w-full h-full flex-grow flex items-center justify-center">
                        <canvas id="tombola-canvas" class="w-full h-full min-h-[300px] rounded-2xl bg-gradient-to-b from-sky-50 to-white border-2 border-slate-200/50 shadow-inner"></canvas>
                        
                        <!-- DEBUG OVERLAY (Removed) -->
                        
                        <!-- Canal de salida para la bola ganadora -->
                        <div class="absolute bottom-0 right-1/4 w-12 h-24 border-l-4 border-r-4 border-dashed border-sky-400 bg-sky-100/30 -z-10 rounded-b-xl flex items-end justify-center">
                            <i class="fa-solid fa-arrow-down text-sky-400 mb-2 animate-bounce"></i>
                        </div>
                    </div>



                    <!-- Controles de la Tï¿½mbola -->
                    <div class="w-full flex gap-4 mt-4">
                        <button id="btn-spin-tombola" onclick="spinTombolaManual()" class="wii-btn wii-btn-orange flex-1 py-4 font-bold text-xl flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrows-spin"></i>
                            <span>MEZCLAR BOLAS</span>
                        </button>
                        <button id="btn-draw-ball" onclick="drawBall()" class="wii-btn wii-btn-blue flex-1 py-4 font-bold text-xl flex items-center justify-center gap-2">
                            <i class="fa-solid fa-gift"></i>
                            <span>ï¿½EXTRAER GANADOR!</span>
                        </button>
                    </div>
                </div>

                <!-- Panel Lateral Derecho: Tablero de Premios y Ganadores -->
                <div class="wii-panel p-4 flex flex-col h-[65vh] min-h-[450px] lg:col-span-1">
                    <h3 class="font-bold text-gray-700 mb-2 flex items-center gap-2 border-b pb-2">
                        <i class="fa-solid fa-gift text-orange-500"></i>
                        <span>Tablero de Sorteo</span>
                        <span id="tombola-prize-count-view" class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full ml-auto">0</span>
                    </h3>
                    
                    <!-- Encabezados de tabla -->
                    <div class="flex text-[11px] font-black text-gray-400 uppercase tracking-wider px-2 py-2 border-b border-gray-100">
                        <div class="w-1/2">Premio</div>
                        <div class="w-1/2 text-right">Ganador</div>
                    </div>
                    
                    <div id="tombola-available-prizes" class="flex-grow overflow-y-auto pr-1 space-y-1.5 mt-2">
                        <!-- Rellenado dinï¿½micamente -->
                    </div>
                </div>
            </div>
        </section>

        <!-- Menï¿½ Contextual para Reordenar Premios (Windows Style) -->
        <div id="prize-context-menu" class="hidden absolute bg-white/95 backdrop-blur-md border border-gray-200 rounded-lg shadow-lg z-[10000] w-40 text-[13px] font-sans text-gray-800 p-1">
            <button onclick="contextMenuUp()" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 rounded-md transition-none border-none outline-none flex items-center gap-2">
                <i class="fa-solid fa-arrow-up text-[10px] text-gray-500"></i> Ascender
            </button>
            <button onclick="contextMenuDown()" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 rounded-md transition-none border-none outline-none flex items-center gap-2">
                <i class="fa-solid fa-arrow-down text-[10px] text-gray-500"></i> Descender
            </button>
        </div>

        <!-- SECCIï¿½N DE CONFIGURACIï¿½N -->
        <section id="setup-view" class="w-full hidden flex-col items-center gap-6">
            <div class="w-full flex justify-between items-center px-4 max-w-5xl mb-2">
                <button onclick="switchView('tombola-view')" class="bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-600 shadow-md rounded-lg px-6 py-2.5 font-medium flex items-center gap-2 transition-colors">
                    <i class="fa-solid fa-chevron-left"></i> Volver a la Tï¿½mbola
                </button>
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-white tracking-tight drop-shadow-sm">Configuraciï¿½n del Sorteo</h2>
                    <p class="text-sm text-slate-400 font-medium mt-1 uppercase tracking-widest">Gestiï¿½n avanzada y control interno</p>
                </div>
                <button onclick="resetData()" class="bg-slate-800 hover:bg-red-900/40 text-red-400 hover:text-red-300 border border-slate-700 hover:border-red-800/50 shadow-md rounded-lg px-5 py-2.5 text-sm font-bold flex items-center gap-2 transition-all">
                    <i class="fa-solid fa-arrow-rotate-left"></i> Reiniciar Todo
                </button>
            </div>

            <div class="w-full max-w-[1600px] bg-slate-900/50 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-[0_0_50px_rgba(0,0,0,0.6)] border border-slate-700/60 mx-auto flex flex-col items-center">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 w-full">
                
                <!-- COLUMNA 1: Participantes -->
                <div class="bg-slate-900/80 backdrop-blur-md p-7 flex flex-col h-[620px] rounded-2xl shadow-2xl border border-slate-700">
                    <h3 class="text-xl font-bold text-white mb-5 flex justify-between items-center border-b border-slate-700/60 pb-4">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-users text-indigo-400"></i> Participantes Activos</span>
                        <span id="setup-participant-count" class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs px-3 py-1 rounded-md font-bold tracking-wider">0</span>
                    </h3>

                    <!-- Formulario Aï¿½adir Participante (Eliminado) -->

                    <!-- Lista de Participantes creados -->
                    <div id="setup-miis-list" class="flex-grow overflow-y-auto space-y-2 pr-1">
                        <!-- Generado dinï¿½micamente -->
                    </div>
                </div>

                <!-- COLUMNA 2: Premios del Sorteo -->
                <div class="bg-slate-900/80 backdrop-blur-md p-7 flex flex-col h-[620px] rounded-2xl shadow-2xl border border-slate-700">
                    <h3 class="text-xl font-bold text-white mb-5 flex justify-between items-center border-b border-slate-700/60 pb-4">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-trophy text-amber-400"></i> Registro de Ganadores</span>
                        <span id="setup-winners-count" class="bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs px-3 py-1 rounded-md font-bold tracking-wider">0</span>
                    </h3>

                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50 mb-5 text-xs font-medium text-slate-400 flex justify-between items-center shadow-inner">
                        <span class="tracking-wide">Marca los premios entregados fï¿½sicamente.</span>
                        <button onclick="clearHistories()" class="bg-slate-900 hover:bg-red-900/40 text-slate-400 hover:text-red-400 border border-slate-700 hover:border-red-800/50 rounded-lg px-4 py-2 transition-all flex items-center font-bold tracking-wide"><i class="fa-solid fa-trash-can mr-2 text-[11px]"></i> Limpiar</button>
                    </div>

                    <!-- Lista de Ganadores -->
                    <div id="setup-winners-list" class="flex-grow overflow-y-auto space-y-2 pr-1">
                        <!-- Generado dinï¿½micamente -->
                    </div>
                </div>

                <!-- COLUMNA 3: Canjes por Puntos -->
                <div class="bg-slate-900/80 backdrop-blur-md p-7 flex flex-col h-[620px] rounded-2xl shadow-2xl border border-slate-700">
                    <h3 class="text-xl font-bold text-white mb-5 flex justify-between items-center border-b border-slate-700/60 pb-4">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-gift text-emerald-400"></i> Premios Canjeados (Puntos)</span>
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs px-3 py-1 rounded-md font-bold tracking-wider">{{ count($historialPuntos) }}</span>
                    </h3>

                    <div class="bg-slate-800/40 p-4 rounded-xl border border-slate-700/50 mb-5 text-xs font-medium text-slate-400 flex justify-between items-center shadow-inner">
                        <span class="tracking-wide">Historial de premios canjeados mediante puntos.</span>
                    </div>

                    <!-- Lista de Canjes -->
                    <div class="flex-grow overflow-y-auto space-y-2 pr-1">
                        @forelse($historialPuntos as $hp)
                            <div class="flex items-center justify-between p-3.5 bg-slate-800/60 hover:bg-slate-700/60 rounded-xl border border-slate-700/80 shadow-sm transition-colors mb-2">
                                <div class="flex flex-col truncate w-full">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-bold text-slate-100 text-[15px] tracking-wide truncate" title="{{ $hp['participante'] }}">{{ $hp['participante'] }} <span class="text-[11px] text-slate-400 font-medium ml-2">ID #{{ $hp['participante_id'] }}</span></span>
                                        <span class="text-xs text-slate-400 font-bold bg-slate-900/50 px-2 py-0.5 rounded">{{ $hp['fecha'] }}</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-xs text-emerald-400 font-bold truncate tracking-wide"><i class="fa-solid fa-gift mr-1.5 opacity-80"></i>{{ $hp['premio'] }} <span class="text-white bg-emerald-500/30 px-1.5 py-0.5 rounded ml-1">x{{ $hp['cantidad'] }}</span></span>
                                        <span class="text-xs text-red-400 font-bold">-{{ number_format($hp['puntos'] * $hp['cantidad']) }} pts</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-slate-500 text-center py-10 text-sm font-medium"><i class="fa-solid fa-inbox text-2xl mb-2 block opacity-50"></i>No hay canjes registrados.</div>
                        @endforelse
                    </div>
                </div>

                </div>
            </div>
        </section>

    </main>

    <!-- MODAL DE APERTURA DE BOLA Y CELEBRACIï¿½N -->
    <div id="sphere-opening-modal" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md flex flex-col items-center justify-center hidden z-50">
        
        <!-- Contenedor de confeti de fondo -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none" id="modal-confetti" style="z-index: 2;"></div>

        <!-- Banner de Victoria Horizontal (Cruza por detrï¿½s de la bola) -->
        <div id="victory-strike-banner" class="strike-banner w-full py-8 md:py-12 absolute left-0 right-0 top-1/2 -translate-y-1/2 scale-y-0 opacity-0 transition-all duration-500 flex flex-col md:flex-row justify-between items-center px-6 md:px-20" style="z-index: 5;">
            <!-- Franja superior de acento blanco -->
            <div class="absolute top-0 w-full h-1.5 bg-white/40"></div>
            
            <!-- Bloque Izquierdo: Datos del Ganador -->
            <div class="text-center md:text-left flex flex-col justify-center w-full md:w-1/3 mb-2 md:mb-0">
                <span class="text-orange-400 font-black tracking-widest text-sm md:text-xl uppercase drop-shadow animate-pulse">? ï¿½TENEMOS UN GANADOR! ?</span>
                <h2 id="strike-winner-name" class="text-white font-extrabold text-4xl md:text-6xl uppercase tracking-wide truncate drop-shadow">MATT</h2>
            </div>
            
            <!-- Bloque Centro (Espacio vacï¿½o reservado para que se luzca la esfera y el papel) -->
            <div class="hidden md:block w-1/3"></div>
            
            <!-- Bloque Derecho: Datos del Premio -->
            <div class="text-center md:text-right flex flex-col justify-center w-full md:w-1/3 mt-2 md:mt-0">
                <span class="text-sky-200 font-bold tracking-widest text-sm md:text-xl uppercase drop-shadow">PREMIO EXTRAï¿½DO</span>
                <h3 id="strike-prize-name" class="text-yellow-300 font-black text-2xl md:text-4xl truncate drop-shadow">Premio Especial</h3>
            </div>
            
            <!-- Franja inferior de acento blanco -->
            <div class="absolute bottom-0 w-full h-1.5 bg-white/40"></div>
        </div>

        <!-- Contenido principal interactivo del sorteo -->
        <div class="text-center flex flex-col items-center max-w-3xl w-full px-4 z-10 relative">
            
            <h3 id="modal-sphere-title" class="text-white text-4xl md:text-5xl font-bold mb-20 tracking-wide uppercase drop-shadow animate-pulse">
                ï¿½BOLA SELECCIONADA!
            </h3>

            <!-- Contenedor principal de la esfera interactiva -->
            <div id="sphere-wrapper" class="relative w-80 h-80 flex items-center justify-center mb-24" style="z-index: 10;">
                
                <!-- Papel enrollado que sale del interior -->
                <div id="drawn-paper" class="absolute w-72 bg-gradient-to-b from-amber-50 to-orange-50 border-4 border-amber-400 rounded-2xl shadow-2xl p-6 flex flex-col items-center justify-center opacity-0 transform origin-top" style="z-index: 15;">
                    <!-- Avatar del ganador celebrando -->
                    <div class="w-24 h-24 bg-white rounded-full border-4 border-amber-400 p-1 overflow-hidden shadow-md mb-3 flex items-center justify-center" id="paper-mii-mini">
                        <!-- Renderizado dinï¿½micamente -->
                    </div>
                    
                    <span class="text-xs text-orange-600 font-extrabold tracking-widest uppercase animate-pulse">ï¿½Felicidades!</span>
                    <h3 id="paper-winner-name" class="text-3xl font-black text-slate-800 tracking-wide mt-2 mb-1 text-center truncate w-full">Nombre</h3>
                    <div class="w-full border-t-2 border-dashed border-amber-400 my-2"></div>
                    
                    <span id="paper-winner-num" class="bg-amber-200 text-amber-800 font-bold px-4 py-1 rounded-full text-sm mb-2 mt-1">ID #123</span>
                    <span id="paper-winner-prize" class="text-2xl text-amber-900 font-extrabold text-center leading-tight">Premio</span>
                </div>
                
                <!-- Mitad superior de la esfera -->
                <div id="sphere-top" class="absolute inset-0 z-20 pointer-events-none transition-transform duration-500">
                    <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-md">
                        <clipPath id="top-clip"><rect x="0" y="0" width="100" height="50"/></clipPath>
                        <g clip-path="url(#top-clip)">
                            <circle cx="50" cy="50" r="46" id="svg-sphere-top-bg" fill="#00a0e9" stroke="#ffffff" stroke-width="3"/>
                            <circle cx="50" cy="50" r="46" fill="url(#sphere-shading-top)"/>
                            <circle cx="50" cy="50" r="22" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                            <text x="50" y="46" font-family="'Fredoka'" font-size="20" font-weight="bold" fill="#334155" text-anchor="middle" id="svg-sphere-top-num">?</text>
                        </g>
                        <defs>
                            <radialGradient id="sphere-shading-top" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.6"/>
                                <stop offset="40%" stop-color="#ffffff" stop-opacity="0"/>
                                <stop offset="100%" stop-color="#000000" stop-opacity="0.45"/>
                            </radialGradient>
                        </defs>
                    </svg>
                </div>
                
                <!-- Mitad inferior de la esfera -->
                <div id="sphere-bottom" class="absolute inset-0 z-10 pointer-events-none transition-transform duration-500">
                    <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-md">
                        <clipPath id="bottom-clip"><rect x="0" y="50" width="100" height="50"/></clipPath>
                        <g clip-path="url(#bottom-clip)">
                            <circle cx="50" cy="50" r="46" id="svg-sphere-bottom-bg" fill="#00a0e9" stroke="#ffffff" stroke-width="3"/>
                            <circle cx="50" cy="50" r="46" fill="url(#sphere-shading-bottom)"/>
                            <circle cx="50" cy="50" r="22" fill="#ffffff" stroke="#cbd5e1" stroke-width="2"/>
                            <text x="50" y="58" font-family="'Fredoka'" font-size="20" font-weight="bold" fill="#334155" text-anchor="middle" id="svg-sphere-bottom-num">?</text>
                        </g>
                        <defs>
                            <radialGradient id="sphere-shading-bottom" cx="35%" cy="35%" r="65%">
                                <stop offset="0%" stop-color="#ffffff" stop-opacity="0.4"/>
                                <stop offset="50%" stop-color="#ffffff" stop-opacity="0"/>
                                <stop offset="100%" stop-color="#000000" stop-opacity="0.5"/>
                            </radialGradient>
                        </defs>
                    </svg>
                </div>
            </div>

            <!-- Botï¿½n para finalizar la celebraciï¿½n -->
            <button id="btn-proceed-celebration" onclick="closeVictoryModal()" class="wii-btn wii-btn-orange px-12 py-5 text-2xl font-bold rounded-2xl max-w-md w-full shadow-2xl opacity-0 transform translate-y-4 transition-all duration-500" style="z-index: 10;">
                <i class="fa-solid fa-check mr-2"></i>
                <span>ï¿½EXCELENTE!</span>
            </button>
        </div>
    </div>

    <!-- MODAL DE ANUNCIO DE PREMIO -->
    <div id="prize-announcement-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm flex flex-col items-center justify-center hidden z-50 transition-opacity duration-300">
        <div class="strike-banner w-full py-12 absolute left-0 right-0 top-1/2 -translate-y-1/2 scale-y-0 opacity-0 transition-all duration-500 flex flex-col items-center justify-center px-6" id="prize-announcement-banner">
            <div class="absolute top-0 w-full h-1.5 bg-white/40"></div>
            <span class="text-orange-400 font-black tracking-widest text-xl md:text-2xl uppercase drop-shadow animate-pulse mb-2">? PRï¿½XIMO PREMIO EN JUEGO ?</span>
            <h2 id="announcement-prize-name" class="text-white font-extrabold text-5xl md:text-7xl uppercase tracking-wide truncate drop-shadow text-center">TV 4K</h2>
            <div class="absolute bottom-0 w-full h-1.5 bg-white/40"></div>
        </div>
    </div>

    <!-- Pie de Pï¿½gina -->
    <footer class="w-full bg-[#f4f7f8] border-t-4 border-white py-4 px-6 flex justify-between items-center z-10 text-xs text-slate-800 font-semibold">
        <div>
            <span>Plaza de Sorteos v1.6</span>
            <span class="mx-2">|</span>
            <span class="text-emerald-600"><i class="fa-solid fa-wifi"></i> Conexiï¿½n de Tï¿½mbola Estable</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="bg-gray-200 px-3 py-1 rounded-full text-gray-600">
                <i class="fa-solid fa-gamepad mr-1"></i> Control de Sorteo [1]
            </span>
            <span>Estilo visual deportivo retro 2006-2026</span>
        </div>
    </footer>

    <script>
        // --- AUDIO SYNTH SYSTEM (Web Audio API) ---
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        let bgmSource = null;
        let isMusicPlaying = false;

        // Sonido de caï¿½da de la bola seleccionada
        function playSoundBallDrop() {
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(180, now);
                osc.frequency.exponentialRampToValueAtTime(90, now + 0.35);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                osc.start();
                osc.stop(now + 0.35);
            } catch(e){}
        }

        // Sonido de vibraciï¿½n de suspenso
        function playSoundRumble() {
            try {
                const now = audioCtx.currentTime;
                for (let i = 0; i < 10; i++) {
                    const timeOffset = i * 0.08;
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(80 + Math.random() * 40, now + timeOffset);
                    gain.gain.setValueAtTime(0.18, now + timeOffset);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + timeOffset + 0.07);
                    osc.start(now + timeOffset);
                    osc.stop(now + timeOffset + 0.07);
                }
            } catch(e){}
        }

        // Sonido de explosiï¿½n pop al abrirse la bola
        function playSoundBallPop() {
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(320, now);
                osc.frequency.exponentialRampToValueAtTime(950, now + 0.18);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.18);
                osc.start();
                osc.stop(now + 0.18);

                // Brillo agudo adicional
                const ch = audioCtx.createOscillator();
                const chGain = audioCtx.createGain();
                ch.connect(chGain);
                chGain.connect(audioCtx.destination);
                ch.type = 'triangle';
                ch.frequency.setValueAtTime(600, now + 0.08);
                ch.frequency.exponentialRampToValueAtTime(1400, now + 0.45);
                chGain.gain.setValueAtTime(0, now);
                chGain.gain.linearRampToValueAtTime(0.2, now + 0.12);
                chGain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
                ch.start(now + 0.08);
                ch.stop(now + 0.45);
            } catch(e){}
        }

        // Sonido de papel estirï¿½ndose/desenrollï¿½ndose
        function playSoundRustle() {
            try {
                const now = audioCtx.currentTime;
                const bufferSize = audioCtx.sampleRate * 0.35;
                const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    data[i] = Math.random() * 2 - 1;
                }
                const noise = audioCtx.createBufferSource();
                noise.buffer = buffer;
                
                const filter = audioCtx.createBiquadFilter();
                filter.type = 'bandpass';
                filter.frequency.value = 1100;
                
                const gain = audioCtx.createGain();
                gain.gain.setValueAtTime(0.07, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                
                noise.connect(filter);
                filter.connect(gain);
                gain.connect(audioCtx.destination);
                
                noise.start(now);
                noise.stop(now + 0.35);
            } catch(e){}
        }

        // Sonido de hover sobre botones
        function playSoundHover() {
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(800, audioCtx.currentTime + 0.08);
                
                gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.08);
            } catch(e){}
        }

        // Sonido de clic (pop seco clï¿½sico)
        function playSoundClick() {
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                osc.frequency.setValueAtTime(110, audioCtx.currentTime + 0.03);
                
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.15);
            } catch(e){}
        }

        // Sonido de suspense / redoble de tambor
        let rollInterval = null;
        function playSoundRoll(start) {
            if (start) {
                if (rollInterval) return;
                rollInterval = setInterval(() => {
                    try {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(60 + Math.random()*20, audioCtx.currentTime);
                        
                        gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);
                        
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.08);
                    } catch(e){}
                }, 50);
            } else {
                if (rollInterval) {
                    clearInterval(rollInterval);
                    rollInterval = null;
                }
            }
        }

        // Fanfarria de Victoria deportiva
        function playSoundVictory() {
            try {
                const now = audioCtx.currentTime;
                const notes = [261.63, 329.63, 392.00, 523.25, 659.25, 783.99, 1046.50];
                
                notes.forEach((freq, idx) => {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.12));
                    
                    gain.gain.setValueAtTime(0.15, now + (idx * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (idx * 0.12) + 0.6);
                    
                    osc.start(now + (idx * 0.12));
                    osc.stop(now + (idx * 0.12) + 0.6);
                });

                // Ruido de aplausos sintetizado
                for (let i = 0; i < 15; i++) {
                    setTimeout(() => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(150 + Math.random() * 200, audioCtx.currentTime);
                        gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.4);
                    }, 50 + (i * 80));
                }
            } catch(e){}
        }

        // Mï¿½sica de fondo sintetizada en tiempo real (estilo menï¿½ deportivo retro)
        function startBgm() {
            if (isMusicPlaying) return;
            try {
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }

                const synthTheme = () => {
                    const now = audioCtx.currentTime;
                    const chordProgression = [
                        [196.00, 246.94, 293.66, 392.00], // Sol Mayor
                        [220.00, 261.63, 329.63, 440.00], // La menor
                        [146.83, 220.00, 293.66, 369.99], // Re Mayor
                        [196.00, 246.94, 293.66, 392.00]  // Sol Mayor
                    ];
                    
                    let time = now;
                    chordProgression.forEach((chord) => {
                        chord.forEach((note, noteIdx) => {
                            const osc = audioCtx.createOscillator();
                            const gain = audioCtx.createGain();
                            osc.connect(gain);
                            gain.connect(audioCtx.destination);
                            
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(note, time + (noteIdx * 0.3));
                            
                            gain.gain.setValueAtTime(0.06, time + (noteIdx * 0.3));
                            gain.gain.exponentialRampToValueAtTime(0.001, time + (noteIdx * 0.3) + 0.5);
                            
                            osc.start(time + (noteIdx * 0.3));
                            osc.stop(time + (noteIdx * 0.3) + 0.5);
                        });
                        time += 1.2;
                    });
                };

                synthTheme();
                bgmSource = setInterval(synthTheme, 4800);
                isMusicPlaying = true;
                
                document.getElementById('music-icon').className = "fa-solid fa-volume-high text-emerald-500";
                document.getElementById('music-text').innerText = "Mï¿½sica: ON";
            } catch(e) {
                console.error("No se pudo iniciar la mï¿½sica sintetizada:", e);
            }
        }

        function stopBgm() {
            if (bgmSource) {
                clearInterval(bgmSource);
                bgmSource = null;
            }
            isMusicPlaying = false;
            document.getElementById('music-icon').className = "fa-solid fa-volume-xmark text-red-500";
            document.getElementById('music-text').innerText = "Mï¿½sica: OFF";
        }

        function toggleMusic() {
            playSoundClick();
            if (isMusicPlaying) {
                stopBgm();
            } else {
                startBgm();
            }
        }
    </script>

    <script>
        // --- BASE DE DATOS Y ESTADO DE LA APLICACIï¿½N ---
        let participants = [];
        let prizes = [];
        let drawnBallsHistory = [];
        
        let currentAnimWinnerMii = null;
        let currentAnimWinningPrizeName = "";

        // Lista de personajes preestablecidos (Sin nombres del elenco original de la consola)
        const defaultMiis = [
            { id: '1', display_id: 101, name: 'Carlos', color: '#ff9500', face: 'cool' },
            { id: '2', display_id: 102, name: 'Sofï¿½a', color: '#e91e63', face: 'happy' },
            { id: '3', display_id: 103, name: 'Beto', color: '#76c336', face: 'excited' },
            { id: '4', display_id: 104, name: 'Ana', color: '#00a0e9', face: 'happy' },
            { id: '5', display_id: 105, name: 'Tomï¿½s', color: '#9c27b0', face: 'surprised' },
            { id: '6', display_id: 106, name: 'Lucï¿½a', color: '#e60012', face: 'cool' },
            { id: '7', display_id: 107, name: 'Silvia', color: '#ffeb3b', face: 'excited' }
        ];

        // Lista de premios preestablecidos (Genï¿½ricos)
        const defaultPrizes = [
            { id: 'p1', name: '?? Gran Trofeo de Oro', color: '#ffeb3b', type: 'sorteo' },
            { id: 'p2', name: '?? Remera de Campeï¿½n de Sorteos', color: '#00a0e9', type: 'sorteo' },
            { id: 'p3', name: '?? Gran Pizza Party de Celebraciï¿½n', color: '#ff9500', type: 'sorteo' },
            { id: 'p4', name: '?? Raqueta Profesional de Tenis', color: '#76c336', type: 'sorteo' },
            { id: 'p5', name: '??? 500 Puntos de Regalo', color: '#9c27b0', type: 'puntos' }
        ];

        function initData() {
            const backendParticipants = @json($participantes ?? []);
            const backendPrizes = @json($premios ?? []);
            const backendHistorial = @json($historial ?? []);

            participants = backendParticipants || [];
            prizes = backendPrizes || [];
            drawnBallsHistory = backendHistorial || [];
            
            updateUI();
        }

        function saveToStorage() {
            // Ya no se usa localStorage, se sincroniza con la BD
        }

        function syncPrizeOrderToDB() {
            const ordenes = prizes.map(p => p.id);
            fetch(`{{ route('eventos.sorteo.orden', $evento) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ordenes: ordenes })
            }).catch(e => console.error(e));
        }

        function movePrizeUp(prizeId) {
            const idx = prizes.findIndex(p => p.id == prizeId);
            if (idx <= 0) return;
            
            let prevIdx = -1;
            for(let i = idx - 1; i >= 0; i--) {
                if ((prizes[i].type || 'sorteo') === 'sorteo') {
                    prevIdx = i;
                    break;
                }
            }
            if (prevIdx !== -1) {
                const temp = prizes[idx];
                prizes[idx] = prizes[prevIdx];
                prizes[prevIdx] = temp;
                updateUI();
                syncPrizeOrderToDB();
            }
        }

        function movePrizeDown(prizeId) {
            const idx = prizes.findIndex(p => p.id == prizeId);
            if (idx === -1 || idx === prizes.length - 1) return;
            
            let nextIdx = -1;
            for(let i = idx + 1; i < prizes.length; i++) {
                if ((prizes[i].type || 'sorteo') === 'sorteo') {
                    nextIdx = i;
                    break;
                }
            }
            if (nextIdx !== -1) {
                const temp = prizes[idx];
                prizes[idx] = prizes[nextIdx];
                prizes[nextIdx] = temp;
                updateUI();
                syncPrizeOrderToDB();
            }
        }

        // --- MANEJO DEL MENï¿½ CONTEXTUAL (CLIC DERECHO) ---
        let currentContextMenuId = null;

        function showPrizeMenu(e, prizeId) {
            e.preventDefault(); // Ocultar el menï¿½ por defecto del navegador
            e.stopPropagation(); // Evitar que el clic llegue al document y lo cierre
            
            const menu = document.getElementById('prize-context-menu');
            const container = document.querySelector('.tombola-container');
            if (!menu || !container) return;
            
            currentContextMenuId = prizeId;
            menu.classList.remove('hidden');
            menu.style.display = 'block';
            
            const rect = container.getBoundingClientRect();
            let xPos = e.clientX - rect.left;
            let yPos = e.clientY - rect.top;
            
            // Evitar que el menï¿½ se salga del contenedor
            if (xPos + 180 > rect.width) xPos -= 180; // 180px aprox ancho
            if (yPos + 80 > rect.height) yPos -= 80;  // 80px aprox alto

            menu.style.left = xPos + 'px';
            menu.style.top = yPos + 'px';
        }

        document.addEventListener('click', () => {
            const menu = document.getElementById('prize-context-menu');
            if (menu) {
                menu.classList.add('hidden');
                menu.style.display = 'none';
            }
        });

        document.addEventListener('contextmenu', (e) => {
            const menu = document.getElementById('prize-context-menu');
            if (menu && !e.target.closest('.cursor-context-menu')) {
                menu.classList.add('hidden');
                menu.style.display = 'none';
            }
        });

        function contextMenuUp() {
            if (currentContextMenuId) movePrizeUp(currentContextMenuId);
        }
        function contextMenuDown() {
            if (currentContextMenuId) movePrizeDown(currentContextMenuId);
        }

        function resetData() {
            playSoundClick();
            participants = [...defaultMiis];
            prizes = [...defaultPrizes];
            drawnBallsHistory = [];
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        // --- GENERADOR DE AVATARES DE PERSONAJES EN SVG ---
        function generateMiiSVG(color, faceType) {
            // Mapeamos el color a un icono de herramientas/material elï¿½ctrico consistente
            const tools = [
                'fa-wrench', 'fa-screwdriver-wrench', 'fa-hammer', 'fa-plug', 
                'fa-lightbulb', 'fa-bolt', 'fa-toolbox', 'fa-hard-hat', 
                'fa-gear', 'fa-plug-circle-bolt', 'fa-bolt-lightning', 'fa-car-battery',
                'fa-screwdriver', 'fa-plug-circle-check', 'fa-power-off'
            ];
            
            // Hash simple del color (ej: #ff5733) para obtener un ï¿½ndice siempre igual para cada persona
            let hash = 0;
            if (color) {
                for (let i = 0; i < color.length; i++) {
                    hash = color.charCodeAt(i) + ((hash << 5) - hash);
                }
            }
            const iconIndex = Math.abs(hash) % tools.length;
            const iconClass = tools[iconIndex];

            return `
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: ${color}; color: white; border-radius: 50%;">
                    <i class="fa-solid ${iconClass}" style="font-size: 1.5em; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);"></i>
                </div>
            `;
        }
    </script>

    <script>
        // --- ACTUALIZACIï¿½N DE INTERFAZ DE USUARIO ---
        function updateUI() {
            document.getElementById('tombola-participant-count').innerText = participants.length;
            document.getElementById('setup-participant-count').innerText = participants.length;
            const setupPrizeCount = document.getElementById('setup-prize-count');
            if (setupPrizeCount) setupPrizeCount.innerText = prizes.length;
            
            const prizeCountView = document.getElementById('tombola-prize-count-view');
            const tombolaPrizes = document.getElementById('tombola-available-prizes');
            if (tombolaPrizes) {
                tombolaPrizes.innerHTML = '';
                // En la tï¿½mbola solo mostramos los premios destinados al azar (sorteo) que NO estï¿½n marcados como entregados
                const sorteoPrizes = prizes.filter(p => {
                    if ((p.type || 'sorteo') !== 'sorteo') return false;
                    
                    const isDelivered = p.delivered || drawnBallsHistory.some(h => {
                        if (p.canje_id && h.canje_id) return h.canje_id == p.canje_id && h.delivered;
                        const hPrizeName = (typeof h.prize === 'object' && h.prize) ? h.prize.name : h.prize;
                        const hWinnerName = h.p ? h.p.name : (h.name || '');
                        return hPrizeName === p.name && (hWinnerName === p.winner || h.display_id == p.winner_id) && h.delivered;
                    });

                    return !isDelivered;
                });
                
                if (prizeCountView) prizeCountView.innerText = sorteoPrizes.length;

                if (sorteoPrizes.length === 0) {
                    tombolaPrizes.innerHTML = '<div class="text-gray-400 text-center py-4 text-xs font-semibold">No hay premios pendientes de entrega.</div>';
                } else {
                    let nextPrizeFound = false;
                    sorteoPrizes.forEach(pr => {
                        const row = document.createElement('div');
                        const isWon = pr.winner ? true : false;
                        
                        let isNext = false;
                        if (!isWon && !nextPrizeFound) {
                            isNext = true;
                            nextPrizeFound = true;
                        }

                        let bgColor = isWon ? 'bg-emerald-950/50 border-emerald-700/60 shadow-sm' : 'bg-slate-800/40 border-slate-700/50';
                        if (isNext) {
                            bgColor = 'bg-sky-900/80 border-sky-400 shadow-[0_0_15px_rgba(56,189,248,0.4)] transform scale-[1.02] z-10';
                        }
                        
                        let iconColor = isWon ? 'text-emerald-400 bg-emerald-900/80' : 'text-gray-400 bg-slate-800';
                        if (isNext) {
                            iconColor = 'text-sky-100 bg-sky-500';
                        }
                        
                        if (isWon) {
                            row.className = `flex flex-col gap-2 p-2.5 rounded-xl border ${bgColor} transition-all duration-300 relative group select-none`;
                            row.innerHTML = `
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 overflow-hidden w-1/2">
                                        <div class="w-7 h-7 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0 shadow-inner">
                                            <i class="fa-solid fa-gift text-[10px]"></i>
                                        </div>
                                        <span class="font-bold text-gray-100 text-xs leading-tight truncate" title="${pr.name}">${pr.name}</span>
                                    </div>
                                    <div class="w-1/2 text-right overflow-hidden flex justify-end items-center">
                                        <span class="font-bold text-emerald-400 text-[11px] truncate bg-emerald-900/60 px-2 py-0.5 rounded-md border border-emerald-800/50 flex items-center gap-1" title="${pr.winner} #${pr.winner_id}">
                                            <i class="fa-solid fa-check text-[10px]"></i>
                                            <span class="truncate">${pr.winner}</span>
                                            <small class="text-emerald-500/70 font-semibold">#${pr.winner_id}</small>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end gap-2 pt-1.5 border-t border-emerald-800/40">
                                    <button onclick="confirmToggleDeliveryByPrize(event, '${pr.id}')" class="cursor-pointer group flex items-center gap-2 bg-slate-900/60 hover:bg-slate-900 border border-slate-700/80 px-2.5 py-1 rounded-lg transition-colors select-none shadow-inner" title="Marcar como Entregado">
                                        <span class="text-[10px] font-bold text-slate-400 group-hover:text-slate-200 uppercase tracking-wider">Entregado</span>
                                        <div class="relative w-7 h-3.5 rounded-full transition-colors bg-slate-800 border border-slate-600">
                                            <div class="absolute top-[1px] left-[1px] w-2.5 h-2.5 rounded-full transition-transform duration-300 bg-slate-400"></div>
                                        </div>
                                    </button>
                                    <button onclick="confirmRevertWinnerByPrize(event, '${pr.id}')" class="w-7 h-7 rounded-lg bg-slate-900/60 hover:bg-red-500/20 text-slate-400 hover:text-red-400 border border-slate-700 hover:border-red-500/50 transition-all flex items-center justify-center text-xs font-bold shadow-inner" title="Revertir premio (devolver al sorteo)">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </div>
                            `;
                        } else {
                            let winnerHtml = isNext 
                                ? `<span class="font-black text-sky-200 text-[10px] uppercase tracking-widest border border-sky-400/50 px-2 py-0.5 rounded-full bg-sky-800 animate-pulse"><i class="fa-solid fa-crosshairs mr-1"></i>Sorteando</span>` 
                                : `<span class="font-semibold text-gray-500 text-[11px] uppercase italic">Pendiente</span>`;

                            row.className = `flex items-center justify-between p-2 rounded-xl border ${bgColor} transition-all duration-300 relative group select-none cursor-context-menu`;
                            row.setAttribute('oncontextmenu', `showPrizeMenu(event, '${pr.id}')`);
                            row.title = "Clic derecho para cambiar el orden";
                            row.innerHTML = `
                                <div class="flex items-center gap-2 w-1/2 overflow-hidden pr-2">
                                    <div class="w-7 h-7 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0 shadow-inner">
                                        <i class="fa-solid ${isNext ? 'fa-star' : 'fa-box'} text-[10px]"></i>
                                    </div>
                                    <span class="font-bold ${isNext ? 'text-white' : 'text-gray-200'} text-xs leading-tight truncate" title="${pr.name}">${pr.name}</span>
                                </div>
                                <div class="w-1/2 text-right overflow-hidden flex justify-end items-center">
                                    ${winnerHtml}
                                </div>
                            `;
                        }
                        tombolaPrizes.appendChild(row);
                    });

                    if (!nextPrizeFound) {
                        // All prizes are won
                        const allWonMsg = document.createElement('div');
                        allWonMsg.className = "mt-4 p-5 rounded-2xl bg-gradient-to-b from-emerald-900/40 to-emerald-950/40 border-2 border-emerald-500/50 text-center shadow-lg";
                        allWonMsg.innerHTML = `
                            <div class="text-emerald-400 text-4xl mb-3 drop-shadow-[0_0_10px_rgba(52,211,153,0.5)]"><i class="fa-solid fa-trophy"></i></div>
                            <h4 class="font-black text-white text-[13px] uppercase tracking-widest">ï¿½Sorteo Finalizado!</h4>
                            <p class="text-emerald-200/80 text-xs mt-1 font-semibold">Todos los premios han sido entregados.</p>
                        `;
                        tombolaPrizes.appendChild(allWonMsg);
                    }
                }
            }

            // Agrupar participantes por display_id para la UI
            const groupedMap = new Map();
            participants.forEach(p => {
                if (groupedMap.has(p.display_id)) {
                    groupedMap.get(p.display_id).count++;
                } else {
                    groupedMap.set(p.display_id, { ...p, count: 1 });
                }
            });
            const groupedParticipants = Array.from(groupedMap.values());

            if (window.tombolaMarquee) cancelAnimationFrame(window.tombolaMarquee);
            const tombolaList = document.getElementById('tombola-miis-list');
            tombolaList.innerHTML = '';
            
            if (groupedParticipants.length === 0) {
                tombolaList.innerHTML = '<div class="text-gray-400 text-center py-10 text-sm">No hay participantes cargados. Agrega algunos en Configuraciï¿½n.</div>';
                tombolaList.style.overflowY = 'auto';
            } else {
                const fragment = document.createDocumentFragment();
                const renderLimit = 500; // Aumentado para el carrusel
                let toRender = groupedParticipants.slice(0, renderLimit);
                
                // Duplicar elementos para scroll infinito continuo (solo si son pocos o si no excede limite extremo)
                const isScrollable = toRender.length > 4;
                if (isScrollable) {
                    toRender = [...toRender, ...toRender];
                }
                
                toRender.forEach(p => {
                    const card = document.createElement('div');
                    card.className = "flex items-center gap-3 p-2 bg-white rounded-xl border-2 border-slate-200/80 shadow-xs transition-all";
                    card.innerHTML = `
                        <div class="w-12 h-12 bg-slate-50 rounded-full border border-slate-200 p-0.5 overflow-hidden flex-shrink-0">
                            ${generateMiiSVG(p.color, p.face)}
                        </div>
                        <div class="flex-grow">
                            <p class="font-bold text-gray-800 text-sm">${p.name}</p>
                            <span class="text-[10px] text-[#00a0e9] uppercase font-bold">ID #${p.display_id}</span>
                            ${p.count > 1 ? `<span class="ml-2 text-[10px] bg-[#ff9500] text-white px-2 py-0.5 rounded-full font-bold"><i class="fa-solid fa-ticket mr-1"></i>${p.count} Boletos</span>` : ''}
                        </div>
                    `;
                    fragment.appendChild(card);
                });
                
                if (groupedParticipants.length > renderLimit) {
                    const extra = document.createElement('div');
                    extra.className = "text-center py-3 mt-2 text-gray-500 font-bold text-sm bg-gray-50 rounded-xl border-2 border-dashed";
                    extra.innerText = `+ ${groupedParticipants.length - renderLimit} participantes mï¿½s...`;
                    fragment.appendChild(extra);
                }
                
                tombolaList.appendChild(fragment);

                if (isScrollable && groupedParticipants.length <= renderLimit) {
                    tombolaList.style.overflowY = 'hidden';
                    // Ocultar scrollbar estï¿½ndar en navegadores webkit temporalmente si es posible
                    tombolaList.style.scrollbarWidth = 'none'; // Firefox
                    
                    let scrollPos = tombolaList.scrollTop;
                    let isHovered = false;
                    
                    tombolaList.onmouseenter = () => isHovered = true;
                    tombolaList.onmouseleave = () => isHovered = false;
                    
                    function marqueeStep() {
                        if (!isHovered) {
                            scrollPos += 0.5; // Velocidad del carrusel
                            if (scrollPos >= tombolaList.scrollHeight / 2) {
                                scrollPos = 0; // Reset infinito imperceptible
                            }
                            tombolaList.scrollTop = scrollPos;
                        } else {
                            scrollPos = tombolaList.scrollTop;
                        }
                        window.tombolaMarquee = requestAnimationFrame(marqueeStep);
                    }
                    window.tombolaMarquee = requestAnimationFrame(marqueeStep);
                } else {
                    tombolaList.style.overflowY = 'auto';
                    tombolaList.onmouseenter = null;
                    tombolaList.onmouseleave = null;
                }
            }



            const setupMiis = document.getElementById('setup-miis-list');
            setupMiis.innerHTML = '';
            const renderLimitSetup = 150; // Lï¿½mite de optimizaciï¿½n de UI
            groupedParticipants.slice(0, renderLimitSetup).forEach(p => {
                const row = document.createElement('div');
                row.className = "flex items-center justify-between p-3.5 bg-slate-800/60 hover:bg-slate-700/60 rounded-xl border border-slate-700/80 shadow-sm transition-colors mb-2";
                row.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-900/50 border-2 border-slate-700 rounded-full overflow-hidden flex-shrink-0 flex items-center justify-center shadow-inner">
                            <div class="w-[120%] h-[120%] mt-2 ml-1" style="transform: scale(0.8)">
                                ${generateMiiSVG(p.color, p.face)}
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-100 text-[15px] tracking-wide">${p.name}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[11px] text-slate-400 font-medium">ID #${p.display_id}</span>
                                ${p.count > 1 ? `<span class="text-[10px] bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-2 py-0.5 rounded-md font-bold tracking-wide"><i class="fa-solid fa-ticket mr-1"></i>${p.count} Boletos</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <button onclick="removeParticipant('${p.id}')" class="w-9 h-9 rounded-lg bg-slate-900/40 hover:bg-red-500/20 text-slate-500 hover:text-red-400 border border-slate-700 hover:border-red-500/50 transition-all flex items-center justify-center font-bold" title="Eliminar todos los boletos de este participante">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                `;
                setupMiis.appendChild(row);
            });
            if (groupedParticipants.length > renderLimitSetup) {
                const extra = document.createElement('div');
                extra.className = "text-center py-2 text-gray-500 font-bold text-sm";
                extra.innerText = `+ ${groupedParticipants.length - renderLimitSetup} ocultos para fluidez...`;
                setupMiis.appendChild(extra);
            }

            // Actualizar vista de Ganadores en Setup
            const setupWinnersList = document.getElementById('setup-winners-list');
            if (setupWinnersList) {
                setupWinnersList.innerHTML = '';
                document.getElementById('setup-winners-count').innerText = drawnBallsHistory.length;
                if (drawnBallsHistory.length === 0) {
                    setupWinnersList.innerHTML = '<div class="text-slate-500 text-center py-10 text-sm font-medium"><i class="fa-solid fa-inbox text-2xl mb-2 block opacity-50"></i>Aï¿½n no hay ganadores registrados.</div>';
                } else {
                    drawnBallsHistory.forEach((h, index) => {
                        const row = document.createElement('div');
                        row.className = "flex items-center justify-between p-3.5 bg-slate-800/60 hover:bg-slate-700/60 rounded-xl border border-slate-700/80 shadow-sm transition-colors mb-2";
                        const isDelivered = h.delivered ? 'checked' : '';
                        
                        const pColor = h.p ? h.p.color : (h.color || '#00a0e9');
                        const pFace = h.p ? h.p.face : (h.face || 'happy');
                        const pName = h.p ? h.p.name : (h.name || 'Desconocido');
                        const pId = h.p ? h.p.display_id : (h.display_id || '--');
                        const pPrize = h.prize && typeof h.prize === 'object' ? h.prize.name : (h.prize || 'Sorteo');

                        row.innerHTML = `
                            <div class="flex items-center justify-between w-full gap-4">
                                <div class="flex items-center gap-4 overflow-hidden flex-grow">
                                    <div class="w-12 h-12 rounded-full flex-shrink-0 overflow-hidden bg-slate-900/50 flex items-center justify-center border-2 border-slate-700 shadow-inner">
                                        <div class="w-[120%] h-[120%] mt-2 ml-1" style="transform: scale(0.8)">
                                            ${generateMiiSVG(pColor, pFace)}
                                        </div>
                                    </div>
                                    <div class="flex flex-col truncate">
                                        <span class="font-bold text-slate-100 text-[15px] tracking-wide truncate" title="${pName}">${pName} <span class="text-[11px] text-slate-400 font-medium ml-2">ID #${pId}</span></span>
                                        <span class="text-xs text-amber-400 font-bold truncate mt-1 tracking-wide"><i class="fa-solid fa-gift mr-1.5 opacity-80"></i>${pPrize}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center flex-shrink-0 gap-2">
                                    <div class="cursor-pointer group flex items-center gap-2.5 bg-slate-900/40 hover:bg-slate-900/80 border border-slate-700/80 px-3.5 py-2 rounded-lg transition-colors select-none shadow-inner" onclick="confirmToggleDelivery(event, ${index})">
                                        <span class="text-[10px] font-bold ${h.delivered ? 'text-emerald-400' : 'text-slate-400 group-hover:text-slate-300'} uppercase tracking-wider transition-colors">Entregado</span>
                                        <div class="relative w-8 h-4 rounded-full transition-colors ${h.delivered ? 'bg-emerald-500/30 border border-emerald-500/50' : 'bg-slate-800 border border-slate-600'}">
                                            <div class="absolute top-[1px] left-[1px] w-3 h-3 rounded-full transition-transform duration-300 ease-out ${h.delivered ? 'transform translate-x-4 bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)]' : 'bg-slate-400'}"></div>
                                        </div>
                                    </div>
                                    <button onclick="confirmRevertWinner(event, ${index})" class="w-9 h-9 rounded-lg bg-slate-900/40 hover:bg-red-500/20 text-slate-500 hover:text-red-400 border border-slate-700 hover:border-red-500/50 transition-all flex items-center justify-center font-bold" title="Revertir premio (devolver al sorteo)">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                        setupWinnersList.appendChild(row);
                    });
                }
            }

            updateHistories();
        }

        // Agregamos la funciï¿½n de confirmaciï¿½n
        function confirmToggleDeliveryByPrize(event, prizeId) {
            event.preventDefault();
            const pr = prizes.find(p => p.id == prizeId);
            if (!pr) return;

            let hIndex = drawnBallsHistory.findIndex(h => {
                if (pr.canje_id && h.canje_id) {
                    return h.canje_id == pr.canje_id;
                }
                const hPrizeName = (typeof h.prize === 'object' && h.prize) ? h.prize.name : h.prize;
                const hWinnerName = h.p ? h.p.name : (h.name || '');
                return hPrizeName === pr.name && (hWinnerName === pr.winner || h.display_id == pr.winner_id);
            });

            if (hIndex !== -1) {
                confirmToggleDelivery(event, hIndex);
            }
        }

        function confirmRevertWinnerByPrize(event, prizeId) {
            event.preventDefault();
            const pr = prizes.find(p => p.id == prizeId);
            if (!pr) return;

            let hIndex = drawnBallsHistory.findIndex(h => {
                if (pr.canje_id && h.canje_id) {
                    return h.canje_id == pr.canje_id;
                }
                const hPrizeName = (typeof h.prize === 'object' && h.prize) ? h.prize.name : h.prize;
                const hWinnerName = h.p ? h.p.name : (h.name || '');
                return hPrizeName === pr.name && (hWinnerName === pr.winner || h.display_id == pr.winner_id);
            });

            if (hIndex !== -1) {
                confirmRevertWinner(event, hIndex);
            }
        }

        function confirmToggleDelivery(event, index) {
            // Evitamos que el checkbox cambie su estado automï¿½ticamente
            event.preventDefault();
            
            const h = drawnBallsHistory[index];
            if (!h) return;
            
            const isDelivered = h.delivered;
            const actionText = isDelivered ? '<span class="text-red-400">Desmarcar</span>' : '<span class="text-emerald-400">Marcar</span>';
            const statusText = isDelivered ? 'NO ENTREGADO' : 'ENTREGADO';
            const pName = h.p ? h.p.name : (h.name || 'Desconocido');
            const pPrize = h.prize && typeof h.prize === 'object' ? h.prize.name : (h.prize || 'el premio');

            Swal.fire({
                title: 'ï¿½Confirmar entrega?',
                html: `ï¿½Deseas ${actionText} como <b>${statusText}</b> el premio <b>${pPrize}</b> de <b>${pName}</b>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sï¿½, confirmar',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#f8fafc',
                customClass: {
                    popup: 'border border-slate-700 rounded-2xl shadow-2xl',
                    title: 'text-white',
                    htmlContainer: 'text-slate-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const newState = !h.delivered;
                    h.delivered = newState;
                    const hPrizeName = (typeof h.prize === 'object' && h.prize) ? h.prize.name : (h.prize || '');
                    const matchingPrize = prizes.find(p => {
                        if (h.canje_id && p.canje_id) return p.canje_id == h.canje_id;
                        return p.name === hPrizeName && (p.winner === (h.p ? h.p.name : h.name) || p.winner_id == h.display_id);
                    });
                    if (matchingPrize) {
                        matchingPrize.delivered = newState;
                    }
                    updateUI(); // Se repinta la UI completa optimï¿½sticamente

                    if (h.canje_id) {
                        fetch(`{{ route('eventos.sorteo.toggle-delivery', $evento) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                canje_id: h.canje_id,
                                delivered: newState
                            })
                        }).catch(e => console.error(e));
                    }
                }
            });
        }

        function confirmRevertWinner(event, index) {
            event.preventDefault();
            const h = drawnBallsHistory[index];
            if (!h) return;
            
            const pName = h.p ? h.p.name : (h.name || 'Desconocido');
            const pPrize = h.prize && typeof h.prize === 'object' ? h.prize.name : (h.prize || 'el premio');

            Swal.fire({
                title: 'ï¿½Revertir premio?',
                html: `ï¿½Estï¿½s seguro de que quieres anular el premio <b>${pPrize}</b> a <b>${pName}</b>? <br><br> <span class="text-xs text-slate-400">El premio volverï¿½ a estar disponible para sortear.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sï¿½, revertir',
                cancelButtonText: 'Cancelar',
                background: '#1e293b',
                color: '#f8fafc',
                customClass: {
                    popup: 'border border-slate-700 rounded-2xl shadow-2xl',
                    title: 'text-white',
                    htmlContainer: 'text-slate-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (h.canje_id) {
                        fetch(`{{ route('eventos.sorteo.revertir-ganador', $evento) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ canje_id: h.canje_id })
                        }).then(r => r.json()).then(data => {
                            if (data.ok) {
                                // Devolver participante a la lista activa
                                // Recargar la pï¿½gina es la manera mï¿½s segura de asegurar que los premios disponibles se actualicen en JS
                                // Pero si queremos hacerlo con SPA:
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Revertido',
                                    text: 'Premio devuelto al sorteo. Recargando...',
                                    background: '#1e293b', color: '#f8fafc',
                                    timer: 1500, showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('Error', 'No se pudo revertir', 'error');
                            }
                        }).catch(e => console.error(e));
                    } else {
                        // Si no hay canje_id, solo lo quitamos localmente (aunque no deberï¿½a pasar)
                        drawnBallsHistory.splice(index, 1);
                        saveToStorage();
                        updateUI();
                        window.location.reload();
                    }
                }
            });
        }

        // Historial de la vista de Tï¿½mbola (Derecha en main)
        function updateHistories() {
            const tombolaHistEl = document.getElementById('tombola-history');
            if (!tombolaHistEl) return;
            
            tombolaHistEl.innerHTML = '';
            if (drawnBallsHistory.length === 0) {
                tombolaHistEl.innerHTML = '<p class="text-gray-400 text-sm py-1">No se han extraï¿½do ganadores todavï¿½a.</p>';
            } else {
                drawnBallsHistory.forEach(b => {
                    const badge = document.createElement('div');
                    badge.className = "flex items-center gap-2 bg-sky-100 border-2 border-sky-300 text-sky-800 px-3 py-1.5 rounded-full font-bold text-sm whitespace-nowrap flex-shrink-0 animate-fade-in";
                    badge.innerHTML = `
                        <div class="w-4 h-4 rounded-full" style="background-color: ${b.color}"></div>
                        <span>?? ${b.name} (${b.prize})</span>
                    `;
                    tombolaHistEl.appendChild(badge);
                });
            }
        }

        // --- ACCIONES DE GESTIï¿½N (Aï¿½adir/Eliminar) ---
        function saveCustomMii() {
            playSoundClick();
            const nameInput = document.getElementById('input-mii-name');
            const name = nameInput.value.trim();
            if (!name) return;

            const color = document.getElementById('select-mii-color').value;
            const face = document.getElementById('select-mii-face').value;

            const newMii = {
                id: 'char_' + Date.now(),
                display_id: Math.floor(Math.random() * 9000 + 1000),
                name: name,
                color: color,
                face: face
            };

            participants.push(newMii);
            nameInput.value = '';
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        function addMiiRandom() {
            playSoundClick();
            const names = ['Felipe', 'Lucï¿½a', 'Nicolï¿½s', 'Martina', 'Gastï¿½n', 'Sofï¿½a', 'Sandro', 'Estela', 'Charly', 'Renata', 'Mateo', 'Valentina'];
            const randomName = names[Math.floor(Math.random() * names.length)] + " " + Math.floor(Math.random()*90 + 10);
            const colors = ['#00a0e9', '#ff9500', '#76c336', '#e60012', '#e91e63', '#9c27b0', '#ffeb3b'];
            const faces = ['happy', 'cool', 'excited', 'surprised'];

            const newMii = {
                id: 'char_' + Date.now(),
                display_id: Math.floor(Math.random() * 9000 + 1000),
                name: randomName,
                color: colors[Math.floor(Math.random() * colors.length)],
                face: faces[Math.floor(Math.random() * faces.length)]
            };

            participants.push(newMii);
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        function addMultipleRandomMiis(amount) {
            playSoundClick();
            const names = ['Felipe', 'Lucï¿½a', 'Nicolï¿½s', 'Martina', 'Gastï¿½n', 'Sofï¿½a', 'Sandro', 'Estela', 'Charly', 'Renata', 'Mateo', 'Valentina', 'Alex', 'Juan', 'Maria', 'Pedro', 'Ana'];
            const colors = ['#00a0e9', '#ff9500', '#76c336', '#e60012', '#e91e63', '#9c27b0', '#ffeb3b'];
            const faces = ['happy', 'cool', 'excited', 'surprised'];

            for (let i = 0; i < amount; i++) {
                const randomName = names[Math.floor(Math.random() * names.length)] + " " + Math.floor(Math.random()*900 + 100);
                const newMii = {
                    id: 'char_' + Date.now() + '_' + i,
                    display_id: Math.floor(Math.random() * 90000 + 10000),
                    name: randomName,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    face: faces[Math.floor(Math.random() * faces.length)]
                };
                participants.push(newMii);
            }

            saveToStorage();
            updateUI();
            initTombolaPhysics();
            showCustomToast(`ï¿½Aï¿½adidos!`, `Se agregaron ${amount} participantes de prueba al instante.`);
        }

        function removeParticipant(id) {
            playSoundClick();
            participants = participants.filter(p => p.id !== id);
            saveToStorage();
            updateUI();
            initTombolaPhysics();
        }

        function savePrize() {
            playSoundClick();
            const nameInput = document.getElementById('input-prize-name');
            const name = nameInput.value.trim();
            if (!name) return;

            const color = document.getElementById('input-prize-color').value;
            const type = document.getElementById('input-prize-type').value;

            const newPrize = {
                id: 'p_' + Date.now(),
                name: name,
                color: color,
                type: type
            };

            prizes.push(newPrize);
            nameInput.value = '';
            saveToStorage();
            updateUI();
        }

        function removePrize(id) {
            playSoundClick();
            prizes = prizes.filter(p => p.id !== id);
            saveToStorage();
            updateUI();
        }
    </script>

    <script>
        // --- SISTEMA Fï¿½SICO DE LA Tï¿½MBOLA EN CANVAS ---
        const tombolaCanvas = document.getElementById('tombola-canvas');
        const tombolaCtx = tombolaCanvas.getContext('2d');
        let tombolaBalls = [];
        let tombolaAngle = 0;
        let tombolaSpeed = 0;
        let isSpinningTombola = false;
        let animationFrameId = null;
        let droppingBallObj = null;

        // Escala dinï¿½mica
        let tombolaScale = 1;
        let tombolaOffsetX = 0;
        let tombolaOffsetY = 0;

        function resizeTombolaCanvas() {
            const rect = tombolaCanvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            
            tombolaCanvas.width = rect.width * dpr;
            tombolaCanvas.height = rect.height * dpr;
            
            const scaleX = tombolaCanvas.width / 400;
            const scaleY = tombolaCanvas.height / 350;
            tombolaScale = Math.min(scaleX, scaleY);
            
            tombolaOffsetX = (tombolaCanvas.width - (400 * tombolaScale)) / 2;
            tombolaOffsetY = (tombolaCanvas.height - (350 * tombolaScale)) / 2;
        }

        const resizeObserver = new ResizeObserver(() => {
            resizeTombolaCanvas();
        });
        resizeObserver.observe(tombolaCanvas);

        class PhysicalBall {
            constructor(name, color, face, index, displayId) {
                this.name = name;
                this.color = color;
                this.face = face;
                this.index = index;
                this.displayId = displayId;
                this.radius = 12;
                
                const rMax = 100;
                const angle = Math.random() * Math.PI * 2;
                const dist = Math.random() * rMax;
                
                this.x = 200 + Math.cos(angle) * dist;
                this.y = 175 + Math.sin(angle) * dist;
                
                this.vx = (Math.random() - 0.5) * 4;
                this.vy = (Math.random() - 0.5) * 4;
            }

            update(cageRotation, centerX, centerY, cageRadius) {
                // Fuerza de gravedad constante hacia abajo
                this.vy += 0.25;
                
                // Rotaciï¿½n impulsada por el tambor (si la bola toca las paredes)
                const dx = this.x - centerX;
                const dy = this.y - centerY;
                const dist = Math.sqrt(dx * dx + dy * dy);
                
                // Efecto de centrifugado suave y arrastre
                if (dist > cageRadius - this.radius - 5) {
                    const angle = Math.atan2(dy, dx);
                    this.vx += Math.cos(angle + Math.PI/2) * cageRotation * 2.5;
                    this.vy += Math.sin(angle + Math.PI/2) * cageRotation * 2.5;
                }

                // Fricciï¿½n del aire
                this.vx *= 0.99;
                this.vy *= 0.99;

                this.x += this.vx;
                this.y += this.vy;

                // Colisiï¿½n con la pared circular exterior
                const dx2 = this.x - centerX;
                const dy2 = this.y - centerY;
                const dist2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                if (dist2 > cageRadius - this.radius) {
                    const angle = Math.atan2(dy2, dx2);
                    this.x = centerX + Math.cos(angle) * (cageRadius - this.radius);
                    this.y = centerY + Math.sin(angle) * (cageRadius - this.radius);
                    
                    const dotProduct = this.vx * Math.cos(angle) + this.vy * Math.sin(angle);
                    this.vx -= 1.8 * dotProduct * Math.cos(angle);
                    this.vy -= 1.8 * dotProduct * Math.sin(angle);
                }
            }

            updateDrop() {
                // Efecto 3D: La bola crece hacia la pantalla
                this.radius += (45 - this.radius) * 0.12;

                this.vy += 0.8; // Gravedad
                this.x += this.vx;
                this.y += this.vy;
                
                // Rebotar en el suelo, respetando su radio actual
                const floorY = 345 - this.radius;
                if (this.y > floorY) {
                    this.y = floorY;
                    this.vy *= -0.65; // Rebote elï¿½stico
                    this.vx *= 0.96;  // Fricciï¿½n horizontal
                }
            }

            drawDrop(ctx) {
                // Sombra de piso proyectada en perspectiva 3D
                const floorY = 345 - this.radius;
                const distToFloor = Math.max(0, floorY - this.y);
                const shadowScale = Math.max(0.1, 1 - distToFloor / 180);
                
                ctx.save();
                ctx.beginPath();
                ctx.translate(this.x, 345);
                ctx.scale(1, 0.25);
                ctx.arc(0, 0, this.radius * shadowScale * 1.3, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(0,0,0,0.35)";
                ctx.fill();
                ctx.restore();

                // Dibuja la bola con su escala actual
                this.draw(ctx);
            }

            draw(ctx) {
                // Sombra interna de la bola (iluminaciï¿½n)
                ctx.beginPath();
                ctx.arc(this.x + this.radius*0.15, this.y + this.radius*0.15, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(0,0,0,0.15)";
                ctx.fill();

                // Esfera principal
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();

                ctx.strokeStyle = "rgba(0,0,0,0.2)";
                ctx.lineWidth = this.radius * 0.12;
                ctx.stroke();

                // Brillo de luz superior izquierdo de la bola
                ctx.beginPath();
                ctx.arc(this.x - this.radius*0.15, this.y - this.radius*0.15, this.radius * 0.5, 0, Math.PI * 2);
                ctx.fillStyle = "#ffffff";
                ctx.fill();

                // Nï¿½mero de bola pintado en el centro, escalado con el radio
                ctx.fillStyle = "#1e293b";
                ctx.font = "bold " + Math.max(9, Math.round(this.radius * 0.75)) + "px 'Fredoka'";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(this.displayId, this.x - this.radius*0.15, this.y - this.radius*0.15);
            }
        }

        function initTombolaPhysics() {
            resizeTombolaCanvas();
            tombolaBalls = [];
            
            // Para evitar que el navegador colapse con la fï¿½sica O(n^2) de 1000+ colisiones,
            // limitamos la cantidad mï¿½xima de bolas que se dibujan fï¿½sicamente dentro del tambor.
            // (Nota: 75 bolas llenan visualmente el tambor ampliado dejï¿½ndoles suficiente espacio para rebotar)
            const maxVisibleBalls = 75;
            
            // Mezclamos un poco visualmente para que las bolas "visibles" sean variadas
            const shuffled = [...participants].sort(() => 0.5 - Math.random());
            const participantsToRender = shuffled.slice(0, maxVisibleBalls);

            participantsToRender.forEach((p, index) => {
                tombolaBalls.push(new PhysicalBall(p.name, p.color, p.face, index, p.display_id || (index + 1)));
            });
        }

        function resolveBallCollisions() {
            for (let i = 0; i < tombolaBalls.length; i++) {
                for (let j = i + 1; j < tombolaBalls.length; j++) {
                    const b1 = tombolaBalls[i];
                    const b2 = tombolaBalls[j];

                    const dx = b2.x - b1.x;
                    const dy = b2.y - b1.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    const minDist = b1.radius + b2.radius;

                    if (dist < minDist) {
                        const overlap = minDist - dist;
                        const nx = dx / (dist || 1);
                        const ny = dy / (dist || 1);

                        // Resolver interpenetraciï¿½n suavemente (Soft body relaxation para evitar temblores/jittering)
                        // En lugar de moverlos al 100% (0.5 cada uno), aplicamos un factor de relajaciï¿½n (0.15)
                        const relaxation = 0.15;
                        b1.x -= nx * overlap * relaxation;
                        b1.y -= ny * overlap * relaxation;
                        b2.x += nx * overlap * relaxation;
                        b2.y += ny * overlap * relaxation;

                        // Transferencia elï¿½stica de impulsos vectoriales
                        const kx = b1.vx - b2.vx;
                        const ky = b1.vy - b2.vy;
                        const p = 2 * (nx * kx + ny * ky) / 2;

                        b1.vx -= p * nx * 0.85;
                        b1.vy -= p * ny * 0.85;
                        b2.vx += p * nx * 0.85;
                        b2.vy += p * ny * 0.85;
                    }
                }
            }
        }

        const programLogoImg = new Image();
        programLogoImg.src = "{{ asset(\App\Models\Apariencia::getConfig()['logo_path'] ?? 'Icono.png') }}";

        let debugTick = 0;
        function animateTombola() {
            try {
                debugTick++;
                tombolaCtx.clearRect(0, 0, tombolaCanvas.width, tombolaCanvas.height);
                
                tombolaCtx.save();
                tombolaCtx.translate(tombolaOffsetX, tombolaOffsetY);
                tombolaCtx.scale(tombolaScale, tombolaScale);

            const centerX = 200;
            const centerY = 165;
            const cageRadius = 150;

            // Dibujar soporte metï¿½lico inferior
            tombolaCtx.strokeStyle = "#adb9be";
            tombolaCtx.lineWidth = 14;
            tombolaCtx.lineCap = "round";
            
            tombolaCtx.beginPath();
            tombolaCtx.moveTo(centerX - 165, centerY + 160);
            tombolaCtx.lineTo(centerX - 110, centerY);
            tombolaCtx.lineTo(centerX + 110, centerY);
            tombolaCtx.lineTo(centerX + 165, centerY + 160);
            tombolaCtx.stroke();

            tombolaCtx.beginPath();
            tombolaCtx.moveTo(centerX - 185, centerY + 160);
            tombolaCtx.lineTo(centerX + 185, centerY + 160);
            tombolaCtx.lineWidth = 8;
            tombolaCtx.stroke();

            // Lï¿½gica de aceleraciï¿½n/fricciï¿½n al girar
            if (isSpinningTombola) {
                tombolaSpeed += 0.02;
                if (tombolaSpeed > 0.4) tombolaSpeed = 0.4;
            } else {
                tombolaSpeed *= 0.95;
            }
            tombolaAngle += tombolaSpeed;

            resolveBallCollisions();

            tombolaBalls.forEach(ball => {
                ball.update(tombolaSpeed, centerX, centerY, cageRadius);
                ball.draw(tombolaCtx);
            });

            // Dibujar esfera/rejilla del tambor giratorio
            tombolaCtx.save();
            tombolaCtx.translate(centerX, centerY);
            tombolaCtx.rotate(tombolaAngle);

            tombolaCtx.strokeStyle = "#80929a";
            tombolaCtx.lineWidth = 6;
            tombolaCtx.beginPath();
            tombolaCtx.arc(0, 0, cageRadius, 0, Math.PI * 2);
            tombolaCtx.stroke();

            // Rayos del tambor metï¿½lico
            tombolaCtx.strokeStyle = "rgba(128,146,154,0.45)";
            tombolaCtx.lineWidth = 2.5;
            const spokes = 16;
            for (let i = 0; i < spokes; i++) {
                const angle = (i * Math.PI * 2) / spokes;
                tombolaCtx.beginPath();
                tombolaCtx.moveTo(0, 0);
                tombolaCtx.lineTo(Math.cos(angle) * cageRadius, Math.sin(angle) * cageRadius);
                tombolaCtx.stroke();
            }

            // Plaqueta central del Bingo
            tombolaCtx.fillStyle = "#ffffff";
            tombolaCtx.beginPath();
            tombolaCtx.arc(0, 0, 32, 0, Math.PI * 2);
            tombolaCtx.fill();
            tombolaCtx.strokeStyle = "#00a0e9";
            tombolaCtx.lineWidth = 3;
            tombolaCtx.stroke();

            if (programLogoImg.complete && programLogoImg.naturalWidth !== 0) {
                const logoSize = 48; // Tamaï¿½o ajustado para caber en el centro de 64px de diï¿½metro
                tombolaCtx.drawImage(programLogoImg, -logoSize/2, -logoSize/2, logoSize, logoSize);
            } else {
                tombolaCtx.fillStyle = "#5b5b5b";
                tombolaCtx.font = "bold 13px 'Fredoka'";
                tombolaCtx.textAlign = "center";
                tombolaCtx.textBaseline = "middle";
                tombolaCtx.fillText("BINGO", 0, -4);
                tombolaCtx.font = "bold 8px 'Fredoka'";
                tombolaCtx.fillStyle = "#00a0e9";
                tombolaCtx.fillText("SPORTS", 0, 8);
            }

            tombolaCtx.restore();

            // Perno central
            tombolaCtx.fillStyle = "#0087c4";
            tombolaCtx.beginPath();
            tombolaCtx.arc(centerX, centerY, 8, 0, Math.PI * 2);
            tombolaCtx.fill();

            // Manivela lateral giratoria
            tombolaCtx.save();
            tombolaCtx.translate(centerX, centerY);
            tombolaCtx.rotate(tombolaAngle);
            
            tombolaCtx.strokeStyle = "#3a4a50";
            tombolaCtx.lineWidth = 8;
            tombolaCtx.beginPath();
            tombolaCtx.moveTo(0, 0);
            tombolaCtx.lineTo(180, 0);
            tombolaCtx.lineTo(180, 45);
            tombolaCtx.stroke();

            tombolaCtx.fillStyle = "#f47c20";
            tombolaCtx.beginPath();
            tombolaCtx.arc(180, 45, 14, 0, Math.PI * 2);
            tombolaCtx.fill();
            tombolaCtx.restore();
            
            // Dibujar la bola cayendo (bola ganadora extraï¿½da con efecto 3D)
            if (droppingBallObj) {
                droppingBallObj.updateDrop();
                droppingBallObj.drawDrop(tombolaCtx);
            }

            tombolaCtx.restore(); // Restore global scale and translate

            } catch (err) {
                console.error("Tombola Animation Error:", err);
                tombolaCtx.restore();
                tombolaCtx.fillStyle = 'red';
                tombolaCtx.font = '14px Arial';
                tombolaCtx.fillText("Error: " + err.message, 10, 50);
            }

            animationFrameId = requestAnimationFrame(animateTombola);
        }

        function spinTombolaManual() {
            playSoundClick();
            if (isSpinningTombola) {
                isSpinningTombola = false;
                playSoundRoll(false);
                document.getElementById('btn-spin-tombola').innerHTML = '<i class="fa-solid fa-arrows-spin"></i><span>MEZCLAR BOLAS</span>';
            } else {
                if (participants.length === 0) {
                    showCustomToast("ï¿½No hay participantes!", "Agrega algunos personajes en el panel de Ajustes.");
                    return;
                }
                isSpinningTombola = true;
                playSoundRoll(true);
                document.getElementById('btn-spin-tombola').innerHTML = '<i class="fa-solid fa-pause"></i><span>DETENER MEZCLA</span>';
                
                setTimeout(() => {
                    if (isSpinningTombola) {
                        isSpinningTombola = false;
                        playSoundRoll(false);
                        const btn = document.getElementById('btn-spin-tombola');
                        if (btn) btn.innerHTML = '<i class="fa-solid fa-arrows-spin"></i><span>MEZCLAR BOLAS</span>';
                    }
                }, 1800);
            }
        }
    </script>

    <script>
        // --- EXTRACCIï¿½N Y REVELACIï¿½N DEL GANADOR ---
        function drawBall() {
            playSoundClick();
            if (participants.length === 0) {
                showCustomToast("ï¿½No hay bolas!", "Introduce participantes para jugar.");
                return;
            }

            const drawBtn = document.getElementById('btn-draw-ball');
            drawBtn.disabled = true;
            drawBtn.classList.add('opacity-50');

            // Determinar el premio antes de empezar
            // Filtramos premios que son para 'sorteo' y que aï¿½n no tengan ganador
            let availablePrizes = prizes.filter(p => (p.type || 'sorteo') === 'sorteo' && !p.winner);
            
            let winningPrizeName = "ï¿½Premio Sorpresa!";
            let prizeIndex = -1;
            
            if (availablePrizes.length > 0) {
                // Elegir el primer premio disponible (en el orden actual de la lista)
                const selectedPrize = availablePrizes[0];
                
                // Encontrar su ï¿½ndice real en el array 'prizes' global para actualizarlo despuï¿½s
                prizeIndex = prizes.findIndex(p => p.id === selectedPrize.id);
                winningPrizeName = prizes[prizeIndex].name;
            }

            // Lanzar el anuncio de premio
            showPrizeAnnouncement(winningPrizeName, () => {
                executeDrawSequence(winningPrizeName, prizeIndex);
            });
        }

        function showPrizeAnnouncement(prizeName, callback) {
            const modal = document.getElementById('prize-announcement-modal');
            const banner = document.getElementById('prize-announcement-banner');
            document.getElementById('announcement-prize-name').textContent = prizeName;
            
            modal.classList.remove('hidden');
            
            // Subida de suspenso tipo notificaciï¿½n
            setTimeout(() => {
                playSoundClick();
                banner.classList.remove('scale-y-0', 'opacity-0');
                banner.classList.add('scale-y-100', 'opacity-100');
            }, 50);

            // Mantener en pantalla y luego ocultar para empezar el sorteo
            setTimeout(() => {
                banner.classList.remove('scale-y-100', 'opacity-100');
                banner.classList.add('scale-y-0', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    callback();
                }, 400); // tiempo de salida
            }, 2500); // 2.5 segundos de visualizaciï¿½n
        }

        function executeDrawSequence(winningPrizeName, prizeIndex) {
            isSpinningTombola = true;
            playSoundRoll(true);

            setTimeout(() => {
                isSpinningTombola = false;
                playSoundRoll(false);

                const winnerIndex = Math.floor(Math.random() * participants.length);
                const winnerMii = participants[winnerIndex];

                // Asignar el ganador al premio
                if (prizeIndex !== -1) {
                    prizes[prizeIndex].winner = winnerMii.name;
                    prizes[prizeIndex].winner_id = winnerMii.display_id;
                }

                // Remover TODOS los boletos de este participante para que no pueda volver a salir en la ruleta
                participants = participants.filter(p => p.display_id !== winnerMii.display_id);

                // Instanciar la bola cayendo
                const winningBallIndex = tombolaBalls.findIndex(b => b.displayId === winnerMii.display_id);
                if (winningBallIndex !== -1) {
                    droppingBallObj = tombolaBalls[winningBallIndex];
                    tombolaBalls.splice(winningBallIndex, 1);
                } else {
                    // Si la bola no estaba renderizada por el lï¿½mite de mï¿½ximo de bolas, 
                    // creamos una instancia fresca de la nada solo para la animaciï¿½n de expulsiï¿½n
                    droppingBallObj = new PhysicalBall(
                        winnerMii.name, 
                        winnerMii.color, 
                        winnerMii.face, 
                        winnerIndex, 
                        winnerMii.display_id || (winnerIndex + 1)
                    );
                }

                // Posicionarla en el centro para expulsarla hacia el frente
                droppingBallObj.x = 200;
                droppingBallObj.y = 165; // Centro del tambor
                droppingBallObj.vx = (Math.random() - 0.5) * 10; // Impulso lateral mï¿½s fuerte
                droppingBallObj.vy = -12; // Salto alto simulando que es lanzada hacia afuera
                
                playSoundBallDrop();

                const newHistoryItem = {
                    name: winnerMii.name,
                    color: winnerMii.color,
                    face: winnerMii.face,
                    display_id: winnerMii.display_id,
                    prize: winningPrizeName,
                    delivered: false
                };
                drawnBallsHistory.unshift(newHistoryItem);

                // Registrar ganador en Base de Datos
                fetch(`{{ route('eventos.sorteo.ganador', $evento) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        participante_id: winnerMii.display_id,
                        premio_id: prizes[prizeIndex].id
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.canje_id) {
                        newHistoryItem.canje_id = data.canje_id;
                    }
                })
                .catch(e => console.error(e));

                // Esperar a que termine la animaciï¿½n de caï¿½da antes de mostrar la celebraciï¿½n
                setTimeout(() => {
                    droppingBallObj = null; // Quitarla de la pantalla principal
                    const drawBtn = document.getElementById('btn-draw-ball');
                    drawBtn.disabled = false;
                    drawBtn.classList.remove('opacity-50');

                    // Iniciar la cinemï¿½tica integrada de la bola abriï¿½ndose
                    triggerSphereOpeningAnimation(winnerMii, winningPrizeName, winnerIndex);
                    updateUI();
                }, 1800);
                
            }, 1800); // Tiempo girando la tï¿½mbola
        }

        // Ejecuciï¿½n de la cinemï¿½tica detallada de la esfera fï¿½sica del bingo
        function triggerSphereOpeningAnimation(winnerMii, winningPrizeName, winnerIndex) {
            currentAnimWinnerMii = winnerMii;
            currentAnimWinningPrizeName = winningPrizeName;

            const wrapper = document.getElementById('sphere-wrapper');
            const topHalf = document.getElementById('sphere-top');
            const bottomHalf = document.getElementById('sphere-bottom');
            const paper = document.getElementById('drawn-paper');
            const actionBtn = document.getElementById('btn-proceed-celebration');
            const strikeBanner = document.getElementById('victory-strike-banner');
            const titleEl = document.getElementById('modal-sphere-title');

            // Resetear estados del banner de victoria horizontal
            strikeBanner.classList.add('scale-y-0', 'opacity-0');
            strikeBanner.classList.remove('scale-y-100', 'opacity-100');

            // Resetear contenedores de la esfera
            wrapper.className = "relative w-80 h-80 flex items-center justify-center mb-24";
            topHalf.className = "absolute inset-0 z-20 pointer-events-none transition-transform duration-500";
            bottomHalf.className = "absolute inset-0 z-10 pointer-events-none transition-transform duration-500";
            paper.className = "absolute w-72 bg-gradient-to-b from-amber-50 to-orange-50 border-4 border-amber-400 rounded-2xl shadow-2xl p-6 flex flex-col items-center justify-center opacity-0 transform origin-top";
            
            actionBtn.classList.add('opacity-0', 'translate-y-4');
            actionBtn.classList.remove('opacity-100', 'translate-y-0');

            titleEl.textContent = "ï¿½BOLA SELECCIONADA!";

            // Color e ï¿½ndice de la bola fï¿½sica
            const sphereColor = winnerMii.color;
            const displayId = winnerMii.display_id || (winnerIndex + 1);
            document.getElementById('svg-sphere-top-bg').setAttribute('fill', sphereColor);
            document.getElementById('svg-sphere-bottom-bg').setAttribute('fill', sphereColor);
            document.getElementById('svg-sphere-top-num').textContent = displayId;
            document.getElementById('svg-sphere-bottom-num').textContent = displayId;

            // Cargar datos en el pergamino de papel interno
            document.getElementById('paper-winner-name').textContent = winnerMii.name;
            document.getElementById('paper-winner-num').textContent = `ID #${displayId}`;
            document.getElementById('paper-winner-prize').textContent = winningPrizeName;
            document.getElementById('paper-mii-mini').innerHTML = generateMiiSVG(winnerMii.color, 'excited');

            // Cargar datos en el banner horizontal de fondo
            document.getElementById('strike-winner-name').textContent = winnerMii.name;
            document.getElementById('strike-prize-name').textContent = winningPrizeName;

            // Desplegar modal
            document.getElementById('sphere-opening-modal').classList.remove('hidden');

            // --- SECUENCIA CRONOLï¿½GICA DE ANIMACIï¿½N ---
            
            // 1. Caï¿½da gravitatoria y rebote elï¿½stico de la bola en pantalla
            wrapper.classList.add('animate-sphere-bounce');
            playSoundBallDrop();

            // 2. Comienza a temblar ruidosamente (suspenso)
            setTimeout(() => {
                wrapper.classList.remove('animate-sphere-bounce');
                wrapper.classList.add('animate-sphere-shake');
                playSoundRumble();
            }, 750);

            // 3. ï¿½Se abre fï¿½sicamente la cï¿½psula!
            setTimeout(() => {
                wrapper.classList.remove('animate-sphere-shake');
                topHalf.classList.add('animate-split-top');
                bottomHalf.classList.add('animate-split-bottom');
                playSoundBallPop();
            }, 1650);

            // 4. Se desenrolla el papel de premio revelando el avatar del ganador
            setTimeout(() => {
                paper.classList.add('animate-paper-unroll');
                playSoundRustle();
            }, 1900);

            // 5. ï¿½EXPLOSIï¿½N DE FIESTA! Se despliega el banner de victoria y llueve confeti
            setTimeout(() => {
                strikeBanner.classList.remove('scale-y-0', 'opacity-0');
                strikeBanner.classList.add('scale-y-100', 'opacity-100');
                playSoundVictory();
                triggerConfettiRain();
            }, 2200);

            // 6. Aparece botï¿½n de confirmaciï¿½n
            setTimeout(() => {
                actionBtn.classList.remove('opacity-0', 'translate-y-4');
                actionBtn.classList.add('opacity-100', 'translate-y-0');
            }, 2900);
        }

        let confettiInterval = null;

        // Genera rï¿½fagas fï¿½sicas de confeti cayendo continuamente
        function triggerConfettiRain() {
            const confettiContainer = document.getElementById('modal-confetti');
            confettiContainer.innerHTML = '';
            
            const dropBatch = () => {
                for (let i = 0; i < 12; i++) {
                    const flake = document.createElement('div');
                    flake.className = "absolute rounded-full pointer-events-none z-0";
                    const size = Math.random() * 10 + 6;
                    const left = Math.random() * 100;
                    const top = Math.random() * 20;
                    const color = ['#ff9500', '#00a0e9', '#76c336', '#e60012', '#ffeb3b'][Math.floor(Math.random()*5)];
                    
                    flake.style.width = `${size}px`;
                    flake.style.height = `${size}px`;
                    flake.style.backgroundColor = color;
                    flake.style.left = `${left}%`;
                    flake.style.top = `-${top}px`;
                    flake.style.opacity = Math.random() * 0.5 + 0.5;
                    flake.style.transform = `rotate(${Math.random()*360}deg)`;
                    
                    const duration = Math.random() * 2.5 + 2.0;
                    flake.style.transition = `top ${duration}s linear, transform ${duration}s ease-in-out`;
                    confettiContainer.appendChild(flake);

                    // Fuerza fï¿½sica simulada de gravedad para la caï¿½da
                    setTimeout(() => {
                        flake.style.top = '105%';
                        flake.style.transform = `rotate(${Math.random()*720}deg) translateX(${Math.random()*100 - 50}px)`;
                    }, 50);

                    // Limpieza al terminar la caï¿½da para que no sature la memoria
                    setTimeout(() => {
                        if (flake.parentNode) flake.remove();
                    }, duration * 1000 + 100);
                }
            };

            // Disparo fuerte inicial
            for(let k=0; k<4; k++) dropBatch();
            
            // Bucle infinito de caï¿½da
            if (confettiInterval) clearInterval(confettiInterval);
            confettiInterval = setInterval(dropBatch, 400);
        }

        function closeVictoryModal() {
            playSoundClick();
            document.getElementById('sphere-opening-modal').classList.add('hidden');
            if (confettiInterval) {
                clearInterval(confettiInterval);
                confettiInterval = null;
            }
        }

        let wiiCursorActive = true;
        let isConfigMode = false;
        const wiiPointer = document.getElementById('wii-pointer');
        const tombolaContainer = document.querySelector('.tombola-container');

        function switchView(viewId) {
            playSoundClick();
            document.getElementById('tombola-view').classList.add('hidden');
            document.getElementById('setup-view').classList.add('hidden');
            document.getElementById(viewId).classList.remove('hidden');

            if (viewId === 'tombola-view') {
                isConfigMode = false;
                initTombolaPhysics();
                if (wiiCursorActive) {
                    tombolaContainer.style.cursor = 'none';
                    wiiPointer.style.display = 'block';
                }
            } else if (viewId === 'setup-view') {
                isConfigMode = true;
                tombolaContainer.style.cursor = 'auto';
                wiiPointer.style.display = 'none';
            }
        }

        tombolaContainer.addEventListener('mousemove', (e) => {
            if (wiiCursorActive && !isConfigMode) {
                wiiPointer.style.display = 'block';
                const rect = tombolaContainer.getBoundingClientRect();
                wiiPointer.style.left = `${e.clientX - rect.left}px`;
                wiiPointer.style.top = `${e.clientY - rect.top}px`;
            }
        });

        tombolaContainer.addEventListener('mouseleave', () => {
            wiiPointer.style.display = 'none';
        });

        tombolaContainer.addEventListener('mouseover', (e) => {
            if (e.target.closest('.wii-btn') || e.target.closest('.cursor-pointer')) {
                playSoundHover();
            }
        });

        function toggleWiiCursor() {
            playSoundClick();
            wiiCursorActive = !wiiCursorActive;
            if (wiiCursorActive) {
                tombolaContainer.style.cursor = 'none';
            } else {
                tombolaContainer.style.cursor = 'auto';
                wiiPointer.style.display = 'none';
            }
        }

        function showCustomToast(title, bodyText) {
            const toast = document.createElement('div');
            toast.className = "fixed bottom-12 right-12 wii-panel p-4 max-w-sm flex flex-col gap-1 border-l-8 border-l-[#f47c20] shadow-2xl z-50 animate-bounce";
            toast.innerHTML = `
                <h4 class="font-bold text-gray-800 text-base">${title}</h4>
                <p class="text-xs text-gray-500">${bodyText}</p>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-all', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // --- MANEJADOR DE PANTALLA COMPLETA ---
        function toggleFullscreen() {
            playSoundClick();
            if (!document.fullscreenElement) {
                tombolaContainer.requestFullscreen().catch(err => {
                    alert(`No se pudo iniciar el modo pantalla completa: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const fsIcon = document.getElementById('fs-icon');
            const fsText = document.getElementById('fs-text');
            if (document.fullscreenElement) {
                fsIcon.classList.replace('fa-expand', 'fa-compress');
                fsText.textContent = 'Salir de Pantalla';
            } else {
                fsIcon.classList.replace('fa-compress', 'fa-expand');
                fsText.textContent = 'Pantalla Completa';
            }
        });

        // --- INICIALIZADORES AL CARGAR LA Pï¿½GINA ---
        window.onload = function() {
            initData();

            // Reloj en tiempo real
            setInterval(() => {
                const clock = document.getElementById('wii-clock');
                const now = new Date();
                let hours = now.getHours();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const minutes = now.getMinutes().toString().padStart(2, '0');
                clock.innerText = `${hours}:${minutes} ${ampm}`;
            }, 1000);

            initTombolaPhysics();
            animateTombola();

            document.body.addEventListener('click', () => {
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
            }, { once: true });
        }
    </script>
</div>
@endsection
