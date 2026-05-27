<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'username' => 'admin',
            'password' => Hash::make('1234'),
            'Rol' => 'Administrador',
            'password_visible' => '1234'
        ]);

        // También podemos crear un proveedor de prueba para que pruebes el escáner
        DB::table('usuarios')->insert([
            'username' => 'proveedor1',
            'password' => Hash::make('1234'),
            'Rol' => 'Proveedor',
            'password_visible' => '1234'
        ]);

        // Y un vendedor de prueba
        DB::table('usuarios')->insert([
            'username' => 'vendedor1',
            'password' => Hash::make('1234'),
            'Rol' => 'Vendedor',
            'password_visible' => '1234'
        ]);
    }
}
