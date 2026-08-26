<?php

namespace App\Http\Controllers;

use App\Models\PremioEvento;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PremioController extends Controller
{
    /**
     * Almacena un nuevo premio asociado al evento.
     */
    public function store(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'NombrePremio'     => 'required|string|max:255',
            'TipoPremio'       => 'required|in:sorteo,puntos',
            'PuntosNecesarios' => 'nullable|integer|min:0',
            'Disponible'       => 'required|integer|min:0',
            'dia_sorteo'       => 'nullable|integer|min:1',
        ]);

        $data['ID_Evento'] = $evento->ID;

        try {
            PremioEvento::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 22003 || str_contains($e->getMessage(), '1264') || str_contains($e->getMessage(), 'Disponible')) {
                try {
                    DB::statement("ALTER TABLE `premios_evento` MODIFY COLUMN `Disponible` INT NOT NULL DEFAULT 0");
                    PremioEvento::create($data);
                } catch (\Exception $exRetry) {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return redirect()->route('eventos.show', [$evento, 'active_tab' => $request->input('active_tab', 'tab-premios')])->with('success', 'Premio agregado correctamente.');
    }

    /**
     * Actualiza un premio existente.
     */
    public function update(Request $request, PremioEvento $premio)
    {
        $data = $request->validate([
            'NombrePremio'     => 'required|string|max:255',
            'TipoPremio'       => 'required|in:sorteo,puntos',
            'PuntosNecesarios' => 'nullable|integer|min:0',
            'Disponible'       => 'required|integer|min:0',
            'dia_sorteo'       => 'nullable|integer|min:1',
        ]);

        try {
            $premio->update($data);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 22003 || str_contains($e->getMessage(), '1264') || str_contains($e->getMessage(), 'Disponible')) {
                try {
                    DB::statement("ALTER TABLE `premios_evento` MODIFY COLUMN `Disponible` INT NOT NULL DEFAULT 0");
                    $premio->update($data);
                } catch (\Exception $exRetry) {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }

        return redirect()->route('eventos.show', [$premio->ID_Evento, 'active_tab' => $request->input('active_tab', 'tab-premios')])->with('success', 'Premio actualizado.');
    }

    /**
     * Elimina un premio.
     */
    public function destroy(PremioEvento $premio)
    {
        $evento_id = $premio->ID_Evento;
        $premio->delete();

        return redirect()->route('eventos.show', [$evento_id, 'active_tab' => request('active_tab', 'tab-premios')])->with('success', 'Premio eliminado.');
    }

    /**
     * Actualiza el stock (Solo Admin).
     */
    public function updateStock(Request $request, PremioEvento $premio)
    {
        if (auth()->check() && auth()->user()->Rol !== 'Admin') {
            return response()->json(['ok' => false, 'msg' => 'No autorizado'], 403);
        }

        $data = $request->validate([
            'delta' => 'required|integer',
        ]);

        $nuevoStock = $premio->Disponible + $data['delta'];
        if ($nuevoStock < 0) $nuevoStock = 0;

        try {
            $premio->Disponible = $nuevoStock;
            $premio->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 22003 || str_contains($e->getMessage(), '1264') || str_contains($e->getMessage(), 'Disponible')) {
                DB::statement("ALTER TABLE `premios_evento` MODIFY COLUMN `Disponible` INT NOT NULL DEFAULT 0");
                $premio->Disponible = $nuevoStock;
                $premio->save();
            } else {
                throw $e;
            }
        }

        return response()->json(['ok' => true, 'nuevoStock' => $nuevoStock]);
    }
}
