<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    // Usamos DatabaseTransactions para que cualquier cambio en la BD (como crear el usuario de prueba) 
    // se deshaga automáticamente al terminar el test. ¡Así no dejamos basura!
    use DatabaseTransactions;

    /**
     * Prueba que el login exitoso redirige al dashboard.
     */
    public function test_login_con_credenciales_correctas()
    {
        // 1. Crear un usuario de prueba en la base de datos temporalmente
        $user = Usuario::create([
            'username' => 'test_admin',
            'password' => Hash::make('password123'),
            'Rol' => 'Administrador'
        ]);

        // 2. Intentar hacer login con esas credenciales
        $response = $this->post('/login', [
            'username' => 'test_admin',
            'password' => 'password123',
        ]);

        // 3. Verificar que nos redirige al dashboard
        $response->assertRedirect(route('dashboard'));
        
        // 4. Verificar que el usuario está autenticado en el sistema
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Prueba que el login falla con credenciales incorrectas.
     */
    public function test_login_con_credenciales_incorrectas()
    {
        // 1. Intentar hacer login con un usuario que no existe
        $response = $this->post('/login', [
            'username' => 'no_existo',
            'password' => '1234',
        ]);

        // 2. Verificar que nos regresa a la página de login
        $response->assertSessionHasErrors(['username']);
        $this->assertGuest(); // Verifica que seguimos sin sesión iniciada
    }

    /**
     * Prueba que las rutas protegidas no son accesibles sin login.
     */
    public function test_rutas_protegidas_requieren_login()
    {
        // Intentar entrar directo al dashboard sin hacer login
        $response = $this->get('/dashboard');

        // Nos debe redirigir a la página de login
        $response->assertRedirect('/login');
    }
}
