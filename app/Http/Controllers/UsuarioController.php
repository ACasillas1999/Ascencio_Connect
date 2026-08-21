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
            'ID_Evento'        => in_array(\App\Helpers\Permisos::normalizar($request->rol), ['Evento', 'Proveedor', 'Kiosko']) ? $request->ID_Evento : null,
            'tipo_kiosko'      => $request->input('tipo_kiosko', 'hibrido'),
        ]);

        if ($request->rol === 'Proveedor' && $request->filled('ID_Evento')) {
            $exists = \Illuminate\Support\Facades\DB::table('proveedor_evento')
                ->where('NombreProveedor', $request->username)
                ->where('ID_Evento', $request->ID_Evento)
                ->exists();
            
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('proveedor_evento')->insert([
                    'NombreProveedor' => $request->username,
                    'ID_Evento' => $request->ID_Evento,
                    'Puntos' => 0,
                    'Activo' => 1
                ]);
            }
        }

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
            'ID_Evento'=> in_array($request->rol, ['Evento', 'Proveedor']) ? $request->ID_Evento : null,
            'tipo_kiosko'=> $request->input('tipo_kiosko', 'hibrido'),
        ];

        if ($request->filled('password')) {
            $data['password']         = Hash::make($request->password);
            $data['password_visible'] = $request->password;
        }

        $usuario->update($data);

        if ($request->rol === 'Proveedor' && $request->filled('ID_Evento')) {
            $exists = \Illuminate\Support\Facades\DB::table('proveedor_evento')
                ->where('NombreProveedor', $request->username)
                ->where('ID_Evento', $request->ID_Evento)
                ->exists();
            
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('proveedor_evento')->insert([
                    'NombreProveedor' => $request->username,
                    'ID_Evento' => $request->ID_Evento,
                    'Puntos' => 0,
                    'Activo' => 1
                ]);
            }
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->username === 'Admin') {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar al administrador principal.');
        }

        // Si el usuario es proveedor, limpiar sus asignaciones de eventos asociadas
        if (strtolower($usuario->Rol) === 'proveedor') {
            \Illuminate\Support\Facades\DB::table('proveedor_evento')
                ->where('NombreProveedor', $usuario->username)
                ->delete();
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleActivo($id)
    {
        $usuario = Usuario::findOrFail($id);

        if ($usuario->username === 'Admin') {
            return back()->with('error', 'No se puede desactivar al usuario Administrador principal.');
        }

        $usuario->Activo = ($usuario->Activo == 1) ? 0 : 1;
        if ($usuario->Activo == 0) {
            $usuario->remember_token = null;
        }
        $usuario->save();

        if (strtolower($usuario->Rol) === 'proveedor') {
            \Illuminate\Support\Facades\DB::table('proveedor_evento')
                ->where('NombreProveedor', $usuario->username)
                ->update(['Activo' => $usuario->Activo]);
        }

        $estadoText = $usuario->Activo ? 'activado' : 'desactivado (sesiones cerradas)';
        return back()->with('success', "Usuario \"{$usuario->username}\" {$estadoText} exitosamente.");
    }
}
