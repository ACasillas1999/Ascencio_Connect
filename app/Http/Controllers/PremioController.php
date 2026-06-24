<?php

namespace App\Http\Controllers;

use App\Models\PremioEvento;
use App\Models\Evento;
use Illuminate\Http\Request;

class PremioController extends Controller
{
    /**
     * Almacena un nuevo premio asociado al evento.
     */
    public function store(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'NombrePremio'     => 'required|string|max:255',
            'PuntosNecesarios' => 'required|integer|min:1',
            'Disponible'       => 'required|integer|min:0',
            'TipoPremio'       => 'required|in:sorteo,puntos',
        ]);

        $data['ID_Evento'] = $evento->ID;

        PremioEvento::create($data);

        return redirect()->route('eventos.show', $evento)->with('success', 'Premio agregado correctamente.');
    }

    /**
     * Actualiza un premio existente.
     */
    public function update(Request $request, PremioEvento $premio)
    {
        $data = $request->validate([
            'NombrePremio'     => 'required|string|max:255',
            'PuntosNecesarios' => 'required|integer|min:1',
            'Disponible'       => 'required|integer|min:0',
            'TipoPremio'       => 'required|in:sorteo,puntos',
        ]);

        $premio->update($data);

        return redirect()->route('eventos.show', $premio->ID_Evento)->with('success', 'Premio actualizado.');
    }

    /**
     * Elimina un premio.
     */
    public function destroy(PremioEvento $premio)
    {
        $evento_id = $premio->ID_Evento;
        $premio->delete();

        return redirect()->route('eventos.show', $evento_id)->with('success', 'Premio eliminado.');
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

        $premio->Disponible = $nuevoStock;
        $premio->save();

        return response()->json(['ok' => true, 'nuevoStock' => $nuevoStock]);
    }
}
