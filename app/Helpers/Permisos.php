<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Permisos
{
    /**
     * Módulos disponibles en el sistema con sus etiquetas e iconos.
     */
    public const MODULOS = [
        'dashboard'     => ['label' => 'Dashboard',       'icon' => 'bi-grid-1x2',         'desc' => 'Panel principal con estadísticas'],
        'eventos'       => ['label' => 'Eventos',         'icon' => 'bi-calendar-event',    'desc' => 'Crear y administrar eventos'],
        'participantes' => ['label' => 'Participantes',   'icon' => 'bi-people',            'desc' => 'Registro y gestión de participantes'],
        'proveedores'   => ['label' => 'Proveedores',     'icon' => 'bi-building',          'desc' => 'Gestión de proveedores y puntos'],
        'ubicaciones'   => ['label' => 'Ubicaciones',     'icon' => 'bi-geo-alt',           'desc' => 'Administrar ubicaciones del evento'],
        'actividades'   => ['label' => 'Actividades',     'icon' => 'bi-list-check',        'desc' => 'Actividades y asistencia'],
        'agenda'        => ['label' => 'Agenda',          'icon' => 'bi-clock',             'desc' => 'Horarios y programación'],
        'premios'       => ['label' => 'Premios',         'icon' => 'bi-trophy',            'desc' => 'Sorteos y premios del evento'],
        'usuarios'      => ['label' => 'Usuarios',        'icon' => 'bi-person-gear',       'desc' => 'Administrar cuentas de usuario'],
        'roles'         => ['label' => 'Roles & Permisos','icon' => 'bi-shield-lock',       'desc' => 'Configurar acceso por rol'],
        'kiosko'        => ['label' => 'Kiosko',          'icon' => 'bi-qr-code-scan',       'desc' => 'Consulta de puntos para participantes vía QR'],
    ];

    /**
     * Roles canónicos del sistema (los nombres "oficiales").
     */
    public const ROLES = ['Admin', 'Gerente', 'Vendedor', 'Proveedor', 'Evento', 'Kiosko'];

    /**
     * Mapa de sinónimos: variantes -> nombre canónico.
     * Esto resuelve que en la BD existan valores como 'proveedor', 'Administrador', etc.
     */
    private const SINONIMOS = [
        'admin'         => 'Admin',
        'administrador' => 'Admin',
        'gerente'       => 'Gerente',
        'vendedor'      => 'Vendedor',
        'proveedor'     => 'Proveedor',
        'evento'        => 'Evento',
        'kiosko'        => 'Kiosko',
        'kiosk'         => 'Kiosko',
    ];

    /**
     * Normalizar un rol a su nombre canónico.
     * Ej: 'proveedor' -> 'Proveedor', 'Administrador' -> 'Admin'
     */
    public static function normalizar(string $rol): string
    {
        return self::SINONIMOS[strtolower(trim($rol))] ?? $rol;
    }

    /**
     * Verificar si un rol tiene acceso a un módulo.
     */
    public static function tieneAcceso(string $rol, string $modulo): bool
    {
        $rol = self::normalizar($rol);

        // Admin siempre tiene acceso total
        if ($rol === 'Admin') {
            return true;
        }

        $cacheKey = "permisos_{$rol}";

        $permisos = Cache::remember($cacheKey, 300, function () use ($rol) {
            return DB::table('roles_permisos')
                ->where('rol', $rol)
                ->where('activo', 1)
                ->pluck('modulo')
                ->toArray();
        });

        return in_array($modulo, $permisos);
    }

    /**
     * Obtener todos los permisos de un rol.
     */
    public static function obtenerPermisos(string $rol): array
    {
        return DB::table('roles_permisos')
            ->where('rol', $rol)
            ->pluck('activo', 'modulo')
            ->toArray();
    }

    /**
     * Obtener la matriz completa: todos los roles con todos sus permisos.
     */
    public static function obtenerMatriz(): array
    {
        $matriz = [];
        foreach (self::ROLES as $rol) {
            $matriz[$rol] = self::obtenerPermisos($rol);
        }
        return $matriz;
    }

    /**
     * Guardar los permisos de un rol.
     */
    public static function guardarPermisos(string $rol, array $modulosActivos): void
    {
        foreach (array_keys(self::MODULOS) as $modulo) {
            $activo = in_array($modulo, $modulosActivos) ? 1 : 0;

            DB::table('roles_permisos')
                ->updateOrInsert(
                    ['rol' => $rol, 'modulo' => $modulo],
                    ['activo' => $activo]
                );
        }

        // Limpiar cache para este rol
        Cache::forget("permisos_{$rol}");
    }

    /**
     * Verificar si la tabla de permisos existe.
     */
    public static function tablaExiste(): bool
    {
        try {
            return \Schema::hasTable('roles_permisos');
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtener todos los valores REALES de la columna Rol en la BD
     * y agrupar por su nombre canónico.
     */
    public static function contarUsuariosPorRol(): array
    {
        $counts = [];
        foreach (self::ROLES as $rol) {
            $counts[$rol] = 0;
        }

        $usuarios = DB::table('usuarios')->select('Rol')->get();
        foreach ($usuarios as $u) {
            $canonico = self::normalizar($u->Rol);
            if (isset($counts[$canonico])) {
                $counts[$canonico] += 1;
            }
        }

        return $counts;
    }

    /**
     * Obtener usuarios filtrados por rol canónico (incluyendo sinónimos).
     */
    public static function usuariosPorRol(string $rolCanonico): \Illuminate\Support\Collection
    {
        // Encontrar todas las variantes de este rol
        $variantes = [];
        foreach (self::SINONIMOS as $variante => $canonico) {
            if ($canonico === $rolCanonico) {
                $variantes[] = $variante;
                $variantes[] = ucfirst($variante); // proveedor -> Proveedor
            }
        }
        $variantes[] = $rolCanonico; // el canónico mismo
        $variantes = array_unique($variantes);

        return \App\Models\Usuario::whereIn('Rol', $variantes)->get();
    }
}
