<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class ParticipanteTest extends TestCase
{
    use DatabaseTransactions;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear un usuario administrador antes de cada prueba
        $this->admin = Usuario::create([
            'username' => 'admin_participantes_' . uniqid(),
            'password' => Hash::make('1234'),
            'Rol' => 'Administrador'
        ]);
    }

    /**
     * Prueba la visualización del listado de participantes.
     */
    public function test_admin_puede_ver_lista_participantes()
    {
        $response = $this->actingAs($this->admin)->get('/participantes');

        $response->assertStatus(200);
        $response->assertSee('Participantes');
    }

    /**
     * Prueba el registro de un nuevo participante.
     */
    public function test_admin_puede_registrar_participante()
    {
        // Los datos dependen de tu formulario real en el proyecto, esto es un modelo general
        $datosParticipante = [
            'Nombres' => 'Juan',
            'Apellidos' => 'Pérez',
            'Correo' => 'juan.perez@test.com',
            'Telefono' => '5551234567',
            'Empresa' => 'Grupo Ascencio'
        ];

        $response = $this->actingAs($this->admin)->post('/participantes', $datosParticipante);

        // Dependiendo de tu controlador, podría redirigir a la lista o mostrar la vista del gafete
        $response->assertStatus(302); // 302 indica una redirección exitosa

        // Verificamos que se haya guardado
        $this->assertDatabaseHas('participante', [
            'Correo' => 'juan.perez@test.com'
        ]);
    }
}
