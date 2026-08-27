<?php

Route::get('/ping', function () {
    return response()->json(['ok' => true, 'time' => now()->toDateTimeString()]);
});


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ParticipanteController;
use App\Http\Controllers\ProveedorController;

// Incluir ruta de setup de permisos
require __DIR__ . '/setup_permisos.php';

/* ─────────────────── AUTH ─────────────────── */
Route::get('/',      [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/clear-cache', function() {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    \Artisan::call('route:clear');
    \Artisan::call('config:clear');
    \Artisan::call('cache:clear');
    return "Cache limpia y OPcache reiniciada.";
});

Route::get('/migrate', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('usuarios', 'ID_Evento')) {
            \DB::statement("ALTER TABLE `usuarios` ADD COLUMN `ID_Evento` BIGINT UNSIGNED NULL");
            \DB::statement("ALTER TABLE `usuarios` ADD CONSTRAINT `fk_evento_usuario` FOREIGN KEY (`ID_Evento`) REFERENCES `evento`(`ID`) ON DELETE SET NULL");
            return "Columna ID_Evento agregada y FK establecida.";
        }
        return "La columna ID_Evento ya existe.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/migrate-sorteo', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('premios_evento', 'OrdenSorteo')) {
            \DB::statement("ALTER TABLE `premios_evento` ADD COLUMN `OrdenSorteo` INT DEFAULT 0 AFTER `TipoPremio`");
            return "Columna OrdenSorteo agregada con exito.";
        }
        return "La columna OrdenSorteo ya existe.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/give-tickets', function() {
    $evento_id = 1;
    $boleto = \App\Models\PremioEvento::where('ID_Evento', $evento_id)->where('NombrePremio', 'LIKE', '%Boleto%')->first();
    if (!$boleto) return "No se encontro el premio Boleto";

    $participantes = \App\Models\Participante::where('ID_Evento', $evento_id)->take(5)->get();
    if ($participantes->isEmpty()) return "No hay participantes";

    $res = "";
    foreach ($participantes as $p) {
        $cantidad = rand(1, 3);
        \App\Models\Canje::create([
            'ID_Evento' => $evento_id,
            'ID_Participante' => $p->ID,
            'ID_Premio' => $boleto->ID,
            'Cantidad' => $cantidad,
            'Fecha' => now()
        ]);
        $res .= "Se le dieron $cantidad boletos a {$p->Nombre}<br>";
    }
    return $res;
});

Route::get('/migrate-apariencia', function() {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('apariencias')) {
            \Illuminate\Support\Facades\Schema::create('apariencias', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('logo_path')->nullable();
                $table->string('color_primario')->default('#c9a227');
                $table->string('color_secundario')->default('#3b82f6');
                $table->string('fondo_login')->default('arbol');
                $table->string('fade_gradient_start')->default('rgba(234, 90, 12, 0.63)');
                $table->string('fade_gradient_end')->default('rgba(2, 6, 23, 1)');
                $table->timestamps();
            });
            \App\Models\Apariencia::create([]);
            return "Tabla apariencias creada con éxito.";
        }
        return "La tabla ya existe.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/hacer-admin', function() {
    \App\Models\Usuario::where('username', 'admin')->update(['Rol' => 'Administrador']);
    return "Usuario admin ahora es Administrador. Ya puedes volver a entrar al Dashboard.";
});

Route::get('/logout-rapido', function() {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
});

