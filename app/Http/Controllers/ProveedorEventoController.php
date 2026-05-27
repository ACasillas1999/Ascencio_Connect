<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorEventoController extends Controller
{
    /**
     * Asigna un proveedor a un evento con sus puntos.
     */
    public function store(Request $request, Evento $evento)
    {
        $data = $request->validate([
            'NombreProveedor' => 'required|string|max:100',
            'Puntos'          => 'required|integer|min:0',
        ]);

        $data['ID_Evento'] = $evento->ID;
        $data['Activo'] = 1;

        // Verificar si ya existe el proveedor en este evento
        $existe = DB::table('proveedor_evento')
            ->where('ID_Evento', $evento->ID)
            ->where('NombreProveedor', $data['NombreProveedor'])
            ->exists();

        if ($existe) {
            return redirect()->route('eventos.show', $evento)->with('error', 'Este proveedor ya está asignado a este evento.');
        }

        DB::table('proveedor_evento')->insert($data);

        return redirect()->route('eventos.show', $evento)->with('success', 'Proveedor asignado correctamente.');
    }

    /**
     * Elimina la asignación de un proveedor a un evento.
     */
    public function destroy(Evento $evento, $id)
    {
        DB::table('proveedor_evento')
            ->where('ID', $id)
            ->where('ID_Evento', $evento->ID)
            ->delete();

        return redirect()->route('eventos.show', $evento)->with('success', 'Proveedor eliminado del evento.');
    }

    /**
     * Actualiza los puntos de un proveedor.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'Puntos' => 'required|integer|min:0',
        ]);

        DB::table('proveedor_evento')
            ->where('ID', $id)
            ->update(['Puntos' => $request->Puntos]);

        return redirect()->back()->with('success', 'Puntos del proveedor actualizados.');
    }
}
