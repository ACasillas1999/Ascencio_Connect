<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Usuario;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear permisos (pueden expandirse en el futuro)
        $permisos = [
            'ver dashboard',
            'gestionar usuarios',
            'gestionar roles',
            'gestionar eventos',
            'gestionar participantes',
            'gestionar proveedores',
            'asignar puntos'
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }

        // 2. Crear los roles estandarizados
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $roleAdmin->syncPermissions(Permission::all());

        $roleGerente = Role::firstOrCreate(['name' => 'Gerente']);
        $roleGerente->syncPermissions(['ver dashboard', 'gestionar participantes']);

        $roleVendedor = Role::firstOrCreate(['name' => 'Vendedor']);
        $roleVendedor->syncPermissions(['ver dashboard', 'gestionar participantes']); // Permisos básicos de vendedor

        $roleProveedor = Role::firstOrCreate(['name' => 'Proveedor']);
        $roleProveedor->syncPermissions(['asignar puntos']);

        $roleEvento = Role::firstOrCreate(['name' => 'Evento']);
        // Permisos para el escáner de evento u otras herramientas in situ
        $roleEvento->syncPermissions(['ver dashboard', 'gestionar participantes']);

        // 3. Migrar los usuarios actuales basándose en la columna `Rol` (texto)
        // Valores que he visto en tu DB: 'Admin', 'Evento', 'Vendedor', 'Gerente', 'proveedor'
        $usuarios = Usuario::all();

        foreach ($usuarios as $user) {
            $rolOriginal = strtolower(trim($user->Rol));
            
            if ($rolOriginal === 'admin' || $rolOriginal === 'administrador') {
                $user->assignRole($roleAdmin);
            } elseif ($rolOriginal === 'gerente') {
                $user->assignRole($roleGerente);
            } elseif ($rolOriginal === 'vendedor') {
                $user->assignRole($roleVendedor);
            } elseif ($rolOriginal === 'proveedor') {
                $user->assignRole($roleProveedor);
            } elseif ($rolOriginal === 'evento') {
                $user->assignRole($roleEvento);
            }
        }
    }
}
