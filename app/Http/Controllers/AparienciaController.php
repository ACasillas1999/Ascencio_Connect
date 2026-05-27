<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apariencia;
use Illuminate\Support\Facades\Storage;

class AparienciaController extends Controller
{
    public function index()
    {
        $config = Apariencia::getConfig();
        return view('apariencia.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'color_primario' => 'required|string',
            'color_secundario' => 'required|string',
            'tema_gold' => 'required|string',
            'tema_blue' => 'required|string',
            'bg_primary' => 'required|string',
            'bg_secondary' => 'required|string',
            'bg_sidebar' => 'required|string',
            'text_primary' => 'required|string',
            'fondo_login' => 'required|string',
            'fade_gradient_start' => 'required|string',
            'fade_gradient_end' => 'required|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['_token', 'logo']);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('imgs', 'public');
            $data['logo_path'] = 'storage/' . $path;
        }

        Apariencia::setConfig($data);

        return redirect()->back()->with('success', 'Configuración de apariencia actualizada correctamente.');
    }
}
