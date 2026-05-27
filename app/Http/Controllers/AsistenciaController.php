<?php

namespace App\Http\Controllers;

use App\Models\Clase;
use App\Models\Agenda;
use App\Models\Participante;
use App\Models\PuntosRfc;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Marcar asistencia a una agenda/actividad para un participante
     */
    public function marcarAsistencia(Request $request)
    {
        $request->validate([
            'id_agenda' => 'required|integer|exists:agenda,ID',
            'id_participante' => 'required|integer|exists:participante,ID',
        ]);

        $agenda = Agenda::with('evento')->findOrFail($request->id_agenda);
        $participante = Participante::findOrFail($request->id_participante);
        $evento = $agenda->evento;

        if (!$evento) {
            return response()->json(['success' => false, 'message' => 'Evento no encontrado.']);
        }

        // Buscar o crear el registro en la clase (inscripción)
        $clase = Clase::firstOrCreate(
            ['ID_Agenda' => $agenda->ID, 'ID_Participante' => $participante->ID],
            ['Asistio' => 0, 'Tipo_Inscripcion' => 0]
        );

        if ($clase->Asistio) {
            return response()->json(['success' => false, 'message' => 'El participante ya tiene asistencia registrada para esta actividad.']);
        }

        // Marcar asistencia
        $clase->Asistio = 1;
        $clase->Asistencia_Fecha = Carbon::now();
        $clase->Asistencia_Usuario = auth()->id() ?? null;
        $clase->save();

        // Lógica Dinámica de Puntos
        $puntos = $agenda->Puntos_Asistencia; // Los puntos que otorga esta actividad

        if ($puntos > 0) {
            if ($evento->tipo_puntos === 'individual') {
                // Sumar al participante
                $participante->Puntos = ($participante->Puntos ?? 0) + $puntos;
                $participante->save();

            } elseif ($evento->tipo_puntos === 'grupal') {
                // Sumar a la "billetera grupal" por RFC
                $puntosRfc = PuntosRfc::firstOrCreate(
                    ['RFC' => $participante->RFC, 'ID_Evento' => $evento->ID],
                    ['Puntos' => 0]
                );
                $puntosRfc->Puntos += $puntos;
                $puntosRfc->save();
            }
            // Si es 'ninguno', no se hace nada
        }

        return response()->json([
            'success' => true,
            'message' => 'Asistencia registrada correctamente.',
            'puntos_otorgados' => $evento->tipo_puntos === 'ninguno' ? 0 : $puntos,
            'tipo_puntos' => $evento->tipo_puntos
        ]);
    }
}
