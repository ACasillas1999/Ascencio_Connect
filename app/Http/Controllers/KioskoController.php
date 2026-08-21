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
        $evento = Evento::where('estado', 'EN CURSO')->first() 
            ?? Evento::orderByDesc('fecha_inicio')->first();

        $tipoKiosko = auth()->user()->tipo_kiosko ?? 'hibrido';
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
            $participante = DB::table('participante')->where('ID', $id)->first();
        }
        if (!$participante && $rfc) {
            $participante = DB::table('participante')
                ->where('RFC', $rfc)
                ->orWhere('Nombre', 'LIKE', "%{$rfc}%")
                ->first();
        }

        if (!$participante) {
            return response()->json([
                'ok' => false,
                'message' => "No se encontró ningún participante registrado con el código \"{$codigo}\"."
            ], 404);
        }

        // 2. Calcular Puntos Acumulados y Canjeados
        $puntos_indiv = (int)($participante->Puntos ?? 0);
        
        $puntos_rfc = 0;
        if (!empty($participante->RFC) && Schema::hasTable('puntos_rfc')) {
            $puntos_rfc = (int)(DB::table('puntos_rfc')->where('RFC', $participante->RFC)->value('Puntos') ?? 0);
        }

        $puntos_prov = 0;
        if (Schema::hasTable('puntos_proveedor')) {
            $puntos_prov = (int)(DB::table('puntos_proveedor')->where('id_participante', $participante->ID)->sum('puntos') ?? 0);
        }

        $puntos_din = 0;
        if (Schema::hasTable('registro_dinamica')) {
            $puntos_din = (int)(DB::table('registro_dinamica')->where('id_participante', $participante->ID)->sum('puntos') ?? 0);
        }

        // Historial de Asistencia a Salones / Agenda
        $historial_asistencia = collect();
        $puntos_asistencia = 0;
        if (Schema::hasTable('clase') && Schema::hasTable('agenda')) {
            $historial_asistencia = DB::table('clase')
                ->join('agenda', 'clase.ID_Agenda', '=', 'agenda.ID')
                ->where('clase.ID_Participante', $participante->ID)
                ->where('clase.Asistio', 1)
                ->select(
                    DB::raw("CONCAT(COALESCE(agenda.Salon, 'Salón'), ' — ', COALESCE(agenda.Actividad, 'Asistencia')) as origen"),
                    'agenda.Puntos_Asistencia as puntos',
                    'clase.Asistencia_Fecha as fecha',
                    DB::raw("'asistencia' as tipo")
                )
                ->get();
            
            $puntos_asistencia = (int)$historial_asistencia->sum('puntos');
        }

        // Puntos gastados en Canjes
        $puntos_canjeados = 0;
        if (Schema::hasTable('canjes')) {
            $puntos_canjeados = (int)DB::table('canjes')
                ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
                ->where('canjes.ID_Participante', $participante->ID)
                ->sum(DB::raw('canjes.Cantidad * premios_evento.PuntosNecesarios'));
        }

        // Puntos acumulados brutos
        $puntos_acumulados = max($puntos_indiv + $puntos_rfc, $puntos_prov + $puntos_din + $puntos_asistencia, $puntos_indiv, $puntos_rfc);

        // Puntos netos disponibles (restando los canjes realizados)
        $puntos_totales = max(0, $puntos_acumulados - $puntos_canjeados);

        // 3. Historial de Puntos Recibidos (Proveedores, Canjes, Asistencias)
        $historial_prov = collect();
        if (Schema::hasTable('puntos_proveedor')) {
            $historial_prov = DB::table('puntos_proveedor')
                ->where('id_participante', $participante->ID)
                ->select('usuario as origen', 'puntos', 'fecha', DB::raw("'proveedor' as tipo"))
                ->get();
        }

        $historial_canjes = collect();
        if (Schema::hasTable('canjes')) {
            $historial_canjes = DB::table('canjes')
                ->join('premios_evento', 'canjes.ID_Premio', '=', 'premios_evento.ID')
                ->where('canjes.ID_Participante', $participante->ID)
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