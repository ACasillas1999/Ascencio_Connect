<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    private function checkSchema()
    {
        try {
            if (!\Schema::hasColumn('evento', 'gafete_qr_x')) {
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_qr_x` INT DEFAULT 1755");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_qr_y` INT DEFAULT 280");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_nombre_x` INT DEFAULT 202");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_nombre_y` INT DEFAULT 1050");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_font_size` INT DEFAULT 60");
            }
            if (!\Schema::hasColumn('evento', 'gafete_id_x')) {
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_id_x` INT DEFAULT 202");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_id_y` INT DEFAULT 1200");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_id_font_size` INT DEFAULT 40");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_color_nombre` VARCHAR(7) DEFAULT '#000000'");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_color_id` VARCHAR(7) DEFAULT '#000000'");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_font_family` VARCHAR(50) DEFAULT 'Arial'");
            }
            if (!\Schema::hasColumn('evento', 'gafete_qr_size')) {
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `gafete_qr_size` INT DEFAULT 25");
            }
            if (!\Schema::hasColumn('evento', 'horario_nombre_x')) {
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_nombre_x` INT DEFAULT 202");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_nombre_y` INT DEFAULT 150");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_font_size` INT DEFAULT 40");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_color_nombre` VARCHAR(7) DEFAULT '#000000'");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_id_x` INT DEFAULT 202");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_id_y` INT DEFAULT 250");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_id_font_size` INT DEFAULT 30");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_color_id` VARCHAR(7) DEFAULT '#000000'");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_lista_x` INT DEFAULT 100");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_lista_y` INT DEFAULT 350");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_lista_w` INT DEFAULT 800");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_lista_h` INT DEFAULT 1000");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_lista_font_size` INT DEFAULT 24");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_color_lista` VARCHAR(7) DEFAULT '#000000'");
                \DB::statement("ALTER TABLE `evento` ADD COLUMN `horario_font_family` VARCHAR(50) DEFAULT 'Arial'");
            }
        } catch (\Exception $e) {
            \Log::error("Error en migración temporal: " . $e->getMessage());
        }
    }

    public function index()
    {
        $this->checkSchema();

        $eventos = Evento::withCount('participantes')
            ->orderByDesc('fecha_inicio')
            ->paginate(15);
        $ubicaciones = Ubicacion::orderBy('Nombre')->get();
        return view('eventos.index', compact('eventos', 'ubicaciones'));
    }

    public function create()
    {
        $ubicaciones = Ubicacion::orderBy('Nombre')->get();
        return view('eventos.create', compact('ubicaciones'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name_evento'          => 'required|string|max:255',
            'duracion'             => 'required|string|max:255',
            'estado'               => 'required|in:EN CURSO,FINALIZADO,PRÓXIMO',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion'            => 'required|string|max:255',
            'capacidad'            => 'required|integer|min:1',
            'tipo_puntos'          => 'required|in:ninguno,individual,grupal',
            'machote_gafete'       => 'nullable|image|mimes:jpeg,png,jpg|max:25600',
            'machote_horario'      => 'nullable|image|mimes:jpeg,png,jpg|max:25600',
            'enviar_whatsapp_auto' => 'nullable|boolean',
            'clases_obligatorias'  => 'nullable|boolean',
            'wa_template_name'     => 'nullable|string|max:255',
        ];

        $data = $request->validate($rules);

        // Upload machotes si existen
        if ($request->hasFile('machote_gafete')) {
            $data['machote_gafete'] = $request->file('machote_gafete')->store('machotes', 'public');
        }
        if ($request->hasFile('machote_horario')) {
            $data['machote_horario'] = $request->file('machote_horario')->store('machotes', 'public');
        }

        // Set booleans (checkboxes don't send anything if unchecked)
        $data['enviar_whatsapp_auto'] = $request->has('enviar_whatsapp_auto');
        $data['clases_obligatorias']  = $request->has('clases_obligatorias');

        Evento::create($data);
        return redirect()->route('eventos.index')->with('success', 'Evento creado correctamente.');
    }

    public function show(Evento $evento)
    {
        $normRol = auth()->check() ? \App\Helpers\Permisos::normalizar(auth()->user()->Rol) : '';
        if (in_array($normRol, ['Vendedor', 'Gerente'])) {
            if ($evento->estado === 'FINALIZADO') {
                return redirect()->route('participantes.index')->with('error', 'No tienes permiso para acceder a eventos finalizados.');
            }
        }

        $evento->loadCount(['participantes', 'actividades', 'agenda']);
        $participantes = $evento->participantes()->paginate(20);
        
        $actividades = $evento->actividades()->orderBy('Actividad')->get();
        $agenda = $evento->agenda()->orderBy('Fecha')->orderBy('Horario')->get();
        
        $proveedores = \DB::table('proveedor_evento')
            ->leftJoin('usuarios', 'usuarios.username', '=', 'proveedor_evento.NombreProveedor')
            ->where('proveedor_evento.ID_Evento', $evento->ID)
            ->select('proveedor_evento.*', 'usuarios.ID as usuario_id', 'usuarios.password_visible')
            ->get();

        $cuentasProveedores = \App\Models\Usuario::whereIn('Rol', ['proveedor', 'Proveedor'])->orderBy('username')->get();
        
        // Generar previsualización del gafete y horario
        // Carga ultrarrápida (< 30ms): Servir rutas de machote sin ejecutar procesamiento GD síncrono en cada request
        $mockGafetePath = 'machotes/mock_gafete_' . $evento->ID . '.jpg';
        $mockHorarioPath = 'machotes/mock_horario_' . $evento->ID . '.jpg';

        $imageService = new \App\Services\ImageService();
        if (!\Storage::disk('public')->exists($mockGafetePath)) {
            $imageService->generarMockGafete($evento);
        }
        $mockGafete = \Storage::disk('public')->exists($mockGafetePath) ? $mockGafetePath : $imageService->generarMockGafete($evento);
        $mockHorario = ($evento->machote_horario && \Storage::disk('public')->exists($mockHorarioPath)) ? $mockHorarioPath : null;
        
        $ubicacionModel = \App\Models\Ubicacion::where('Nombre', $evento->ubicacion)->first();
        $numSalones = $ubicacionModel ? $ubicacionModel->Salones : 1;
        $salones = $ubicacionModel ? \App\Models\Salon::where('ubicacion_id', $ubicacionModel->ID)->orderBy('Nombre')->get() : collect();
        $agendaJson = $agenda->toJson();
        
        $premios = \App\Models\PremioEvento::where('ID_Evento', $evento->ID)->get();

        // Estadísticas de Canjes y Premios
        $canjesEvento = \DB::table('canjes')->where('ID_Evento', $evento->ID)->get();
        $totalCanjesRealizados = $canjesEvento->count();
        $totalPremiosEntregados = $canjesEvento->sum('Cantidad');
        
        $puntosTotalesCanjeados = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->sum(\DB::raw('canjes.Cantidad * premios_evento.PuntosNecesarios'));

        // Ranking de lo más canjeado
        $rankingCanjes = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->groupBy('canjes.ID_Premio', 'premios_evento.NombrePremio', 'premios_evento.TipoPremio', 'premios_evento.PuntosNecesarios', 'premios_evento.Disponible')
            ->selectRaw('premios_evento.NombrePremio as nombre, premios_evento.TipoPremio as tipo, premios_evento.PuntosNecesarios as puntos, premios_evento.Disponible as disponible, SUM(canjes.Cantidad) as total_unidades, COUNT(*) as num_operaciones')
            ->orderByDesc('total_unidades')
            ->get();

        $premioMasCanjeado = $rankingCanjes->first();

        // Boletos en Tómbola (Canjes de premios que contienen "boleto")
        $boletosTombolaData = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->join('participante', 'canjes.ID_Participante', '=', 'participante.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->where('premios_evento.NombrePremio', 'LIKE', '%boleto%')
            ->groupBy('canjes.ID_Participante', 'participante.Nombre', 'participante.Sucursal')
            ->selectRaw('participante.Nombre as nombre, participante.Sucursal as sucursal, SUM(canjes.Cantidad) as total_boletos')
            ->orderByDesc('total_boletos')
            ->get();

        $totalBoletosTombola = $boletosTombolaData->sum('total_boletos');
        $totalParticipantesTombola = $boletosTombolaData->count();
        $stockTotalPremios = $premios->sum('Disponible');

        // Resumen agrupado por sucursal para escalar a 1,500+ participantes
        $boletosPorSucursal = $boletosTombolaData->groupBy('sucursal')->map(function($items, $sucursalKey) {
            return (object)[
                'sucursal' => $sucursalKey ?: 'Sin Sucursal',
                'total_boletos' => $items->sum('total_boletos'),
                'total_participantes' => $items->count(),
            ];
        })->sortByDesc('total_boletos')->values();
        
        return view('eventos.show', compact('evento', 'participantes', 'actividades', 'agenda', 'agendaJson', 'proveedores', 'cuentasProveedores', 'mockGafete', 'mockHorario', 'numSalones', 'salones', 'premios', 'totalCanjesRealizados', 'totalPremiosEntregados', 'puntosTotalesCanjeados', 'rankingCanjes', 'premioMasCanjeado', 'boletosTombolaData', 'totalBoletosTombola', 'totalParticipantesTombola', 'stockTotalPremios', 'boletosPorSucursal'));
    }

    public function edit(Evento $evento)
    {
        $ubicaciones = Ubicacion::orderBy('Nombre')->get();
        return view('eventos.edit', compact('evento', 'ubicaciones'));
    }

    public function update(Request $request, Evento $evento)
    {
        $this->checkSchema();
        $rules = [
            'name_evento'          => 'required|string|max:255',
            'duracion'             => 'required|string|max:255',
            'estado'               => 'required|in:EN CURSO,FINALIZADO,PRÓXIMO',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'required|date|after_or_equal:fecha_inicio',
            'ubicacion'            => 'required|string|max:255',
            'capacidad'            => 'required|integer|min:1',
            'tipo_puntos'          => 'required|in:ninguno,individual,grupal',
            'machote_gafete'       => 'nullable|image|mimes:jpeg,png,jpg|max:25600',
            'machote_horario'      => 'nullable|image|mimes:jpeg,png,jpg|max:25600',
            'enviar_whatsapp_auto' => 'nullable|boolean',
            'clases_obligatorias'  => 'nullable|boolean',
            'wa_template_name'     => 'nullable|string|max:255',
            'gafete_qr_x'          => 'nullable|integer',
            'gafete_qr_y'          => 'nullable|integer',
            'gafete_qr_size'       => 'nullable|integer|min:5|max:100',
            'gafete_nombre_x'      => 'nullable|integer',
            'gafete_nombre_y'      => 'nullable|integer',
            'gafete_font_size'     => 'nullable|integer',
            'gafete_id_x'          => 'nullable|integer',
            'gafete_id_y'          => 'nullable|integer',
            'gafete_id_font_size'  => 'nullable|integer',
            'gafete_color_nombre'  => 'nullable|string|max:7',
            'gafete_color_id'      => 'nullable|string|max:7',
            'gafete_font_family'   => 'nullable|string|max:50',
            'horario_nombre_x'     => 'nullable|integer',
            'horario_nombre_y'     => 'nullable|integer',
            'horario_font_size'    => 'nullable|integer',
            'horario_id_x'         => 'nullable|integer',
            'horario_id_y'         => 'nullable|integer',
            'horario_id_font_size' => 'nullable|integer',
            'horario_lista_x'      => 'nullable|integer',
            'horario_lista_y'      => 'nullable|integer',
            'horario_lista_w'      => 'nullable|integer',
            'horario_lista_h'      => 'nullable|integer',
            'horario_lista_font_size' => 'nullable|integer',
            'horario_color_nombre' => 'nullable|string|max:7',
            'horario_color_id'     => 'nullable|string|max:7',
            'horario_color_lista'  => 'nullable|string|max:7',
            'horario_font_family'  => 'nullable|string|max:50',
        ];

        $data = $request->validate($rules);

        // Upload machotes si existen
        if ($request->hasFile('machote_gafete')) {
            $data['machote_gafete'] = $request->file('machote_gafete')->store('machotes', 'public');
        }
        if ($request->hasFile('machote_horario')) {
            $data['machote_horario'] = $request->file('machote_horario')->store('machotes', 'public');
        }

        // Set booleans (checkboxes don't send anything if unchecked)
        $data['enviar_whatsapp_auto'] = $request->has('enviar_whatsapp_auto');
        $data['clases_obligatorias']  = $request->has('clases_obligatorias');

        $evento->update($data);

        // Regenerar de inmediato las imágenes del machote con las nuevas coordenadas
        $imageService = new \App\Services\ImageService();
        $mockGafete = $imageService->generarMockGafete($evento);
        $mockHorario = $imageService->generarMockHorario($evento);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'mockGafeteUrl' => $mockGafete ? asset('storage/' . $mockGafete) . '?t=' . microtime(true) : null,
                'mockHorarioUrl' => $mockHorario ? asset('storage/' . $mockHorario) . '?t=' . microtime(true) : null
            ]);
        }

        return redirect()->route('eventos.show', [$evento, 'active_tab' => $request->input('active_tab', 'tab-general')])->with('success', 'Evento actualizado.');
    }

    public function destroy(Evento $evento)
    {
        // Eliminar registros asociados en cascada para evitar herencia de datos huérfanos si se reutiliza el ID
        \DB::table('premios_evento')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('proveedor_evento')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('actividades')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('agenda')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('canjes')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('participante')->where('ID_Evento', $evento->ID)->delete();
        \DB::table('puntos_rfc')->where('ID_Evento', $evento->ID)->delete();

        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento y sus datos asociados eliminados.');
    }

    public function sorteo(Evento $evento)
    {
        $evento->load(['participantes.canjes.premio']);
        
        $participantes = collect();
        
        foreach ($evento->participantes as $p) {
            $colors = ['#00a0e9', '#ff9500', '#76c336', '#e60012', '#e91e63', '#9c27b0', '#ffeb3b'];
            $faces = ['happy', 'cool', 'excited', 'surprised'];
            
            $color = $colors[array_rand($colors)];
            $face = $faces[array_rand($faces)];
            
            $boletosExtras = 0;
            $yaGano = false;

            foreach ($p->canjes as $canje) {
                if ($canje->premio) {
                    if (str_contains(strtolower($canje->premio->NombrePremio), 'boleto')) {
                        $boletosExtras += $canje->Cantidad;
                    }
                    if (($canje->premio->TipoPremio ?? 'sorteo') === 'sorteo') {
                        $yaGano = true;
                    }
                }
            }
            
            // Si ya ganó un premio de sorteo, no participa
            if ($yaGano) {
                continue;
            }
            
            $totalEntries = $boletosExtras;
            
            for ($i = 0; $i < $totalEntries; $i++) {
                $participantes->push([
                    'id' => 'p_' . $p->ID . '_' . $i,
                    'display_id' => $p->ID,
                    'name' => $p->Nombre,
                    'color' => $color,
                    'face' => $face,
                    'boletos' => $totalEntries
                ]);
            }
        }
        
        $participantes = $participantes->values();

        // Obtener historial de ganadores (Canjes de premios de sorteo)
        $historialRaw = \App\Models\Canje::with(['participante', 'premio'])
            ->where('ID_Evento', $evento->ID)
            ->whereHas('premio', function($q) {
                $q->where('TipoPremio', 'sorteo')->orWhereNull('TipoPremio');
            })
            ->whereHas('participante')
            ->orderBy('Fecha', 'desc')
            ->get();

        $historial = $historialRaw->map(function($canje) {
            return [
                'canje_id' => $canje->ID,
                'display_id' => $canje->participante ? $canje->participante->ID : 'N/A',
                'name' => $canje->participante ? $canje->participante->Nombre : 'Participante Eliminado',
                'prize' => $canje->premio ? $canje->premio->NombrePremio : 'Premio Eliminado',
                'delivered' => (bool)$canje->Entregado,
                'color' => '#00a0e9',
                'face' => 'excited'
            ];
        });

        $premiosRaw = \App\Models\PremioEvento::where('ID_Evento', $evento->ID)
            ->orderBy('OrdenSorteo', 'asc')
            ->get();
            
        $premios = $premiosRaw->map(function($pr) use ($historialRaw) {
            $colors = ['#ffeb3b', '#00a0e9', '#ff9500', '#76c336', '#e60012'];
            $canje = $historialRaw->firstWhere('ID_Premio', $pr->ID);
            
            $data = [
                'id' => 'pr_' . $pr->ID,
                'name' => $pr->NombrePremio,
                'color' => $colors[array_rand($colors)],
                'type' => $pr->TipoPremio ?? 'sorteo'
            ];

            if ($canje) {
                $data['winner'] = $canje->participante ? $canje->participante->Nombre : 'Participante Eliminado';
                $data['winner_id'] = $canje->participante ? $canje->participante->ID : null;
                $data['canje_id'] = $canje->ID;
                $data['delivered'] = (bool)$canje->Entregado;
            }

            return $data;
        })->values();

        // Obtener historial de premios canjeados por puntos
        $historialPuntosRaw = \App\Models\Canje::with(['participante', 'premio'])
            ->where('ID_Evento', $evento->ID)
            ->whereHas('premio', function($q) {
                $q->where('TipoPremio', 'puntos');
            })
            ->whereHas('participante')
            ->orderBy('Fecha', 'desc')
            ->get();

        $historialPuntos = $historialPuntosRaw->map(function($canje) {
            return [
                'canje_id' => $canje->ID,
                'participante' => $canje->participante ? $canje->participante->Nombre : 'Participante Eliminado',
                'participante_id' => $canje->participante ? $canje->participante->ID : null,
                'premio' => $canje->premio ? $canje->premio->NombrePremio : 'Premio Eliminado',
                'puntos' => $canje->premio ? $canje->premio->PuntosNecesarios : 0,
                'cantidad' => $canje->Cantidad,
                'fecha' => $canje->Fecha ? $canje->Fecha->format('d/m/Y H:i') : ''
            ];
        });

        return view('eventos.sorteo', compact('evento', 'participantes', 'premios', 'historial', 'historialPuntos'));
    }

    public function actualizarOrdenPremio(Request $request, Evento $evento)
    {
        $ordenes = $request->input('ordenes', []);
        foreach ($ordenes as $index => $premioId) {
            // El premioId viene como 'pr_ID'
            $idStr = str_replace('pr_', '', $premioId);
            \App\Models\PremioEvento::where('ID', $idStr)
                ->where('ID_Evento', $evento->ID)
                ->update(['OrdenSorteo' => $index]);
        }
        return response()->json(['ok' => true]);
    }

    public function registrarGanador(Request $request, Evento $evento)
    {
        $participanteId = $request->input('participante_id');
        $premioId = str_replace('pr_', '', $request->input('premio_id'));

        $premio = \App\Models\PremioEvento::where('ID', $premioId)
            ->where('ID_Evento', $evento->ID)
            ->first();

        $canjeId = null;
        if ($premio && $premio->Disponible > 0) {
            $canje = \App\Models\Canje::create([
                'ID_Evento' => $evento->ID,
                'ID_Participante' => $participanteId,
                'ID_Premio' => $premioId,
                'Cantidad' => 1,
                'Fecha' => now(),
            ]);
            $canjeId = $canje->ID;

            $premio->Disponible -= 1;
            $premio->save();
        }

        return response()->json(['ok' => true, 'canje_id' => $canjeId]);
    }

    public function revertirGanador(Request $request, Evento $evento)
    {
        $canjeId = $request->input('canje_id');
        $canje = \App\Models\Canje::where('ID', $canjeId)
            ->where('ID_Evento', $evento->ID)
            ->first();

        if ($canje) {
            $premio = \App\Models\PremioEvento::where('ID', $canje->ID_Premio)->first();
            if ($premio) {
                $premio->Disponible += $canje->Cantidad;
                $premio->save();
            }
            $canje->delete();
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 404);
    }

    public function toggleDelivery(Request $request, Evento $evento)
    {
        $canjeId = $request->input('canje_id');
        $delivered = $request->input('delivered');

        $canje = \App\Models\Canje::where('ID', $canjeId)
            ->where('ID_Evento', $evento->ID)
            ->first();

        if ($canje) {
            $canje->Entregado = $delivered ? 1 : 0;
            $canje->save();
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false], 404);
    }

    public function estadisticas(Evento $evento)
    {
        // 1. Inscritos y Aforo Registrado
        $totalInscritos = $evento->participantes()->count();
        
        // Obtenemos todos los IDs de agenda del evento
        $agendaIds = $evento->agenda()->pluck('ID')->toArray();

        // 2. Participantes que ASISTIERON (al menos a 1 actividad)
        $asistentesIds = \DB::table('clase')
            ->whereIn('ID_Agenda', $agendaIds)
            ->where('Asistio', 1)
            ->pluck('ID_Participante')
            ->unique();
        
        $totalAsistieron = $asistentesIds->count();
        $totalSinAsistencia = max(0, $totalInscritos - $totalAsistieron);
        $porcentajeAsistencia = ($totalInscritos > 0) ? round(($totalAsistieron / $totalInscritos) * 100, 1) : 0;

        // 3. Asistencia por Día y Cruzada (Día 1 vs Día 2)
        $agendaPorFecha = $evento->agenda()->get()->groupBy(function($item) {
            return $item->Fecha->format('Y-m-d');
        });

        $fechas = $agendaPorFecha->keys()->sort()->values();
        $dia1Str = $fechas->get(0);
        $dia2Str = $fechas->get(1);

        $partDia1 = collect();
        $partDia2 = collect();

        if ($dia1Str) {
            $agendaIdsDia1 = $agendaPorFecha->get($dia1Str)->pluck('ID')->toArray();
            $partDia1 = \DB::table('clase')
                ->whereIn('ID_Agenda', $agendaIdsDia1)
                ->where('Asistio', 1)
                ->pluck('ID_Participante')
                ->unique();
        }

        if ($dia2Str) {
            $agendaIdsDia2 = $agendaPorFecha->get($dia2Str)->pluck('ID')->toArray();
            $partDia2 = \DB::table('clase')
                ->whereIn('ID_Agenda', $agendaIdsDia2)
                ->where('Asistio', 1)
                ->pluck('ID_Participante')
                ->unique();
        }

        $countDia1 = $partDia1->count();
        $countDia2 = $partDia2->count();

        $ambosDias = $partDia1->intersect($partDia2)->count();
        $soloDia1 = $partDia1->diff($partDia2)->count();
        $soloDia2 = $partDia2->diff($partDia1)->count();
        $soloUnDia = $soloDia1 + $soloDia2;

        // 4. Asistencia por Hora (Distribución Horaria)
        $asistenciaPorHora = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("
                SUBSTRING_INDEX(agenda.Horario, '-', 1) as hora_raw,
                COUNT(*) as total_escaneos,
                COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos
            ")
            ->groupBy('hora_raw')
            ->orderBy('hora_raw')
            ->get();

        // 5. Asistencia por Sucursal
        $asistenciaPorSucursal = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("participante.Sucursal as sucursal, COUNT(DISTINCT participante.ID) as total_asistentes")
            ->groupBy('participante.Sucursal')
            ->orderByDesc('total_asistentes')
            ->get();

        // 5.5. Asistencia por Puesto / Perfil de Asistentes
        $asistenciaPorPuesto = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("
                COALESCE(NULLIF(TRIM(participante.Puesto), ''), 'Sin Puesto Especificado') as puesto,
                COUNT(DISTINCT participante.ID) as total_asistentes
            ")
            ->groupBy('puesto')
            ->orderByDesc('total_asistentes')
            ->get();

        // 6. Actividades con Mayor Aforo
        $topActividades = \DB::table('agenda')
            ->leftJoin('clase', function($join) {
                $join->on('agenda.ID', '=', 'clase.ID_Agenda')->where('clase.Asistio', 1);
            })
            ->where('agenda.ID_Evento', $evento->ID)
            ->groupBy('agenda.ID', 'agenda.Actividad', 'agenda.Fecha', 'agenda.Horario', 'agenda.Salon')
            ->selectRaw('agenda.Actividad as actividad, agenda.Fecha as fecha, agenda.Horario as horario, agenda.Salon as salon, COUNT(clase.ID) as total_asistieron')
            ->orderByDesc('total_asistieron')
            ->take(8)
            ->get();

        // 7. Ranking de Vendedores / Registradores (¿Quién registró más y cuántos asistieron?)
        $rankingVendedores = \DB::table('participante')
            ->leftJoin('clase', function($join) use ($agendaIds) {
                $join->on('participante.ID', '=', 'clase.ID_Participante')
                    ->whereIn('clase.ID_Agenda', $agendaIds)
                    ->where('clase.Asistio', 1);
            })
            ->where('participante.ID_Evento', $evento->ID)
            ->groupBy('participante.Vendedor')
            ->selectRaw("
                participante.Vendedor as vendedor,
                COUNT(DISTINCT participante.ID) as total_registrados,
                COUNT(DISTINCT clase.ID_Participante) as total_asistieron
            ")
            ->orderByDesc('total_registrados')
            ->get()
            ->map(function($item) {
                $item->vendedor_nombre = $item->vendedor ? $item->vendedor : 'Sin Vendedor Asignado';
                $item->ausentes = max(0, $item->total_registrados - $item->total_asistieron);
                $item->pct_asistencia = $item->total_registrados > 0 ? round(($item->total_asistieron / $item->total_registrados) * 100, 1) : 0;
                return $item;
            });

        // 8. Proveedores que Repartieron Más Puntos
        $topProveedoresPuntos = \DB::table('puntos_proveedor')
            ->where('id_evento', $evento->ID)
            ->groupBy('usuario')
            ->selectRaw("
                usuario as proveedor,
                SUM(puntos) as total_puntos,
                COUNT(DISTINCT id_participante) as participantes_atendidos,
                COUNT(*) as num_transacciones
            ")
            ->orderByDesc('total_puntos')
            ->get();

        // 8.5. Concurrencia Global por Salón / Aula
        $salonesGlobal = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("COALESCE(NULLIF(TRIM(agenda.Salon), ''), 'Sin Salón Especificado') as salon, COUNT(clase.ID) as total_asistencias, COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos")
            ->groupBy('salon')
            ->orderByDesc('total_asistencias')
            ->get();

        // 8.6. Premios y Canjes Registrados en el Evento
        $topPremiosCanjeados = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->groupBy('premios_evento.ID', 'premios_evento.NombrePremio', 'premios_evento.Disponible')
            ->selectRaw("premios_evento.NombrePremio as premio, premios_evento.Disponible as stock_actual, SUM(canjes.Cantidad) as total_canjeados, COUNT(DISTINCT canjes.ID_Participante) as participantes_canjeadores")
            ->orderByDesc('total_canjeados')
            ->get();

        // 9. Puntos repartidos por fecha y por hora
        $puntosPorFechaHora = \DB::table('puntos_proveedor')
            ->where('id_evento', $evento->ID)
            ->selectRaw("
                DATE_FORMAT(fecha, '%Y-%m-%d') as fecha_dia,
                DATE_FORMAT(fecha, '%H:00') as hora,
                usuario as proveedor,
                SUM(puntos) as puntos_otorgados,
                COUNT(DISTINCT id_participante) as participantes_atendidos
            ")
            ->groupBy('fecha_dia', 'hora', 'proveedor')
            ->orderBy('fecha_dia')
            ->orderBy('hora')
            ->get()
            ->groupBy('fecha_dia');

        // Generar todas las fechas comprendidas del evento (aplica para 1 día, 2 días o hasta 1 semana)
        $period = \Carbon\CarbonPeriod::create($evento->fecha_inicio, $evento->fecha_fin);
        $todasLasFechas = collect($period)->map->format('Y-m-d')
            ->concat($agendaPorFecha->keys())
            ->unique()
            ->sort()
            ->values();

        // 10. Estadísticas Agrupadas por Día Específico (para las Pestañas de Día)
        $statsPorDia = [];
        foreach ($todasLasFechas as $index => $fStr) {
            $slotsDia = $agendaPorFecha->get($fStr, collect());
            $slotIdsDia = $slotsDia->pluck('ID')->toArray();

            $partDia = \DB::table('clase')
                ->whereIn('ID_Agenda', $slotIdsDia)
                ->where('Asistio', 1)
                ->pluck('ID_Participante')
                ->unique();

            $afluenciaDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("
                    SUBSTRING_INDEX(agenda.Horario, '-', 1) as hora_raw,
                    COUNT(*) as total_escaneos,
                    COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos
                ")
                ->groupBy('hora_raw')
                ->orderBy('hora_raw')
                ->get();

            $actividadesDia = \DB::table('agenda')
                ->leftJoin('clase', function($join) {
                    $join->on('agenda.ID', '=', 'clase.ID_Agenda')->where('clase.Asistio', 1);
                })
                ->whereIn('agenda.ID', $slotIdsDia)
                ->groupBy('agenda.ID', 'agenda.Actividad', 'agenda.Horario', 'agenda.Salon')
                ->selectRaw('agenda.Actividad as actividad, agenda.Horario as horario, agenda.Salon as salon, COUNT(clase.ID) as total_asistieron')
                ->orderByDesc('total_asistieron')
                ->get();

            $puntosDia = \DB::table('puntos_proveedor')
                ->where('id_evento', $evento->ID)
                ->whereDate('fecha', $fStr)
                ->groupBy('usuario')
                ->selectRaw("usuario as proveedor, SUM(puntos) as total_puntos, COUNT(DISTINCT id_participante) as participantes_atendidos")
                ->orderByDesc('total_puntos')
                ->get();

            $puestosDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("COALESCE(NULLIF(TRIM(participante.Puesto), ''), 'Sin Puesto Especificado') as puesto, COUNT(DISTINCT participante.ID) as total_asistentes")
                ->groupBy('puesto')
                ->orderByDesc('total_asistentes')
                ->get();

            $sucursalesDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("participante.Sucursal as sucursal, COUNT(DISTINCT participante.ID) as total_asistentes")
                ->groupBy('participante.Sucursal')
                ->orderByDesc('total_asistentes')
                ->get();

            $salonesDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("COALESCE(NULLIF(TRIM(agenda.Salon), ''), 'Sin Salón Especificado') as salon, COUNT(clase.ID) as total_asistencias")
                ->groupBy('salon')
                ->orderByDesc('total_asistencias')
                ->get();

            $vendedoresDia = \DB::table('participante')
                ->leftJoin('clase', function($join) use ($slotIdsDia) {
                    $join->on('participante.ID', '=', 'clase.ID_Participante')
                        ->whereIn('clase.ID_Agenda', $slotIdsDia)
                        ->where('clase.Asistio', 1);
                })
                ->where('participante.ID_Evento', $evento->ID)
                ->groupBy('participante.Vendedor')
                ->selectRaw("
                    COALESCE(NULLIF(TRIM(participante.Vendedor), ''), 'Sin Vendedor Asignado') as vendedor,
                    COUNT(DISTINCT participante.ID) as total_registrados,
                    COUNT(DISTINCT clase.ID_Participante) as total_asistieron
                ")
                ->orderByDesc('total_registrados')
                ->get()
                ->map(function($item) {
                    $item->ausentes = max(0, $item->total_registrados - $item->total_asistieron);
                    $item->pct_asistencia = $item->total_registrados > 0 ? round(($item->total_asistieron / $item->total_registrados) * 100, 1) : 0;
                    return $item;
                });

            $statsPorDia[$fStr] = [
                'numero_dia'         => $index + 1,
                'fecha_str'          => $fStr,
                'fecha_formateada'   => \Carbon\Carbon::parse($fStr)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY'),
                'asistentes_unicos'  => $partDia->count(),
                'afluencia_horaria'  => $afluenciaDia,
                'actividades'        => $actividadesDia,
                'puntos_proveedores' => $puntosDia,
                'puestos'            => $puestosDia,
                'sucursales'         => $sucursalesDia,
                'salones'            => $salonesDia,
                'vendedores'         => $vendedoresDia,
                'total_puntos_dia'   => $puntosDia->sum('total_puntos'),
            ];
        }

        return view('eventos.estadisticas', compact(
            'evento',
            'totalInscritos',
            'totalAsistieron',
            'totalSinAsistencia',
            'porcentajeAsistencia',
            'fechas',
            'todasLasFechas',
            'dia1Str',
            'dia2Str',
            'countDia1',
            'countDia2',
            'ambosDias',
            'soloDia1',
            'soloDia2',
            'soloUnDia',
            'asistenciaPorHora',
            'asistenciaPorSucursal',
            'asistenciaPorPuesto',
            'topActividades',
            'rankingVendedores',
            'topProveedoresPuntos',
            'salonesGlobal',
            'topPremiosCanjeados',
            'puntosPorFechaHora',
            'statsPorDia'
        ));
    }

    public function exportarEstadisticasExcel(Evento $evento)
    {
        // 1. Inscritos y Aforo Registrado
        $totalInscritos = $evento->participantes()->count();
        $agendaIds = $evento->agenda()->pluck('ID')->toArray();

        $asistentesIds = \DB::table('clase')
            ->whereIn('ID_Agenda', $agendaIds)
            ->where('Asistio', 1)
            ->pluck('ID_Participante')
            ->unique();
        
        $totalAsistieron = $asistentesIds->count();
        $totalSinAsistencia = max(0, $totalInscritos - $totalAsistieron);
        $porcentajeAsistencia = ($totalInscritos > 0) ? round(($totalAsistieron / $totalInscritos) * 100, 1) : 0;

        // Vendedores / Registradores
        $rankingVendedores = \DB::table('participante')
            ->leftJoin('clase', function($join) use ($agendaIds) {
                $join->on('participante.ID', '=', 'clase.ID_Participante')
                    ->whereIn('clase.ID_Agenda', $agendaIds)
                    ->where('clase.Asistio', 1);
            })
            ->where('participante.ID_Evento', $evento->ID)
            ->groupBy('participante.Vendedor')
            ->selectRaw("
                participante.Vendedor as vendedor,
                COUNT(DISTINCT participante.ID) as total_registrados,
                COUNT(DISTINCT clase.ID_Participante) as total_asistieron
            ")
            ->orderByDesc('total_registrados')
            ->get()
            ->map(function($item) {
                $item->vendedor_nombre = $item->vendedor ? $item->vendedor : 'Sin Vendedor Asignado';
                $item->ausentes = max(0, $item->total_registrados - $item->total_asistieron);
                $item->pct_asistencia = $item->total_registrados > 0 ? round(($item->total_asistieron / $item->total_registrados) * 100, 1) : 0;
                return $item;
            });

        // Proveedores Puntos
        $topProveedoresPuntos = \DB::table('puntos_proveedor')
            ->where('id_evento', $evento->ID)
            ->groupBy('usuario')
            ->selectRaw("
                usuario as proveedor,
                SUM(puntos) as total_puntos,
                COUNT(DISTINCT id_participante) as participantes_atendidos,
                COUNT(*) as num_transacciones
            ")
            ->orderByDesc('total_puntos')
            ->get();

        // Sucursales
        $asistenciaPorSucursal = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("participante.Sucursal as sucursal, COUNT(DISTINCT participante.ID) as total_asistentes")
            ->groupBy('participante.Sucursal')
            ->orderByDesc('total_asistentes')
            ->get();

        // Puestos
        $asistenciaPorPuesto = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("
                COALESCE(NULLIF(TRIM(participante.Puesto), ''), 'Sin Puesto Especificado') as puesto,
                COUNT(DISTINCT participante.ID) as total_asistentes
            ")
            ->groupBy('puesto')
            ->orderByDesc('total_asistentes')
            ->get();

        // Actividades
        $topActividades = \DB::table('agenda')
            ->leftJoin('clase', function($join) {
                $join->on('agenda.ID', '=', 'clase.ID_Agenda')->where('clase.Asistio', 1);
            })
            ->where('agenda.ID_Evento', $evento->ID)
            ->groupBy('agenda.ID', 'agenda.Actividad', 'agenda.Fecha', 'agenda.Horario', 'agenda.Salon')
            ->selectRaw('agenda.Actividad as actividad, agenda.Fecha as fecha, agenda.Horario as horario, agenda.Salon as salon, COUNT(clase.ID) as total_asistieron')
            ->orderByDesc('total_asistieron')
            ->get();

        // Afluencia Horaria Global
        $asistenciaPorHora = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("
                SUBSTRING_INDEX(agenda.Horario, '-', 1) as hora_raw,
                COUNT(*) as total_escaneos,
                COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos
            ")
            ->groupBy('hora_raw')
            ->orderBy('hora_raw')
            ->get();

        // Salones Global
        $salonesGlobal = \DB::table('clase')
            ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
            ->where('agenda.ID_Evento', $evento->ID)
            ->where('clase.Asistio', 1)
            ->selectRaw("COALESCE(NULLIF(TRIM(agenda.Salon), ''), 'Sin Salón Especificado') as salon, COUNT(clase.ID) as total_asistencias, COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos")
            ->groupBy('salon')
            ->orderByDesc('total_asistencias')
            ->get();

        // Premios Canjeados
        $topPremiosCanjeados = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->groupBy('premios_evento.ID', 'premios_evento.NombrePremio', 'premios_evento.Disponible')
            ->selectRaw("premios_evento.NombrePremio as premio, premios_evento.Disponible as stock_actual, SUM(canjes.Cantidad) as total_canjeados, COUNT(DISTINCT canjes.ID_Participante) as participantes_canjeadores")
            ->orderByDesc('total_canjeados')
            ->get();

        // Per-Day Stats for Excel sheets
        $agendaPorFecha = $evento->agenda->groupBy('Fecha');
        $period = \Carbon\CarbonPeriod::create($evento->fecha_inicio, $evento->fecha_fin);
        $todasLasFechas = collect($period)->map->format('Y-m-d')
            ->concat($agendaPorFecha->keys())
            ->unique()
            ->sort()
            ->values();

        $statsPorDia = [];
        foreach ($todasLasFechas as $index => $fStr) {
            $slotsDia = $agendaPorFecha->get($fStr, collect());
            $slotIdsDia = $slotsDia->pluck('ID')->toArray();

            $partDia = \DB::table('clase')
                ->whereIn('ID_Agenda', $slotIdsDia)
                ->where('Asistio', 1)
                ->pluck('ID_Participante')
                ->unique();

            $afluenciaDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("
                    SUBSTRING_INDEX(agenda.Horario, '-', 1) as hora_raw,
                    COUNT(*) as total_escaneos,
                    COUNT(DISTINCT clase.ID_Participante) as asistentes_unicos
                ")
                ->groupBy('hora_raw')
                ->orderBy('hora_raw')
                ->get();

            $actividadesDia = \DB::table('agenda')
                ->leftJoin('clase', function($join) {
                    $join->on('agenda.ID', '=', 'clase.ID_Agenda')->where('clase.Asistio', 1);
                })
                ->whereIn('agenda.ID', $slotIdsDia)
                ->groupBy('agenda.ID', 'agenda.Actividad', 'agenda.Horario', 'agenda.Salon')
                ->selectRaw('agenda.Actividad as actividad, agenda.Horario as horario, agenda.Salon as salon, COUNT(clase.ID) as total_asistieron')
                ->orderByDesc('total_asistieron')
                ->get();

            $puntosDia = \DB::table('puntos_proveedor')
                ->where('id_evento', $evento->ID)
                ->whereDate('fecha', $fStr)
                ->groupBy('usuario')
                ->selectRaw("usuario as proveedor, SUM(puntos) as total_puntos, COUNT(DISTINCT id_participante) as participantes_atendidos")
                ->orderByDesc('total_puntos')
                ->get();

            $puestosDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("COALESCE(NULLIF(TRIM(participante.Puesto), ''), 'Sin Puesto Especificado') as puesto, COUNT(DISTINCT participante.ID) as total_asistentes")
                ->groupBy('puesto')
                ->orderByDesc('total_asistentes')
                ->get();

            $sucursalesDia = \DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->join('participante', 'clase.ID_Participante', '=', 'participante.ID')
                ->whereIn('agenda.ID', $slotIdsDia)
                ->where('clase.Asistio', 1)
                ->selectRaw("participante.Sucursal as sucursal, COUNT(DISTINCT participante.ID) as total_asistentes")
                ->groupBy('participante.Sucursal')
                ->orderByDesc('total_asistentes')
                ->get();

            $vendedoresDia = \DB::table('participante')
                ->leftJoin('clase', function($join) use ($slotIdsDia) {
                    $join->on('participante.ID', '=', 'clase.ID_Participante')
                        ->whereIn('clase.ID_Agenda', $slotIdsDia)
                        ->where('clase.Asistio', 1);
                })
                ->where('participante.ID_Evento', $evento->ID)
                ->groupBy('participante.Vendedor')
                ->selectRaw("
                    COALESCE(NULLIF(TRIM(participante.Vendedor), ''), 'Sin Vendedor Asignado') as vendedor,
                    COUNT(DISTINCT participante.ID) as total_registrados,
                    COUNT(DISTINCT clase.ID_Participante) as total_asistieron
                ")
                ->orderByDesc('total_registrados')
                ->get()
                ->map(function($item) {
                    $item->ausentes = max(0, $item->total_registrados - $item->total_asistieron);
                    $item->pct_asistencia = $item->total_registrados > 0 ? round(($item->total_asistieron / $item->total_registrados) * 100, 1) : 0;
                    return $item;
                });

            $statsPorDia[$fStr] = [
                'numero_dia'         => $index + 1,
                'fecha_str'          => $fStr,
                'fecha_formateada'   => \Carbon\Carbon::parse($fStr)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY'),
                'asistentes_unicos'  => $partDia->count(),
                'afluencia_horaria'  => $afluenciaDia,
                'actividades'        => $actividadesDia,
                'puntos_proveedores' => $puntosDia,
                'puestos'            => $puestosDia,
                'sucursales'         => $sucursalesDia,
                'vendedores'         => $vendedoresDia,
                'total_puntos_dia'   => $puntosDia->sum('total_puntos'),
            ];
        }

        $filename = "Reporte_Estadisticas_" . \Str::slug($evento->name_evento) . "_" . date('Ymd_His') . ".xls";

        return response()->streamDownload(function() use (
            $evento, $totalInscritos, $totalAsistieron, $totalSinAsistencia, $porcentajeAsistencia,
            $rankingVendedores, $topProveedoresPuntos, $asistenciaPorSucursal, $asistenciaPorPuesto, $topActividades,
            $asistenciaPorHora, $salonesGlobal, $topPremiosCanjeados, $statsPorDia
        ) {
            echo view('eventos.excel_estadisticas', compact(
                'evento', 'totalInscritos', 'totalAsistieron', 'totalSinAsistencia', 'porcentajeAsistencia',
                'rankingVendedores', 'topProveedoresPuntos', 'asistenciaPorSucursal', 'asistenciaPorPuesto', 'topActividades',
                'asistenciaPorHora', 'salonesGlobal', 'topPremiosCanjeados', 'statsPorDia'
            ))->render();
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
