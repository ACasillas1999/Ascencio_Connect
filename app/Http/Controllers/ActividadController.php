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
    public function show($id)
    {
        $actividad = Actividad::findOrFail($id);
        $evento = $actividad->evento;
        
        // Obtener los horarios (agenda) de esta actividad
        $agenda = Agenda::where('ID_Evento', $actividad->ID_Evento)
                        ->where('Actividad', $actividad->Actividad)
                        ->orderBy('Fecha')
                        ->orderBy('Horario')
                        ->get();

        // Obtener las clases/inscripciones de estos horarios
        $agendaIds = $agenda->pluck('ID');
        $clases = Clase::with(['participante', 'agenda'])
                       ->whereIn('ID_Agenda', $agendaIds)
                       ->get();

        return view('actividades.show', compact('actividad', 'evento', 'agenda', 'clases'));
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

        return redirect()->route('eventos.show', $evento)->with('success', 'Actividad creada correctamente.');
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

        return redirect()->route('eventos.show', $actividad->ID_Evento)->with('success', 'Actividad actualizada.');
    }

    /**
     * Elimina una actividad.
     */
    public function destroy(Actividad $actividad)
    {
        $evento_id = $actividad->ID_Evento;
        $actividad->delete();

        return redirect()->route('eventos.show', $evento_id)->with('success', 'Actividad eliminada.');
    }

    /* =========================================================================
       MÉTODOS AJAX PARA SCANNER Y ASISTENCIA (Portados de Clase.php)
       ========================================================================= */

    public function buscarParticipantes(Request $request, Actividad $actividad)
    {
        $query = $request->input('busqueda', '');
        $horarioId = $request->input('horario', null);

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

        if ($query) {
            // Si hay búsqueda, buscamos en todo el evento (para poder inscribir nuevos)
            $participantesQuery->where(function($q) use ($query) {
                $q->where('Nombre', 'like', "%{$query}%")
                   ->orWhere('Telefono', 'like', "%{$query}%")
                   ->orWhere('Proveedor', 'like', "%{$query}%")
                   ->orWhere('ID', $query);
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
        $idPart = $request->input('id_participante');
        $horarioId = $request->input('horario', null);

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

        // Si no está inscrito, lo inscribimos y le marcamos asistencia automáticamente
        if (!$clase) {
            return $this->inscribirParticipante($request, $actividad);
        }

        if ($clase->Asistio) {
            return response()->json(['ok' => false, 'msg' => 'Ya se había registrado su asistencia previamente']);
        }

        $clase->Asistio = true;
        $clase->Asistencia_Fecha = Carbon::now();
        $clase->Asistencia_Usuario = Auth::id() ?? 0;
        $clase->save();

        if ($actividad->Puntos_Default > 0) {
            $participante = Participante::find($idPart);
            if ($participante) {
                $participante->Puntos += $actividad->Puntos_Default;
                $participante->save();
            }
        }

        return response()->json(['ok' => true, 'msg' => 'Asistencia registrada correctamente']);
    }

    public function inscribirParticipante(Request $request, Actividad $actividad)
    {
        $idPart = $request->input('id_participante');
        $horarioId = $request->input('horario', null);

        $participante = Participante::where('ID', $idPart)->where('ID_Evento', $actividad->ID_Evento)->first();
        
        if (!$participante) {
            return response()->json(['ok' => false, 'msg' => 'Participante no encontrado']);
        }

        if ($horarioId) {
            $agenda = Agenda::find($horarioId);
        } else {
            $agenda = Agenda::where('ID_Evento', $actividad->ID_Evento)
                ->where('Actividad', $actividad->Actividad)
                ->first(); // Tomamos el primer horario por defecto
        }

        if (!$agenda) {
            return response()->json(['ok' => false, 'msg' => 'Esta actividad no tiene horarios asignados']);
        }

        $claseExitente = Clase::where('ID_Participante', $idPart)->whereIn('ID_Agenda', [$agenda->ID])->first();
        if ($claseExitente) {
            // Ya estaba inscrito, veamos si le falta asistencia
            if (!$claseExitente->Asistio) {
                $claseExitente->Asistio = true;
                $claseExitente->Asistencia_Fecha = Carbon::now();
                $claseExitente->Asistencia_Usuario = Auth::id() ?? 0;
                $claseExitente->save();
                
                if ($actividad->Puntos_Default > 0) {
                    $participante->Puntos += $actividad->Puntos_Default;
                    $participante->save();
                }
                return response()->json(['ok' => true, 'msg' => 'Asistencia registrada correctamente (Ya estaba inscrito)']);
            }
            return response()->json(['ok' => false, 'msg' => 'El participante ya estaba inscrito y ya tenía asistencia']);
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

        return response()->json(['ok' => true, 'msg' => 'Participante inscrito y asistencia marcada correctamente']);
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
}
