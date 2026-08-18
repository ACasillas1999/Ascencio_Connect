<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Ubicacion;
use Illuminate\Http\Request;

class SalonController extends Controller
{
    public function index(Ubicacion $ubicacion)
    {
        $salones = Salon::where('ubicacion_id', $ubicacion->ID)->orderBy('Nombre')->get();
        return response()->json($salones);
    }

    public function store(Request $request, Ubicacion $ubicacion)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
        ]);

        $salon = Salon::create([
            'ubicacion_id' => $ubicacion->ID,
            'Nombre' => $request->Nombre,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Salón agregado correctamente.',
            'salon' => $salon
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nombre' => 'required|string|max:255',
        ]);

        $salon = Salon::findOrFail($id);
        $oldName = $salon->Nombre;
        $newName = $request->Nombre;

        $salon->update([
            'Nombre' => $newName
        ]);

        // Propagate name change to existing agenda records!
        \DB::table('agenda')
            ->where('Salon', $oldName)
            ->update(['Salon' => $newName]);

        return response()->json([
            'success' => true,
            'message' => 'Salón actualizado y registros de agenda sincronizados.'
        ]);
    }

    public function destroy($id)
    {
        $salon = Salon::findOrFail($id);
        $salon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Salón de la ubicación eliminado.'
        ]);
    }
}
