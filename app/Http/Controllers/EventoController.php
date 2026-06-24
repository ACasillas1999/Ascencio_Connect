<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function index()
    {
        // Temporal: Ejecutar migración SQL
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

        $eventos = Evento::withCount('participantes')
            ->orderByDesc('fecha_inicio')
            ->paginate(15);
        return view('eventos.index', compact('eventos'));
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
            'machote_gafete'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'machote_horario'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
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
        $evento->loadCount(['participantes', 'actividades', 'agenda']);
        $participantes = $evento->participantes()->paginate(20);
        
        $actividades = $evento->actividades()->orderBy('Actividad')->get();
        $agenda = $evento->agenda()->orderBy('Fecha')->orderBy('Horario')->get();
        
        $proveedores = \DB::table('proveedor_evento')
            ->where('ID_Evento', $evento->ID)
            ->get();
        
        // Generar previsualización del gafete y horario
        $imageService = new \App\Services\ImageService();
        $mockGafete = $imageService->generarMockGafete($evento);
        $mockHorario = $imageService->generarMockHorario($evento);
        
        $ubicacionModel = \App\Models\Ubicacion::where('Nombre', $evento->ubicacion)->first();
        $numSalones = $ubicacionModel ? $ubicacionModel->Salones : 1;
        
        $premios = \App\Models\PremioEvento::where('ID_Evento', $evento->ID)->get();
        
        return view('eventos.show', compact('evento', 'participantes', 'actividades', 'agenda', 'proveedores', 'mockGafete', 'mockHorario', 'numSalones', 'premios'));
    }

    public function edit(Evento $evento)
    {
        $ubicaciones = Ubicacion::orderBy('Nombre')->get();
        return view('eventos.edit', compact('evento', 'ubicaciones'));
    }

    public function update(Request $request, Evento $evento)
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
            'machote_gafete'       => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'machote_horario'      => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'enviar_whatsapp_auto' => 'nullable|boolean',
            'clases_obligatorias'  => 'nullable|boolean',
            'wa_template_name'     => 'nullable|string|max:255',
            'gafete_qr_x'          => 'nullable|integer',
            'gafete_qr_y'          => 'nullable|integer',
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
        return redirect()->route('eventos.show', $evento)->with('success', 'Evento actualizado.');
    }

    public function destroy(Evento $evento)
    {
        $evento->delete();
        return redirect()->route('eventos.index')->with('success', 'Evento eliminado.');
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
            foreach ($p->canjes as $canje) {
                if ($canje->premio && str_contains(strtolower($canje->premio->NombrePremio), 'boleto')) {
                    $boletosExtras += $canje->Cantidad;
                }
            }
            
            $totalEntries = 1 + $boletosExtras;
            
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

        $premiosRaw = \App\Models\PremioEvento::where('ID_Evento', $evento->ID)->get();
        $premios = $premiosRaw->map(function($pr) {
            $colors = ['#ffeb3b', '#00a0e9', '#ff9500', '#76c336', '#e60012'];
            return [
                'id' => 'pr_' . $pr->ID,
                'name' => $pr->NombrePremio,
                'color' => $colors[array_rand($colors)],
                'type' => $pr->TipoPremio ?? 'sorteo'
            ];
        })->values();

        return view('eventos.sorteo', compact('evento', 'participantes', 'premios'));
    }
}
