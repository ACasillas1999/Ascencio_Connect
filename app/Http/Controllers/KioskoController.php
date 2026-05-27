<?php

namespace App\Http\Controllers;

use App\Models\PremioEvento;
use App\Models\Participante;
use App\Models\Canje;
use App\Models\PuntosRfc;
use Illuminate\Http\Request;

class KioskoController extends Controller
{
    /**
     * Muestra la interfaz del Kiosko para un participante o RFC
     */
    public function index(Request $request)
    {
        // ... (Render view logic will be added later)
    }

    /**
     * Procesa el canje de un premio restando los puntos correspondientes
     */
    public function canjear(Request $request)
    {
        $request->validate([
            'id_premio' => 'required|integer|exists:premios_evento,ID',
            'id_participante' => 'required|integer|exists:participante,ID',
            'cantidad' => 'required|integer|min:1'
        ]);

        $premio = PremioEvento::with('evento')->findOrFail($request->id_premio);
        $participante = Participante::findOrFail($request->id_participante);
        $evento = $premio->evento;

        if (!$evento) {
            return response()->json(['success' => false, 'message' => 'Evento no encontrado.']);
        }

        if ($evento->tipo_puntos === 'ninguno') {
            return response()->json(['success' => false, 'message' => 'Este evento no utiliza un sistema de puntos.']);
        }

        $puntosNecesarios = $premio->PuntosNecesarios * $request->cantidad;
        $puntosDisponibles = 0;

        // Determinar puntos disponibles según la configuración del evento
        if ($evento->tipo_puntos === 'individual') {
            $puntosDisponibles = $participante->Puntos ?? 0;
            
            if ($puntosDisponibles < $puntosNecesarios) {
                return response()->json(['success' => false, 'message' => 'Puntos insuficientes (Individual).']);
            }
            
            // Restar puntos al participante
            $participante->Puntos -= $puntosNecesarios;
            $participante->save();

        } elseif ($evento->tipo_puntos === 'grupal') {
            $puntosRfc = PuntosRfc::where('RFC', $participante->RFC)
                                  ->where('ID_Evento', $evento->ID)
                                  ->first();
            
            $puntosDisponibles = $puntosRfc ? $puntosRfc->Puntos : 0;

            if ($puntosDisponibles < $puntosNecesarios) {
                return response()->json(['success' => false, 'message' => 'Puntos insuficientes en el grupo (RFC).']);
            }

            // Restar puntos al grupo RFC
            $puntosRfc->Puntos -= $puntosNecesarios;
            $puntosRfc->save();
        }

        // Registrar el canje
        Canje::create([
            'ID_Evento' => $evento->ID,
            'ID_Participante' => $participante->ID,
            'ID_Premio' => $premio->ID,
            'Cantidad' => $request->cantidad,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Canje realizado con éxito.',
            'puntos_restantes' => $puntosDisponibles - $puntosNecesarios
        ]);
    }
}