/* ─────────────────── PROTEGIDAS ─────────────────── */
Route::middleware('auth')->group(function () {

    /* === RUTAS EXCLUSIVAS DE ADMINISTRADOR === */
    Route::middleware('role:Administrador')->group(function () {
        /* Dashboard */
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /* Eventos (excepto show) */
        Route::resource('eventos', EventoController::class)->except(['show']);

        /* Usuarios y Roles */
        Route::resource('usuarios', \App\Http\Controllers\UsuarioController::class)->except(['show']);
    Route::post('usuarios/{id}/toggle-activo', [\App\Http\Controllers\UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo');

        Route::resource('roles', \App\Http\Controllers\RoleController::class)->except(['show']);

        /* Ubicaciones */
        Route::resource('ubicaciones', \App\Http\Controllers\UbicacionController::class)->parameters(['ubicaciones' => 'ubicacion']);
        Route::get('ubicaciones/{ubicacion}/salones', [\App\Http\Controllers\SalonController::class, 'index'])->name('ubicaciones.salones.index');
        Route::post('ubicaciones/{ubicacion}/salones', [\App\Http\Controllers\SalonController::class, 'store'])->name('ubicaciones.salones.store');
        Route::delete('salones/{salon}', [\App\Http\Controllers\SalonController::class, 'destroy'])->name('salones.destroy');
        Route::put('salones/{salon}', [\App\Http\Controllers\SalonController::class, 'update'])->name('salones.update');

        Route::get('proveedores/gestion', [\App\Http\Controllers\ProveedorController::class, 'gestion'])->name('proveedores.gestion');
        Route::post('proveedores/gestion', [\App\Http\Controllers\ProveedorController::class, 'storeUsuario'])->name('proveedores.storeUsuario');
        Route::put('proveedores/gestion/{usuario}', [\App\Http\Controllers\ProveedorController::class, 'updateUsuario'])->name('proveedores.updateUsuario');
        Route::delete('proveedores/gestion/{usuario}', [\App\Http\Controllers\ProveedorController::class, 'destroyUsuario'])->name('proveedores.destroyUsuario');

        /* Apariencia CSS */
        Route::get('/apariencia', [\App\Http\Controllers\AparienciaController::class, 'index'])->name('apariencia.index');
        Route::post('/apariencia', [\App\Http\Controllers\AparienciaController::class, 'update'])->name('apariencia.update');

        /* Agenda y Actividades (anidadas en Evento) */
        Route::resource('eventos.actividades', \App\Http\Controllers\ActividadController::class)->parameters([
            'actividades' => 'actividad'
        ])->shallow();
        
        Route::resource('eventos.premios', \App\Http\Controllers\PremioController::class)->shallow();
        Route::post('premios/{premio}/stock', [\App\Http\Controllers\PremioController::class, 'updateStock'])->name('premios.stock');
        Route::resource('eventos.agenda', \App\Http\Controllers\AgendaController::class)->shallow();
        Route::resource('eventos.proveedores', \App\Http\Controllers\ProveedorEventoController::class)->only(['store', 'update', 'destroy'])->shallow();
        Route::get('eventos/{evento}/proveedores/{usuario}/prospectos', [\App\Http\Controllers\ProveedorController::class, 'getProspectosProveedor'])->name('eventos.proveedores.prospectos');

        /* Canjes de Premios */
        Route::get('eventos/{evento}/canjes', [\App\Http\Controllers\CanjeController::class, 'index'])->name('eventos.canjes.index');
        Route::post('eventos/{evento}/canjes/buscar', [\App\Http\Controllers\CanjeController::class, 'buscarParticipante'])->name('eventos.canjes.buscar');
        Route::get('eventos/{evento}/canjes/participante/{participante}', [\App\Http\Controllers\CanjeController::class, 'infoParticipante'])->name('eventos.canjes.participante');
        Route::post('eventos/{evento}/canjes/canjear', [\App\Http\Controllers\CanjeController::class, 'canjear'])->name('eventos.canjes.canjear');
        Route::get('eventos/{evento}/canjes/reporte', [\App\Http\Controllers\CanjeController::class, 'reporte'])->name('eventos.canjes.reporte');

        /* Participantes (Acciones exclusivas de Admin: editar y borrar) */
        Route::resource('participantes', ParticipanteController::class)->only(['edit', 'update', 'destroy']);
    });

    /* === RUTAS COMPARTIDAS (ADMIN Y VENDEDOR) === */
    Route::middleware('role:Administrador,Vendedor')->group(function () {
        /* Rutas explícitas antes de resource para evitar solapamientos con {participante} */
        Route::get('participantes/buscar-por-telefono/{telefono}', [ParticipanteController::class, 'searchByPhone'])->name('participantes.searchPhone');
        Route::get('participantes/buscar-lista-telefonos', [ParticipanteController::class, 'searchPhonesList'])->name('participantes.searchPhonesList');
        Route::get('participantes/buscar-vendedores', [ParticipanteController::class, 'buscarVendedores'])->name('participantes.buscar-vendedores');
        
        Route::resource('participantes', ParticipanteController::class)->only(['index', 'show', 'create', 'store']);
        Route::get('eventos/{evento}/agenda-json', [ParticipanteController::class, 'getAgenda'])->name('eventos.agenda.json');
        Route::get('clientes/perfil/{telefono}', [ParticipanteController::class, 'globalProfile'])->name('clientes.perfil');
    });

    /* === RUTAS COMPARTIDAS (ADMIN Y EVENTO) === */
    Route::middleware('role:Administrador,Evento')->group(function () {
        Route::get('eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');
        Route::get('eventos/{evento}/estadisticas', [EventoController::class, 'estadisticas'])->name('eventos.estadisticas');
        Route::get('eventos/{evento}/estadisticas/export', [EventoController::class, 'exportarEstadisticasExcel'])->name('eventos.estadisticas.export');
        Route::get('eventos/{evento}/sorteo', [EventoController::class, 'sorteo'])->name('eventos.sorteo');
        Route::post('eventos/{evento}/sorteo/orden', [EventoController::class, 'actualizarOrdenPremio'])->name('eventos.sorteo.orden');
        Route::post('eventos/{evento}/sorteo/ganador', [EventoController::class, 'registrarGanador'])->name('eventos.sorteo.ganador');
        Route::post('eventos/{evento}/sorteo/revertir-ganador', [EventoController::class, 'revertirGanador'])->name('eventos.sorteo.revertir-ganador');
        Route::post('eventos/{evento}/sorteo/toggle-delivery', [EventoController::class, 'toggleDelivery'])->name('eventos.sorteo.toggle-delivery');
        Route::post('eventos/{evento}/sorteo/dia-premio', [EventoController::class, 'actualizarDiaPremio'])->name('eventos.sorteo.dia-premio');
        
        /* AJAX Asistencia y Scanner QR */
        Route::match(['get', 'post'], 'actividades/{actividad}/buscar', [\App\Http\Controllers\ActividadController::class, 'buscarParticipantes'])->name('actividades.buscar');
        Route::post('actividades/{actividad}/asistencia', [\App\Http\Controllers\ActividadController::class, 'marcarAsistencia'])->name('actividades.asistencia');
        Route::post('actividades/{actividad}/toggle-asistencia', [\App\Http\Controllers\ActividadController::class, 'toggleAsistencia'])->name('actividades.toggle-asistencia');
        Route::post('actividades/{actividad}/inscribir', [\App\Http\Controllers\ActividadController::class, 'inscribirParticipante'])->name('actividades.inscribir');
        Route::post('actividades/{actividad}/registro-rapido', [\App\Http\Controllers\ActividadController::class, 'registroRapido'])->name('actividades.registro-rapido');
    });

    /* === RUTAS DE PROVEEDOR === */
    Route::middleware('role:Administrador,Proveedor')->group(function () {
        Route::get('/proveedor', [ProveedorController::class, 'index'])->name('proveedor.index');
        Route::post('/proveedor/asignar-puntos', [ProveedorController::class, 'asignarPuntos'])->name('proveedor.asignar-puntos');
        Route::post('/proveedor/toggle-prospecto', [ProveedorController::class, 'toggleProspecto'])->name('proveedor.toggle-prospecto');
    });

});

/* Kiosko Consulta de Puntos */
Route::middleware(['auth', 'role:Admin,Gerente,Vendedor,Evento,Kiosko'])->group(function () {
    Route::get('/kiosko', [\App\Http\Controllers\KioskoController::class, 'index'])->name('kiosko.index');
    Route::post('/kiosko/buscar', [\App\Http\Controllers\KioskoController::class, 'buscar'])->name('kiosko.buscar');
});
