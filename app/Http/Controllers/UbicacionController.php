<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    public function index()
    {
        $ubicaciones = Ubicacion::orderBy('Nombre')->get();
        return view('ubicaciones.index', compact('ubicaciones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Nombre'              => 'required|string|max:255',
            'Direccion'           => 'required|string|max:255',
            'Salones'             => 'required|integer|min:1',
            'Capacidad_por_salon' => 'required|integer|min:1',
            'capacidad_total'     => 'required|integer|min:1',
        ]);

        Ubicacion::create($data);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación creada correctamente.');
    }

    public function update(Request $request, Ubicacion $ubicacion)
    {
        $data = $request->validate([
            'Nombre'              => 'required|string|max:255',
            'Direccion'           => 'required|string|max:255',
            'Salones'             => 'required|integer|min:1',
            'Capacidad_por_salon' => 'required|integer|min:1',
            'capacidad_total'     => 'required|integer|min:1',
        ]);

        $ubicacion->update($data);

        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación actualizada.');
    }

    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();
        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación eliminada.');
    }
}
