<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Permisos;

class RoleMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Si la cuenta está desactivada por el administrador, expulsar y cerrar sesión en tiempo real
        if (isset($user->Activo) && (int)$user->Activo === 0) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu sesión ha finalizado porque tu cuenta fue desactivada por el administrador.');
        }

        $userRol = $user->Rol;

        // Normalizar: tratar 'Admin' y 'Administrador' como equivalentes
        $normalizedUserRol = ($userRol === 'Admin' || $userRol === 'Administrador') ? 'Admin' : $userRol;
        $normalizedRoles = array_map(function ($r) {
            return ($r === 'Admin' || $r === 'Administrador') ? 'Admin' : $r;
        }, $roles);

        // Si el rol del usuario está en la lista de roles permitidos
        if (in_array($normalizedUserRol, $normalizedRoles)) {
            return $next($request);
        }

        // Si no está en la lista explícita, verificar permisos dinámicos por módulo
        if (Permisos::tablaExiste()) {
            $routeName = $request->route()->getName();
            $modulo = $this->detectarModulo($routeName);

            if ($modulo && Permisos::tieneAcceso($userRol, $modulo)) {
                return $next($request);
            }
        }

        // Redirección amigable según el rol si no tiene acceso
        if ($userRol === 'Proveedor' || $userRol === 'proveedor') {
            return redirect()->route('proveedor.index')->with('error', 'No tienes acceso a esa sección.');
        }

        if ($userRol === 'Vendedor') {
            return redirect()->route('participantes.index')->with('error', 'No tienes acceso a esa sección.');
        }

        abort(403, 'No tienes acceso a esa sección. Tu rol actual es: ' . $userRol);
    }

    /**
     * Detectar el módulo a partir del nombre de la ruta.
     */
    private function detectarModulo(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        $map = [
            'dashboard'     => 'dashboard',
            'eventos'       => 'eventos',
            'participantes' => 'participantes',
            'proveedores'   => 'proveedores',
            'proveedor'     => 'proveedores',
            'ubicaciones'   => 'ubicaciones',
            'actividades'   => 'actividades',
            'agenda'        => 'agenda',
            'premios'       => 'premios',
            'usuarios'      => 'usuarios',
            'roles'         => 'roles',
        ];

        foreach ($map as $prefix => $modulo) {
            if (str_starts_with($routeName, $prefix)) {
                return $modulo;
            }
        }

        return null;
    }
}
