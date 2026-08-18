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
        $tab = $request->input('active_tab', 'tab-proveedores');
        session(['active_tab' => $tab]);

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
            return redirect()->route('eventos.show', [$evento, 'active_tab' => $tab])
                ->with('active_tab', $tab)
                ->with('error', 'Este proveedor ya está asignado a este evento.');
        }

        DB::table('proveedor_evento')->insert($data);

        return redirect()->route('eventos.show', [$evento, 'active_tab' => $tab])
            ->with('active_tab', $tab)
            ->with('success', 'Proveedor asignado correctamente.');
    }

    /**
     * Elimina la asignación de un proveedor a un evento.
     */
    public function destroy(Request $request, $id)
    {
        $pe = DB::table('proveedor_evento')->where('ID', $id)->first();
        $evento_id = $pe ? $pe->ID_Evento : null;
        $tab = $request->input('active_tab', 'tab-proveedores');
        session(['active_tab' => $tab]);

        DB::table('proveedor_evento')
            ->where('ID', $id)
            ->delete();

        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'proveedores/gestion')) {
            return redirect()->back()->with('success', 'Proveedor eliminado del evento.');
        }

        if ($evento_id) {
            return redirect()->route('eventos.show', [$evento_id, 'active_tab' => $tab])
                ->with('active_tab', $tab)
                ->with('success', 'Proveedor eliminado del evento.');
        }

        return redirect()->back()->with('success', 'Proveedor eliminado del evento.');
    }

    /**
     * Actualiza los puntos o estatus (Activo) de un proveedor.
     */
    public function update(Request $request, $id)
    {
        $pe = DB::table('proveedor_evento')->where('ID', $id)->first();
        $evento_id = $pe ? $pe->ID_Evento : null;
        $tab = $request->input('active_tab', 'tab-proveedores');
        session(['active_tab' => $tab]);

        $updateData = [];
        if ($request->has('Puntos')) {
            $updateData['Puntos'] = $request->Puntos;
        }
        if ($request->has('Activo')) {
            $nuevoActivo = (int)$request->Activo;
            $updateData['Activo'] = $nuevoActivo;

            if ($pe && !empty($pe->NombreProveedor)) {
                $otrasActivas = DB::table('proveedor_evento')
                    ->where('NombreProveedor', $pe->NombreProveedor)
                    ->where('ID', '!=', $id)
                    ->where('Activo', 1)
                    ->exists();

                if (!$otrasActivas && $nuevoActivo === 0) {
                    \App\Models\Usuario::where('username', $pe->NombreProveedor)
                        ->update(['Activo' => 0, 'remember_token' => null]);
                } elseif ($nuevoActivo === 1) {
                    \App\Models\Usuario::where('username', $pe->NombreProveedor)
                        ->update(['Activo' => 1]);
                }
            }
        }

        if (!empty($updateData)) {
            DB::table('proveedor_evento')
                ->where('ID', $id)
                ->update($updateData);
        }

        $referer = $request->headers->get('referer');
        if ($referer && str_contains($referer, 'proveedores/gestion')) {
            return redirect()->back()->with('success', 'Proveedor actualizado correctamente.');
        }

        return redirect()->route('eventos.show', [$evento_id, 'active_tab' => $tab])
            ->with('active_tab', $tab)
            ->with('success', 'Proveedor actualizado correctamente.');
    }
}
