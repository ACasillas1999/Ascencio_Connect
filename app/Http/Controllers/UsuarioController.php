<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Helpers\Permisos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('evento')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Permisos::ROLES;
        $eventos = \App\Models\Evento::orderByDesc('fecha_inicio')->get();
        return view('usuarios.create', compact('roles', 'eventos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:usuarios,username',
            'password' => 'required|min:4',
            'rol'      => 'required|in:' . implode(',', Permisos::ROLES),
            'ID_Evento'=> 'nullable|exists:evento,ID',
        ]);

        Usuario::create([
            'username'         => $request->username,
            'password'         => Hash::make($request->password),
            'password_visible' => $request->password,
            'Rol'              => $request->rol,
            'ID_Evento'        => $request->rol === 'Evento' ? $request->ID_Evento : null,
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles = Permisos::ROLES;
        $eventos = \App\Models\Evento::orderByDesc('fecha_inicio')->get();
        return view('usuarios.edit', compact('usuario', 'roles', 'eventos'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'username' => 'required|unique:usuarios,username,' . $usuario->ID . ',ID',
            'rol'      => 'required|in:' . implode(',', Permisos::ROLES),
            'ID_Evento'=> 'nullable|exists:evento,ID',
        ]);

        $data = [
            'username' => $request->username,
            'Rol'      => $request->rol,
            'ID_Evento'=> $request->rol === 'Evento' ? $request->ID_Evento : null,
        ];

        if ($request->filled('password')) {
            $data['password']         = Hash::make($request->password);
            $data['password_visible'] = $request->password;
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->username === 'Admin') {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar al administrador principal.');
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
