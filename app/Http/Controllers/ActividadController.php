<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Evento;
use App\Models\Agenda;
use App\Models\Clase;
use App\Models\Participante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActividadController extends Controller
{
    /**
     * Muestra los detalles de una actividad, su agenda e inscritos.
     */
    public function show(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);
        $evento = $actividad->evento;
        
        // Determinar URL de retorno según el origen exacto (from_tab, from, referer)
        $fromTab = $request->input('from_tab');
        $from = $request->input('from');

        if ($fromTab) {
            $backUrl = route('eventos.show', [$evento, 'active_tab' => $fromTab]);
            session(['active_tab' => $fromTab]);
            session(['actividad_back_url_' . $id => $backUrl]);
        } elseif ($from === 'dashboard') {
            $backUrl = url('dashboard');
            session(['actividad_back_url_' . $id => $backUrl]);
        } elseif ($request->headers->get('referer') && !str_contains($request->headers->get('referer'), '/actividades/')) {
            $backUrl = $request->headers->get('referer');
            session(['actividad_back_url_' . $id => $backUrl]);
        } else {
            $backUrl = session('actividad_back_url_' . $id, route('eventos.show', [$evento, 'active_tab' => 'tab-actividades']));
        }

        $selectedHorarioId = $request->input('horario') ?? $request->input('agenda_id') ?? $request->input('slot_id') ?? $request->input('slot');
        $selectedSlot = null;

        if ($selectedHorarioId) {
            $selectedSlot = Agenda::find($selectedHorarioId);
        }

        // Obtener los horarios (agenda) de esta actividad
        $agenda = Agenda::where('ID_Evento', $actividad->ID_Evento)
                        ->where('Actividad', $actividad->Actividad)
                        ->orderBy('Fecha')
                        ->orderBy('Horario')
                        ->get();

        // Obtener las clases/inscripciones de estos horarios
        $agendaIds = $agenda->pluck('ID');
        $todasLasClases = Clase::with(['participante', 'agenda'])
                               ->whereIn('ID_Agenda', $agendaIds)
                               ->get();

        // Si se seleccionó una clase/sesión específica (viniendo desde la agenda)
        if ($selectedSlot) {
            $clases = $todasLasClases->where('ID_Agenda', $selectedSlot->ID);
        } else {
            $clases = $todasLasClases;
        }

        // Estadísticas agregadas por horario
        $agendaStats = $agenda->map(function($slot) use ($todasLasClases, $actividad) {
            $inscritosCount = $todasLasClases->where('ID_Agenda', $slot->ID)->count();
            $capacidad = max(1, $actividad->capacidad);
            $pct = min(100, round(($inscritosCount / $capacidad) * 100, 1));
            return [
                'slot' => $slot,
                'inscritos' => $inscritosCount,
                'porcentaje' => $pct,
            ];
        });

        if ($selectedSlot) {
            $totalInscritos = $clases->count();
            $totalSesiones = 1;
            $capacidadTotal = max(1, $actividad->capacidad);
            $porcentajeGlobal = min(100, round(($totalInscritos / $capacidadTotal) * 100, 1));
        } else {
            $totalInscritos = $todasLasClases->count();
            $totalSesiones = $agenda->count();
            $capacidadTotal = max(1, $actividad->capacidad * max(1, $totalSesiones));
            $porcentajeGlobal = min(100, round(($totalInscritos / $capacidadTotal) * 100, 1));
        }

        return view('actividades.show', compact(
            'actividad', 'evento', 'agenda', 'clases', 'agendaStats',
            'totalInscritos', 'totalSesiones', 'capacidadTotal', 'porcentajeGlobal', 'backUrl', 'selectedSlot'
        ));
    }
    /**
     * Almacena una nueva actividad asociada a un evento.
     */
    public function store(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'Actividad'      => 'required|string|max:255',
            'Descripcion'    => 'nullable|string|max:255',
            'capacidad'      => 'required|integer|min:1',
            'Exclusiva'      => 'nullable|boolean',
            'Puntos_Default' => 'nullable|integer|min:0',
        ]);

        $data['ID_Evento'] = $evento->ID;
        $data['Exclusiva'] = $request->has('Exclusiva');
        $data['Puntos_Default'] = $data['Puntos_Default'] ?? 0;
        $data['Descripcion'] = $data['Descripcion'] ?? 'Sin descripción';

        Actividad::create($data);

        return redirect()->route('eventos.show', [$evento, 'active_tab' => $request->input('active_tab', 'tab-actividades')])->with('success', 'Actividad creada correctamente.');
    }

    /**
     * Actualiza una actividad existente.
     */
    public function update(Request $request, Actividad $actividad)
    {
        $data = $request->validate([
            'Actividad'      => 'required|string|max:255',
            'Descripcion'    => 'nullable|string|max:255',
            'capacidad'      => 'required|integer|min:1',
            'Exclusiva'      => 'nullable|boolean',
            'Puntos_Default' => 'nullable|integer|min:0',
        ]);

        $data['Exclusiva'] = $request->has('Exclusiva');
        $data['Puntos_Default'] = $data['Puntos_Default'] ?? 0;
        $data['Descripcion'] = $data['Descripcion'] ?? 'Sin descripción';

        $actividad->update($data);

        return redirect()->route('eventos.show', [$actividad->ID_Evento, 'active_tab' => $request->input('active_tab', 'tab-actividades')])->with('success', 'Actividad actualizada.');
    }

    /**
     * Elimina una actividad.
     */
    public function destroy(Actividad $actividad)
    {
        $evento_id = $actividad->ID_Evento;
        $actividad->delete();

        return redirect()->route('eventos.show', [$evento_id, 'active_tab' => request('active_tab', 'tab-actividades')])->with('success', 'Actividad eliminada.');
    }

    /* =========================================================================
       MÉTODOS AJAX PARA SCANNER Y ASISTENCIA (Portados de Clase.php)
       ========================================================================= */

        /**
     * Parsea entradas de escáner QR (ej: "ID1Alex Casillas", "ID45", "1") o texto directo.
     */
    private function parseParticipanteInput($rawInput)
    {
        $input = trim((string)$rawInput);
        if (!$input) return ['id' => null, 'name' => null, 'raw' => ''];

        if (preg_match('/^ID\s*[\-_:\s\x{00A0}]*(\d+)(.*)$/ui', $input, $matches)) {
            $cleanName = preg_replace('/^[^\p{L}\p{N}]+/u', '', trim($matches[2]));
            return [
                'id' => $matches[1],
                'name' => $cleanName ?: null,
                'raw' => $input
            ];
        }

        if (is_numeric($input)) {
            return [
                'id' => $input,
                'name' => null,
                'raw' => $input
            ];
        }

        return [
            'id' => null,
            'name' => null,
            'raw' => $input
        ];
    }

    /**
     * Busca un participante en la base de datos interpretando formatos de QR como "ID1Alex Casillas".
     */
    private function buscarParticipantePorInput($rawInput, $idEvento)
    {
        $parsed = $this->parseParticipanteInput($rawInput);
        $id = $parsed['id'];
        $name = $parsed['name'];
        $raw = $parsed['raw'];

        if (!$raw) return null;

        return Participante::where('ID_Evento', $idEvento)
            ->where(function($q) use ($id, $name, $raw) {
                if ($id !== null) {
                    $q->where('ID', $id)
                      ->orWhere('Telefono', $id);
                }
                if (!empty($name)) {
                    $q->orWhere('Nombre', 'like', "%{$name}%");
                }
                $q->orWhere('RFC', $raw)
                  ->orWhere('Telefono', $raw)
                  ->orWhere('Nombre', 'like', "%{$raw}%");
            })
            ->first();
    }

    public function buscarParticipantes(Request $request, Actividad $actividad)
    {
        $rawQuery = trim($request->input('q', $request->input('busqueda', '')));
        $horarioId = $request->input('slot_id', $request->input('horario', null));

        // Obtener IDs de agenda
        if ($horarioId) {
            $agendaIds = collect([$horarioId]);
        } else {
            $agendaIds = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->pluck('ID');
        }

        $participantesQuery = Participante::with(['clases' => function($q) use ($agendaIds) {
                $q->whereIn('ID_Agenda', $agendaIds);
            }])
            ->where('ID_Evento', $actividad->ID_Evento);

        if ($rawQuery !== '') {
            $parsed = $this->parseParticipanteInput($rawQuery);
            $queryId = $parsed['id'];
            $queryName = $parsed['name'];

            $participantesQuery->where(function($q) use ($rawQuery, $queryId, $queryName) {
                if ($queryId !== null) {
                    $q->where('ID', $queryId)->orWhere('Telefono', $queryId);
                }
                if (!empty($queryName)) {
                    $q->orWhere('Nombre', 'like', "%{$queryName}%");
                }
                $q->orWhere('Nombre', 'like', "%{$rawQuery}%")
                  ->orWhere('Telefono', 'like', "%{$rawQuery}%")
                  ->orWhere('Proveedor', 'like', "%{$rawQuery}%");
            });
        } else {
            // Si no hay búsqueda, mostramos SOLO los que ya están inscritos en esta actividad/horario
            $participantesQuery->whereHas('clases', function($q) use ($agendaIds) {
                $q->whereIn('ID_Agenda', $agendaIds);
            });
        }

        $participantes = $participantesQuery->limit(50)->get();

        return view('actividades._tabla_asistencia', compact('participantes', 'actividad'));
    }

    public function marcarAsistencia(Request $request, Actividad $actividad)
    {
        $idInput = trim($request->input('id_participante', ''));
        $horarioId = $request->input('horario', null);

        if (!$idInput) {
            return response()->json(['ok' => false, 'msg' => 'Por favor ingresa o escanea un ID de participante']);
        }

        $participante = $this->buscarParticipantePorInput($idInput, $actividad->ID_Evento);

        if (!$participante) {
            return response()->json(['ok' => false, 'msg' => "No se encontró ningún participante registrado con el ID/Código: {$idInput}"]);
        }

        $idPart = $participante->ID;

        if ($horarioId) {
            $agendaIds = collect([$horarioId]);
        } else {
            $agendaIds = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->pluck('ID');
        }

        $clase = Clase::where('ID_Participante', $idPart)
                      ->whereIn('ID_Agenda', $agendaIds)
                      ->first();

        // Si no está inscrito en esta clase/sesión, lo inscribimos y marcamos asistencia
        if (!$clase) {
            return $this->inscribirParticipante($request, $actividad);
        }

        if ($clase->Asistio) {
            return response()->json(['ok' => false, 'msg' => "{$participante->Nombre} ya tiene su asistencia registrada previamente."]);
        }

        $clase->Asistio = true;
        $clase->Asistencia_Fecha = Carbon::now();
        $clase->Asistencia_Usuario = Auth::id() ?? 0;
        $clase->save();

        if ($actividad->Puntos_Default > 0) {
            $participante->Puntos += $actividad->Puntos_Default;
            $participante->save();
        }

        return response()->json(['ok' => true, 'msg' => "Asistencia registrada correctamente para {$participante->Nombre}"]);
    }

    public function inscribirParticipante(Request $request, Actividad $actividad)
    {
        $idInput = trim($request->input('id_participante', ''));
        $horarioId = $request->input('horario', null);

        $participante = $this->buscarParticipantePorInput($idInput, $actividad->ID_Evento);

        if (!$participante) {
            return response()->json(['ok' => false, 'msg' => "No se encontró ningún participante registrado con el ID/Código: {$idInput}"]);
        }

        $idPart = $participante->ID;

        if ($horarioId) {
            $agenda = Agenda::find($horarioId);
        } else {
            $agenda = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->first();
        }

        if (!$agenda) {
            return response()->json(['ok' => false, 'msg' => 'Esta actividad no tiene horarios asignados']);
        }

        $claseExitente = Clase::where('ID_Participante', $idPart)->whereIn('ID_Agenda', [$agenda->ID])->first();
        if ($claseExitente) {
            if (!$claseExitente->Asistio) {
                $claseExitente->Asistio = true;
                $claseExitente->Asistencia_Fecha = Carbon::now();
                $claseExitente->Asistencia_Usuario = Auth::id() ?? 0;
                $claseExitente->save();
                
                if ($actividad->Puntos_Default > 0) {
                    $participante->Puntos += $actividad->Puntos_Default;
                    $participante->save();
                }
                return response()->json(['ok' => true, 'msg' => "Asistencia registrada correctamente para {$participante->Nombre}"]);
            }
            return response()->json(['ok' => false, 'msg' => "{$participante->Nombre} ya está inscrito y cuenta con asistencia."]);
        }

        Clase::create([
            'ID_Agenda' => $agenda->ID,
            'ID_Participante' => $idPart,
            'Asistio' => true,
            'Asistencia_Fecha' => Carbon::now(),
            'Asistencia_Usuario' => Auth::id() ?? 0,
            'Tipo_Inscripcion' => 1,
        ]);

        if ($actividad->Puntos_Default > 0) {
            $participante->Puntos += $actividad->Puntos_Default;
            $participante->save();
        }

        return response()->json(['ok' => true, 'msg' => "{$participante->Nombre} fue inscrito y se marcó su asistencia correctamente"]);
    }

    public function registroRapido(Request $request, Actividad $actividad)
    {
        $request->validate([
            'nombre' => 'required|string',
            'telefono' => 'required|string',
            'proveedor' => 'required|string'
        ]);

        $horarioId = $request->input('horario', null);

        if ($horarioId) {
            $agenda = Agenda::find($horarioId);
        } else {
            $agenda = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->first();
        }

        if (!$agenda) {
            return response()->json(['ok' => false, 'msg' => 'Esta actividad no tiene horarios asignados']);
        }

        // Crear participante
        $participante = Participante::create([
            'ID_Evento' => $actividad->ID_Evento,
            'Nombre' => $request->nombre,
            'Telefono' => $request->telefono,
            'Proveedor' => $request->proveedor,
            'Puntos' => 0,
            'Sucursal' => 'N/A',
            'Vendedor' => 'N/A',
            'RFC' => 'N/A'
        ]);

        // Inscribirlo
        Clase::create([
            'ID_Agenda' => $agenda->ID,
            'ID_Participante' => $participante->ID,
            'Asistio' => true, // En registro rápido asumimos que ya asistió
            'Asistencia_Fecha' => Carbon::now(),
            'Asistencia_Usuario' => Auth::id() ?? 0,
            'Tipo_Inscripcion' => 2,
        ]);

        if ($actividad->Puntos_Default > 0) {
            $participante->Puntos += $actividad->Puntos_Default;
            $participante->save();
        }

        return response()->json(['ok' => true, 'msg' => 'Registrado e inscrito correctamente']);
    }

    public function toggleAsistencia(Request $request, Actividad $actividad)
    {
        // Solo el rol Administrador puede quitar asistencias
        if (!auth()->check() || auth()->user()->Rol !== 'Administrador') {
            return response()->json(['ok' => false, 'msg' => 'Solo un usuario Administrador tiene permisos para quitar asistencias.']);
        }

        $idPart = $request->input('id_participante');
        $horarioId = $request->input('horario', null) ?? $request->input('id_agenda', null);

        if ($horarioId) {
            $agendaIds = collect([$horarioId]);
        } else {
            $agendaIds = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->pluck('ID');
        }

        $clase = Clase::where('ID_Participante', $idPart)
                      ->whereIn('ID_Agenda', $agendaIds)
                      ->first();

        if (!$clase) {
            return response()->json(['ok' => false, 'msg' => 'El participante no tiene registro de clase en este horario.']);
        }

        $nuevoEstado = !$clase->Asistio;
        $clase->Asistio = $nuevoEstado ? 1 : 0;
        $clase->Asistencia_Fecha = $nuevoEstado ? Carbon::now() : null;
        $clase->Asistencia_Usuario = Auth::id() ?? 0;
        $clase->save();

        $participante = Participante::find($idPart);
        if ($actividad->Puntos_Default > 0 && $participante) {
            if ($nuevoEstado) {
                $participante->Puntos += $actividad->Puntos_Default;
            } else {
                $participante->Puntos = max(0, (int)$participante->Puntos - (int)$actividad->Puntos_Default);
            }
            $participante->save();
        }

        $nombre = $participante ? $participante->Nombre : 'El participante';
        $msg = $nuevoEstado ? "Asistencia registrada para {$nombre}" : "Asistencia removida correctamente para {$nombre}";
        return response()->json(['ok' => true, 'msg' => $msg]);
    }
}
