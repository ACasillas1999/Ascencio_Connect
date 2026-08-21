<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Canje;
use App\Models\PremioEvento;
use App\Models\Participante;
use Illuminate\Http\Request;

class CanjeController extends Controller
{
    /**
     * Módulo de Canjes para un evento específico.
     */
    public function index(Evento $evento)
    {
        $premios = PremioEvento::where('ID_Evento', $evento->ID)->where('TipoPremio', 'puntos')->get();

        // Estadísticas
        $totalCanjes = Canje::where('ID_Evento', $evento->ID)->count();
        $totalPremiosCanjeados = Canje::where('ID_Evento', $evento->ID)->sum('Cantidad');

        // Últimos canjes del evento para la vista inicial
        $ultimosCanjes = Canje::where('ID_Evento', $evento->ID)
            ->with(['participante', 'premio'])
            ->orderByDesc('Fecha')
            ->limit(10)
            ->get();

        return view('canjes.index', compact('evento', 'premios', 'totalCanjes', 'totalPremiosCanjeados', 'ultimosCanjes'));
    }

    /**
     * Reporte global de canjes del evento.
     */
    public function reporte(Evento $evento)
    {
        $canjes = Canje::where('ID_Evento', $evento->ID)
            ->with(['participante', 'premio'])
            ->orderByDesc('Fecha')
            ->paginate(50);

        $premios = PremioEvento::where('ID_Evento', $evento->ID)->get();

        // Estadísticas generales
        $totalCanjes = Canje::where('ID_Evento', $evento->ID)->count();
        $totalPremiosEntregados = Canje::where('ID_Evento', $evento->ID)->sum('Cantidad');

        // Resumen por premio
        $resumenPorPremio = \DB::table('canjes')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->groupBy('canjes.ID_Premio', 'premios_evento.NombrePremio', 'premios_evento.PuntosNecesarios', 'premios_evento.Disponible')
            ->selectRaw('premios_evento.NombrePremio as nombre, premios_evento.PuntosNecesarios as puntos, premios_evento.Disponible as stock, SUM(canjes.Cantidad) as total_canjeados, COUNT(*) as num_canjes')
            ->get();

        // Top participantes que más han canjeado
        $topParticipantes = \DB::table('canjes')
            ->join('participante', 'canjes.ID_Participante', '=', 'participante.ID')
            ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
            ->where('canjes.ID_Evento', $evento->ID)
            ->groupBy('canjes.ID_Participante', 'participante.Nombre', 'participante.Sucursal')
            ->selectRaw('participante.Nombre as nombre, participante.Sucursal as sucursal, COUNT(*) as num_canjes, SUM(canjes.Cantidad) as total_premios, SUM(premios_evento.PuntosNecesarios * canjes.Cantidad) as puntos_gastados')
            ->orderByDesc('puntos_gastados')
            ->limit(20)
            ->get();

        return view('canjes.reporte', compact('evento', 'canjes', 'premios', 'totalCanjes', 'totalPremiosEntregados', 'resumenPorPremio', 'topParticipantes'));
    }

