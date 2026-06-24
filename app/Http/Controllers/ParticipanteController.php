<?php

namespace App\Http\Controllers;

use App\Models\Participante;
use App\Models\Evento;
use Illuminate\Http\Request;

class ParticipanteController extends Controller
{
    public function index(Request $request)
    {
        $query = Participante::with('evento');

        if ($request->filled('evento')) {
            $query->where('ID_Evento', $request->evento);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('Nombre', 'like', "%$s%")
                  ->orWhere('RFC', 'like', "%$s%")
                  ->orWhere('Telefono', 'like', "%$s%")
                  ->orWhere('Sucursal', 'like', "%$s%");
            });
        }

        if (auth()->check() && auth()->user()->Rol === 'Vendedor') {
            $query->whereHas('evento', function ($q) {
                $q->whereIn('estado', ['EN CURSO', 'PRÓXIMO']);
            });
            $eventos = Evento::whereIn('estado', ['EN CURSO', 'PRÓXIMO'])->orderByDesc('fecha_inicio')->get();
        } else {
            $eventos = Evento::orderByDesc('fecha_inicio')->get();
        }

        $participantes = $query->orderBy('Nombre')->paginate(25)->withQueryString();

        return view('participantes.index', compact('participantes', 'eventos'));
    }

    public function show(Participante $participante)
    {
        $participante->load(['evento', 'clases.agenda', 'canjes.premio', 'puntosProveedor']);
        return view('participantes.show', compact('participante'));
    }

    public function edit(Participante $participante)
    {
        $eventos = Evento::orderByDesc('fecha_inicio')->get();
        return view('participantes.edit', compact('participante', 'eventos'));
    }

    public function update(Request $request, Participante $participante)
    {
        $data = $request->validate([
            'Nombre'   => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\b(lic|ing|ingeniero|licenciado|arq|arquitecto|dr|doctor|mtra|mtro|maestro|maestra)\b\.?/i', $value)) {
                        $fail('El campo Nombre no debe contener títulos (Lic, Ing, Ingeniero, etc.). Ingresa únicamente el nombre real.');
                    }
                }
            ],
            'RFC'      => 'required|string|max:20',
            'Telefono' => 'required|string|max:15',
            'Sucursal' => 'nullable|string|max:100',
            'Vendedor' => 'nullable|string|max:100',
            'Proveedor'=> 'nullable|string|max:255',
            'Puesto'   => 'nullable|string|max:100',
            'Puntos'   => 'nullable|integer|min:0',
        ]);

        $participante->update($data);
        return redirect()->route('participantes.show', $participante)->with('success', 'Participante actualizado.');
    }

    public function destroy(Participante $participante)
    {
        $participante->delete();
        return redirect()->route('participantes.index')->with('success', 'Participante eliminado.');
    }

    // --- NUEVOS MÉTODOS PARA REGISTRO DE PARTICIPANTE ---

    public function create()
    {
        if (auth()->check() && auth()->user()->Rol === 'Vendedor') {
            $eventos = Evento::whereIn('estado', ['EN CURSO', 'PRÓXIMO'])->orderByDesc('fecha_inicio')->get();
        } else {
            $eventos = Evento::orderByDesc('fecha_inicio')->get();
        }
        return view('participantes.create', compact('eventos'));
    }

    public function store(Request $request)
    {
        if ($request->filled('Nombre_P') && $request->filled('Apellido_P')) {
            $request->merge([
                'Nombre' => trim($request->input('Nombre_P') . ' ' . $request->input('Apellido_P'))
            ]);
        }

        $data = $request->validate([
            'ID_Evento' => 'required|integer|exists:evento,ID',
            'Nombre'    => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    if (preg_match('/\b(lic|ing|ingeniero|licenciado|arq|arquitecto|dr|doctor|mtra|mtro|maestro|maestra)\b\.?/i', $value)) {
                        $fail('El campo Nombre no debe contener títulos (Lic, Ing, Ingeniero, etc.). Ingresa únicamente el nombre real.');
                    }
                }
            ],
            'RFC'       => 'required|string|max:20',
            'Telefono'  => 'required|string|max:15',
            'Sucursal'  => 'nullable|string|max:100',
            'Vendedor'  => 'nullable|string|max:100',
            'Proveedor' => 'nullable|string|max:255',
            'Puesto'    => 'nullable|string|max:100',
            'actividades'=> 'array',
        ]);

        $evento = Evento::findOrFail($data['ID_Evento']);

        // Verificación de Teléfono duplicado
        $exists = Participante::where('Telefono', $data['Telefono'])->where('ID_Evento', $evento->ID)->exists();
        if ($exists) {
            return back()->withInput()->withErrors(['Telefono' => 'Este teléfono ya está registrado en el evento.']);
        }

        // Verificación de clases obligatorias
        if ($evento->clases_obligatorias && empty($data['actividades'])) {
            return back()->withInput()->withErrors(['actividades' => 'Es obligatorio seleccionar al menos una clase/actividad para este evento.']);
        }

        // Verificación de solapamiento de horarios en la agenda
        if (!empty($data['actividades']) && count($data['actividades']) > 1) {
            $agendasSeleccionadas = \App\Models\Agenda::whereIn('ID', $data['actividades'])->get();
            
            $parseHorario = function($horario) {
                $partes = explode('-', $horario);
                $inicioStr = trim($partes[0]);
                $finStr = isset($partes[1]) ? trim($partes[1]) : null;
                $p = function($timeStr) {
                    $t = explode(':', $timeStr);
                    $h = isset($t[0]) ? (int)$t[0] : 0;
                    $m = isset($t[1]) ? (int)$t[1] : 0;
                    return $h * 60 + $m;
                };
                $inicio = $p($inicioStr);
                return [$inicio, $finStr ? $p($finStr) : $inicio + 60];
            };

            foreach ($agendasSeleccionadas as $a1) {
                foreach ($agendasSeleccionadas as $a2) {
                    if ($a1->ID >= $a2->ID) continue;
                    if ($a1->Fecha == $a2->Fecha) {
                        $r1 = $parseHorario($a1->Horario);
                        $r2 = $parseHorario($a2->Horario);
                        if ($r1[0] < $r2[1] && $r1[1] > $r2[0]) {
                            return back()->withInput()->withErrors(['actividades' => "Las actividades '{$a1->Actividad}' y '{$a2->Actividad}' se solapan en su horario."]);
                        }
                    }
                }
            }
        }

        // Verificación de exclusividad por Rol
        if (!empty($data['actividades'])) {
            $userRol = auth()->check() ? auth()->user()->Rol : null;
            if ($userRol !== 'Administrador' && $userRol !== 'Gerente') {
                $exclusivasCount = \App\Models\Agenda::whereIn('agenda.ID', $data['actividades'])
                    ->join('actividades', function($join) {
                        $join->on('agenda.ID_Evento', '=', 'actividades.ID_Evento')
                             ->on('agenda.Actividad', '=', 'actividades.Actividad');
                    })
                    ->where('actividades.Exclusiva', 1)
                    ->count();

                if ($exclusivasCount > 0) {
                    return back()->withInput()->withErrors(['actividades' => 'Has seleccionado una o más actividades exclusivas que solo pueden ser agendadas por un Gerente.']);
                }
            }
        }

        // Crear Participante
        $participante = Participante::create($data);

        // Guardar Clases (Inscripciones)
        if (!empty($data['actividades'])) {
            foreach ($data['actividades'] as $id_agenda) {
                // Aquí iría la lógica de exclusividad o solapamiento si se requiere
                \App\Models\Clase::create([
                    'ID_Agenda' => $id_agenda,
                    'ID_Participante' => $participante->ID,
                    'Tipo_Inscripcion' => 0
                ]);
            }
        }

        // Generar Gafete y Horario
        $imageService = new \App\Services\ImageService();
        $gafetePath = $imageService->generarGafete($participante);
        $horarioPath = $imageService->generarHorario($participante);

        // Envío de WhatsApp si la configuración global del evento lo indica
        if ($evento->enviar_whatsapp_auto) {
            $whatsAppService = new \App\Services\WhatsAppService();
            // Generar un token cifrado para el enlace (similar a lo que tenías)
            // Aquí usamos base64 de manera temporal, o podrías usar openssl_encrypt
            $token = urlencode(base64_encode($participante->ID . '|' . $evento->ID));
            $whatsAppService->enviarPlantilla($participante, $evento, $token);
        }

        return redirect()->route('participantes.show', $participante)->with('success', 'Participante registrado exitosamente.');
    }

    public function getAgenda(Evento $evento)
    {
        $userRol = auth()->check() ? auth()->user()->Rol : null;

        $agenda = \App\Models\Agenda::where('agenda.ID_Evento', $evento->ID)
            ->leftJoin('actividades', function($join) {
                $join->on('agenda.ID_Evento', '=', 'actividades.ID_Evento')
                     ->on('agenda.Actividad', '=', 'actividades.Actividad');
            })
            ->select('agenda.*', 'actividades.Exclusiva')
            ->orderBy('agenda.Fecha')
            ->orderBy('agenda.Horario')
            ->get();

        // Ocultar las actividades exclusivas a quienes no sean Gerente o Administrador
        if ($userRol !== 'Administrador' && $userRol !== 'Gerente') {
            $agenda = $agenda->reject(function($item) {
                return $item->Exclusiva == 1;
            })->values();
        }

        return response()->json($agenda);
    }

    public function searchByPhone($telefono)
    {
        $participante = Participante::where('Telefono', $telefono)
            ->orderBy('ID', 'desc')
            ->first();

        if ($participante) {
            return response()->json([
                'success' => true,
                'data' => [
                    'Nombre' => $participante->Nombre,
                    'RFC' => $participante->RFC,
                    'Sucursal' => $participante->Sucursal,
                    'Vendedor' => $participante->Vendedor,
                    'Proveedor' => $participante->Proveedor,
                    'Puesto' => $participante->Puesto,
                ]
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function searchPhonesList(Request $request)
    {
        $q = $request->query('q');
        if (!$q || strlen($q) < 3) {
            return response()->json([]);
        }

        // Obtener los participantes únicos por teléfono
        $participantes = Participante::where('Telefono', 'like', "%{$q}%")
            ->select('Telefono', 'Nombre', 'RFC', 'Sucursal', 'Vendedor', 'Proveedor', 'Puesto')
            ->orderBy('ID', 'desc')
            ->get()
            ->unique('Telefono')
            ->values()
            ->take(10); // Límite de 10 resultados para el dropdown

        return response()->json($participantes);
    }

    public function globalProfile($telefono)
    {
        $participantes = Participante::where('Telefono', $telefono)
            ->with(['evento', 'clases.agenda', 'canjes.premio'])
            ->orderBy('ID', 'desc')
            ->get();

        if ($participantes->isEmpty()) {
            return redirect()->route('participantes.index')->with('error', 'No se encontró perfil para ese teléfono.');
        }

        $cliente = $participantes->first(); // Datos más recientes
        $totalPuntos = $participantes->sum('Puntos');
        $eventosAsistidos = $participantes->count();

        return view('clientes.perfil', compact('participantes', 'cliente', 'totalPuntos', 'eventosAsistidos', 'telefono'));
    }
}
