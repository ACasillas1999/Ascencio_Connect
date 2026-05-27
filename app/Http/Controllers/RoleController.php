<?php

namespace App\Http\Controllers;

use App\Helpers\Permisos;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        if (!Permisos::tablaExiste()) {
            return redirect('/setup-permisos');
        }

        $counts = Permisos::contarUsuariosPorRol();

        $roles = collect(Permisos::ROLES)->map(function ($roleName) use ($counts) {
            $permisos = Permisos::obtenerPermisos($roleName);
            $activos = count(array_filter($permisos));
            return (object) [
                'name'     => $roleName,
                'count'    => $counts[$roleName] ?? 0,
                'activos'  => $activos,
                'total'    => count(Permisos::MODULOS),
            ];
        });

        $modulos = Permisos::MODULOS;
        $matriz  = Permisos::obtenerMatriz();

        return view('roles.index', compact('roles', 'modulos', 'matriz'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('roles.index')->with('info', 'Para agregar un nuevo rol, contacta al desarrollador.');
    }

    public function edit($role)
    {
        if (!in_array($role, Permisos::ROLES)) {
            abort(404, 'Rol no encontrado.');
        }

        $roleName  = $role;
        $modulos   = Permisos::MODULOS;
        $permisos  = Permisos::obtenerPermisos($roleName);
        $usuarios  = Permisos::usuariosPorRol($roleName);

        return view('roles.edit', compact('roleName', 'modulos', 'permisos', 'usuarios'));
    }

    public function update(Request $request, $role)
    {
        if (!in_array($role, Permisos::ROLES)) {
            abort(404, 'Rol no encontrado.');
        }

        // Admin siempre tiene todos los permisos
        if ($role === 'Admin') {
            return redirect()->route('roles.index')->with('error', 'El rol Admin siempre tiene acceso total y no puede ser modificado.');
        }

        $modulosActivos = $request->input('modulos', []);
        Permisos::guardarPermisos($role, $modulosActivos);

        return redirect()->route('roles.index')->with('success', "Permisos del rol \"{$role}\" actualizados exitosamente.");
    }

    public function destroy($role)
    {
        return redirect()->route('roles.index')->with('info', 'Los roles del sistema no pueden ser eliminados.');
    }
}