    /**
     * Buscar participantes para el canje (AJAX).
     */
    public function buscarParticipante(Request $request, Evento $evento)
    {
        $q = trim($request->input('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $parsedId = null;
        $cleanSearch = $q;

        // Parseo automático del formato QR ID3272ÑAlejandro Casillas...
        if (str_contains($q, 'Ñ') || str_starts_with(strtoupper($q), 'ID')) {
            $partes = explode("Ñ", $q);
            if (isset($partes[0])) {
                $raw_id = trim(str_replace(['ID', 'id', 'Id'], '', $partes[0]));
                $num = preg_replace('/\D/', '', $raw_id);
                if (!empty($num)) {
                    $parsedId = (int)$num;
                }
            }
            if (isset($partes[1]) && !empty(trim($partes[1]))) {
                $cleanSearch = trim($partes[1]);
            }
        }

        $participantes = Participante::where('ID_Evento', $evento->ID)
            ->where(function ($query) use ($q, $parsedId, $cleanSearch) {
                if ($parsedId) {
                    $query->where('ID', $parsedId);
                }
                if (!empty($cleanSearch)) {
                    $query->orWhere('Nombre', 'LIKE', "%{$cleanSearch}%")
                          ->orWhere('Telefono', 'LIKE', "%{$cleanSearch}%")
                          ->orWhere('RFC', 'LIKE', "%{$cleanSearch}%");
                }
                if (is_numeric($q)) {
                    $query->orWhere('ID', (int)$q);
                } else {
                    $query->orWhere('Nombre', 'LIKE', "%{$q}%");
                }
            })
            ->select('ID', 'Nombre', 'Telefono', 'Puntos', 'Proveedor', 'Sucursal', 'RFC')
            ->limit(10)
            ->get();

        // El campo Puntos de participante ya contiene el saldo neto disponible en la base de datos
        $participantes->transform(function ($p) {
            $p->Puntos = max(0, (int)($p->Puntos ?? 0));
            return $p;
        });

        return response()->json($participantes);
    }

    /**
     * Obtener info de un participante específico (AJAX).
     */
    public function infoParticipante(Evento $evento, $participanteId)
    {
        $participante = Participante::where('ID', $participanteId)
            ->where('ID_Evento', $evento->ID)
            ->first();

        if (!$participante) {
            return response()->json(['ok' => false, 'msg' => 'Participante no encontrado en este evento.']);
        }

        // Historial de canjes de este participante en este evento
        $canjes = Canje::where('ID_Participante', $participante->ID)
            ->where('ID_Evento', $evento->ID)
            ->with('premio')
            ->orderByDesc('Fecha')
            ->get();

        $puntosGastados = 0;
        foreach ($canjes as $c) {
            if ($c->premio) {
                $puntosGastados += ($c->premio->PuntosNecesarios * $c->Cantidad);
            }
        }

        // $participante->Puntos es el saldo neto actual disponible
        $puntosDisponibles = max(0, (int)$participante->Puntos);

        // Calcular premios disponibles y cuántos puede canjear
        $premiosEvento = PremioEvento::where('ID_Evento', $evento->ID)->where('TipoPremio', 'puntos')->get();
        $premiosDisponibles = $premiosEvento->map(function ($premio) use ($puntosDisponibles) {
            $maxPorPuntos = $premio->PuntosNecesarios > 0
                ? floor($puntosDisponibles / $premio->PuntosNecesarios)
                : 0;
            $maxPorStock  = $premio->Disponible;
            $maxCanjeable = min($maxPorPuntos, $maxPorStock);

            return [
                'id'              => $premio->ID,
                'nombre'          => $premio->NombrePremio,
                'puntos'          => $premio->PuntosNecesarios,
                'stock'           => $premio->Disponible,
                'max_canjeable'   => max(0, $maxCanjeable),
                'puede_canjear'   => $maxCanjeable > 0,
            ];
        });

        return response()->json([
            'ok' => true,
            'participante' => [
                'ID'       => $participante->ID,
                'Nombre'   => $participante->Nombre,
                'Telefono' => $participante->Telefono,
                'Puntos'   => $participante->Puntos,
                'Proveedor'=> $participante->Proveedor,
                'Sucursal' => $participante->Sucursal,
            ],
            'puntosGastados'    => $puntosGastados,
            'puntosDisponibles' => $puntosDisponibles,
            'premios_disponibles' => $premiosDisponibles,
            'historial' => $canjes->map(function ($c) {
                return [
                    'premio'   => $c->premio->NombrePremio ?? 'Premio eliminado',
                    'cantidad' => $c->Cantidad,
                    'puntos'   => $c->premio->PuntosNecesarios ?? 0,
                    'fecha'    => $c->Fecha ? $c->Fecha->format('d/m/Y H:i') : '',
                    'tipo'     => $c->premio->TipoPremio ?? 'sorteo',
                ];
            }),
        ]);
    }

    /**
     * Realizar el canje de un premio (AJAX).
     */
    public function canjear(Request $request, Evento $evento)
    {
        $request->validate([
            'id_participante' => 'required|integer',
            'id_premio'       => 'required|integer',
            'cantidad'        => 'required|integer|min:1',
        ]);

        $participante = Participante::where('ID', $request->id_participante)
            ->where('ID_Evento', $evento->ID)
            ->first();

        if (!$participante) {
            return response()->json(['ok' => false, 'msg' => 'Participante no encontrado.']);
        }

        $premio = PremioEvento::where('ID', $request->id_premio)
            ->where('ID_Evento', $evento->ID)
            ->first();

        if (!$premio) {
            return response()->json(['ok' => false, 'msg' => 'Premio no encontrado.']);
        }

        // El campo Puntos de participante guarda el saldo neto disponible en tiempo real
        $puntosDisponibles = max(0, (int)$participante->Puntos);
        $costoTotal = $premio->PuntosNecesarios * $request->cantidad;

        if ($costoTotal > $puntosDisponibles) {
            return response()->json([
                'ok'  => false,
                'msg' => "Puntos insuficientes. Disponibles: {$puntosDisponibles}, Costo: {$costoTotal}.",
            ]);
        }

        // Verificar stock
        if ($premio->Disponible < $request->cantidad) {
            return response()->json([
                'ok'  => false,
                'msg' => "Stock insuficiente del premio. Disponible: {$premio->Disponible}.",
            ]);
        }

        // Registrar canje
        Canje::create([
            'ID_Evento'       => $evento->ID,
            'ID_Participante' => $participante->ID,
            'ID_Premio'       => $premio->ID,
            'Cantidad'        => $request->cantidad,
            'Fecha'           => now(),
        ]);

        // Reducir stock del premio
        $premio->Disponible -= $request->cantidad;
        $premio->save();

        // Descontar puntos al participante
        if ($participante->Puntos >= $costoTotal) {
            $participante->decrement('Puntos', $costoTotal);
        } else {
            $participante->Puntos = max(0, $participante->Puntos - $costoTotal);
            $participante->save();
        }

        if (!empty($participante->RFC) && Schema::hasTable('puntos_rfc')) {
            $puntosRfcActual = DB::table('puntos_rfc')->where('RFC', $participante->RFC)->value('Puntos') ?? 0;
            if ($puntosRfcActual > 0) {
                $nuevoRfc = max(0, $puntosRfcActual - $costoTotal);
                DB::table('puntos_rfc')->where('RFC', $participante->RFC)->update(['Puntos' => $nuevoRfc]);
            }
        }

        $nuevoDisponible = $puntosDisponibles - $costoTotal;

        return response()->json([
            'ok'  => true,
            'msg' => "✅ Canje exitoso: {$request->cantidad}x \"{$premio->NombrePremio}\" para {$participante->Nombre}. Puntos restantes: {$nuevoDisponible}.",
        ]);
    }
}
