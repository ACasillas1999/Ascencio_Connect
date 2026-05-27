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
}
