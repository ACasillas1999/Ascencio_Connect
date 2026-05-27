<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Participante;
use App\Models\Canje;
use App\Models\PuntosProveedor;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'eventos_activos'   => Evento::where('estado', 'EN CURSO')->count(),
            'eventos_total'     => Evento::count(),
            'participantes'     => Participante::count(),
            'canjes'            => Canje::count(),
            'puntos_otorgados'  => PuntosProveedor::sum('puntos'),
        ];

        $ultimosEventos = Evento::orderByDesc('fecha_inicio')->limit(5)->get();

        $participantesPorEvento = Evento::withCount('participantes')
            ->orderByDesc('participantes_count')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'ultimosEventos', 'participantesPorEvento'));
    }
}
