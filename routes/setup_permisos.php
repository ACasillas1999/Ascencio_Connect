<?php
/**
 * Script para crear la tabla de permisos por rol.
 * Ejecutar UNA SOLA VEZ desde el navegador:
 * http://192.168.60.194/Ascencio_Connect/public/setup-permisos
 */

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/setup-permisos', function () {
    try {
        // Crear tabla de permisos por rol
        DB::statement("
            CREATE TABLE IF NOT EXISTS `roles_permisos` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `rol` VARCHAR(50) NOT NULL,
                `modulo` VARCHAR(100) NOT NULL,
                `activo` TINYINT(1) NOT NULL DEFAULT 1,
                UNIQUE KEY `unique_rol_modulo` (`rol`, `modulo`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Módulos del sistema
        $modulos = [
            'dashboard',
            'eventos',
            'participantes',
            'proveedores',
            'ubicaciones',
            'actividades',
            'agenda',
            'premios',
            'usuarios',
            'roles',
        ];

        // Roles del sistema
        $roles = ['Admin', 'Gerente', 'Vendedor', 'Proveedor', 'Evento'];

        // Permisos por defecto
        $defaults = [
            'Admin'     => $modulos, // Admin tiene acceso a todo
            'Gerente'   => ['dashboard', 'eventos', 'participantes', 'proveedores', 'actividades', 'agenda', 'premios'],
            'Vendedor'  => ['participantes'],
            'Proveedor' => ['proveedores'],
            'Evento'    => ['eventos', 'actividades', 'agenda'],
        ];

        $inserted = 0;
        foreach ($roles as $rol) {
            $permisosActivos = $defaults[$rol] ?? [];
            foreach ($modulos as $modulo) {
                $activo = in_array($modulo, $permisosActivos) ? 1 : 0;
                $exists = DB::table('roles_permisos')
                    ->where('rol', $rol)
                    ->where('modulo', $modulo)
                    ->first();

                if (!$exists) {
                    DB::table('roles_permisos')->insert([
                        'rol'    => $rol,
                        'modulo' => $modulo,
                        'activo' => $activo,
                    ]);
                    $inserted++;
                }
            }
        }

        return "<h2 style='font-family:sans-serif; color:green;'>✅ Tabla <code>roles_permisos</code> creada exitosamente.</h2>
                <p style='font-family:sans-serif;'>Se insertaron <strong>{$inserted}</strong> registros de permisos por defecto.</p>
                <p style='font-family:sans-serif;'><a href='/Ascencio_Connect/public/roles'>← Ir a Roles & Permisos</a></p>";
    } catch (\Exception $e) {
        return "<h2 style='font-family:sans-serif; color:red;'>❌ Error</h2><pre>{$e->getMessage()}</pre>";
    }
});
