<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KioskoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $evento = null;
        if ($user && $user->ID_Evento) {
            $evento = Evento::find($user->ID_Evento);
        }
        if (!$evento) {
            $evento = Evento::where('estado', 'EN CURSO')->first() 
                ?? Evento::orderByDesc('fecha_inicio')->first();
        }

        $tipoKiosko = $user->tipo_kiosko ?? 'hibrido';
        return view('kiosko.index', compact('evento', 'tipoKiosko'));
    }

    public function buscar(Request $request)
    {
        $codigo = trim($request->input('codigo', ''));

        if (empty($codigo)) {
            return response()->json([
                'ok' => false,
                'message' => 'Por favor escanea un código QR o ingresa un ID / RFC.'
            ], 400);
        }

        $user = auth()->user();
        $eventoId = ($user && $user->ID_Evento) ? $user->ID_Evento : null;

        if (!$eventoId) {
            $eventoDef = Evento::where('estado', 'EN CURSO')->first() 
                ?? Evento::orderByDesc('fecha_inicio')->first();
            $eventoId = $eventoDef ? $eventoDef->ID : null;
        }

        // 1. Extraer ID o RFC
        $id = null;
        $rfc = null;
        if (preg_match('/^ID(\d+)/i', $codigo, $matches)) {
            $id = (int)$matches[1];
        } elseif (is_numeric($codigo)) {
            $id = (int)$codigo;
        } else {
            $rfc = $codigo;
        }

        $participante = null;
        if ($id) {
            $query = DB::table('participante')->where('ID', $id);
            if ($eventoId) {
                $query->where('ID_Evento', $eventoId);
            }
            $participante = $query->first();
        }
        if (!$participante && $rfc) {
            $query = DB::table('participante');
            if ($eventoId) {
                $query->where('ID_Evento', $eventoId);
            }
            $participante = $query->where(function ($q) use ($rfc) {
                $q->where('RFC', $rfc)->orWhere('Nombre', 'LIKE', "%{$rfc}%");
            })->first();
        }

        if (!$participante) {
            return response()->json([
                'ok' => false,
                'message' => "No se encontró ningún participante registrado en este evento con el código \"{$codigo}\"."
            ], 404);
        }

        // 2. Calcular Puntos Acumulados y Canjeados
        $puntos_indiv = (int)($participante->Puntos ?? 0);
        
        $puntos_rfc = 0;
        if (!empty($participante->RFC) && Schema::hasTable('puntos_rfc')) {
            $queryRfc = DB::table('puntos_rfc')->where('RFC', $participante->RFC);
            if ($eventoId) {
                $queryRfc->where('ID_Evento', $eventoId);
            }
            $puntos_rfc = (int)($queryRfc->value('Puntos') ?? 0);
        }

        $puntos_prov = 0;
        if (Schema::hasTable('puntos_proveedor')) {
            $queryProv = DB::table('puntos_proveedor')->where('id_participante', $participante->ID);
            if ($eventoId) {
                $queryProv->where('id_evento', $eventoId);
            }
            $puntos_prov = (int)($queryProv->sum('puntos') ?? 0);
        }

        $puntos_din = 0;
        if (Schema::hasTable('registro_dinamica')) {
            $queryDin = DB::table('registro_dinamica')->where('id_participante', $participante->ID);
            if ($eventoId) {
                $queryDin->where('id_evento', $eventoId);
            }
            $puntos_din = (int)($queryDin->sum('puntos') ?? 0);
        }

        // Historial de Asistencia a Salones / Agenda (Unión con actividades y agenda para puntos reales)
        $historial_asistencia = collect();
        $puntos_asistencia = 0;
        if (Schema::hasTable('clase') && Schema::hasTable('agenda')) {
            $queryAsist = DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->leftJoin('actividades', function ($join) {
                    $join->on('agenda.ID_Evento', '=', 'actividades.ID_Evento')
                         ->on('agenda.Actividad', '=', 'actividades.Actividad');
                })
                ->where('clase.ID_Participante', $participante->ID)
                ->where('clase.Asistio', 1);

            if ($eventoId) {
                $queryAsist->where('agenda.ID_Evento', $eventoId);
            }

            $historial_asistencia = $queryAsist
                ->select(
                    DB::raw("CONCAT(COALESCE(agenda.Salon, 'Salón'), ' — ', COALESCE(agenda.Actividad, 'Asistencia')) as origen"),
                    DB::raw("COALESCE(actividades.Puntos_Default, agenda.Puntos_Asistencia, 0) as puntos"),
                    'clase.Asistencia_Fecha as fecha',
                    DB::raw("'asistencia' as tipo")
                )
                ->get();
            
            $puntos_asistencia = (int)$historial_asistencia->sum('puntos');
        }

        // Puntos gastados en Canjes del evento
        $puntos_canjeados = 0;
        if (Schema::hasTable('canjes')) {
            $queryCanj = DB::table('canjes')
                ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
                ->where('canjes.ID_Participante', $participante->ID);

            if ($eventoId) {
                $queryCanj->where('canjes.ID_Evento', $eventoId);
            }

            $puntos_canjeados = (int)$queryCanj->sum(DB::raw('canjes.Cantidad * premios_evento.PuntosNecesarios'));
        }

        // Puntos netos disponibles
        $puntos_totales = max($puntos_indiv + $puntos_rfc, 0);

        // 3. Historial de Puntos Recibidos (Proveedores, Canjes, Asistencias)
        $historial_prov = collect();
        if (Schema::hasTable('puntos_proveedor')) {
            $queryProvHist = DB::table('puntos_proveedor')
                ->where('id_participante', $participante->ID);
            if ($eventoId) {
                $queryProvHist->where('id_evento', $eventoId);
            }
            $historial_prov = $queryProvHist
                ->select('usuario as origen', 'puntos', 'fecha', DB::raw("'proveedor' as tipo"))
                ->get();
        }

        $historial_canjes = collect();
        if (Schema::hasTable('canjes')) {
            $queryCanjHist = DB::table('canjes')
                ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
                ->where('canjes.ID_Participante', $participante->ID);
            if ($eventoId) {
                $queryCanjHist->where('canjes.ID_Evento', $eventoId);
            }
            $historial_canjes = $queryCanjHist
                ->select(
                    DB::raw("CONCAT(premios_evento.NombrePremio, ' (x', canjes.Cantidad, ')') as origen"),
                    DB::raw('-(canjes.Cantidad * premios_evento.PuntosNecesarios) as puntos'),
                    'canjes.Fecha as fecha',
                    DB::raw("'canje' as tipo")
                )
                ->get();
        }

        $historial = $historial_prov
            ->concat($historial_canjes)
            ->concat($historial_asistencia)
            ->sortByDesc('fecha')
            ->take(20)
            ->values();

        return response()->json([
            'ok' => true,
            'participante' => [
                'id' => $participante->ID,
                'nombre' => $participante->Nombre ?? 'Sin Nombre',
                'rfc' => !empty($participante->RFC) ? $participante->RFC : 'Sin RFC',
                'categoria' => $participante->Categoria ?? 'Participante',
                'puntos_totales' => $puntos_totales,
                'puntos_individuales' => $puntos_indiv,
                'puntos_grupales' => $puntos_rfc,
                'total_visitas' => count($historial_prov),
                'historial' => $historial
            ]
        ]);
    }
}
