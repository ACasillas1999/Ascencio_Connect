<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Usuario;
use App\Models\Evento;
use Illuminate\Support\Facades\Hash;

class EventoTest extends TestCase
{
    use DatabaseTransactions;

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear un usuario administrador antes de cada prueba
        $this->admin = Usuario::create([
            'username' => 'admin_eventos_' . uniqid(),
            'password' => Hash::make('1234'),
            'Rol' => 'Administrador'
        ]);
    }

    /**
     * Prueba que el administrador puede ver la lista de eventos.
     */
    public function test_admin_puede_ver_lista_de_eventos()
    {
        $response = $this->actingAs($this->admin)->get('/eventos');

        $response->assertStatus(200);
        // Debe haber un botón para crear nuevo evento
        $response->assertSee('Crear Evento'); 
    }

    /**
     * Prueba que el sistema valida los campos obligatorios al crear un evento.
     */
    public function test_creacion_evento_valida_campos_requeridos()
    {
        // Enviamos un POST sin datos
        $response = $this->actingAs($this->admin)->post('/eventos', []);

        // El sistema debe regresar errores para los campos requeridos
        $response->assertSessionHasErrors(['name_evento', 'fecha_inicio', 'fecha_fin', 'capacidad', 'ubicacion']);
    }

    /**
     * Prueba la creación exitosa de un evento en la base de datos.
     */
    public function test_admin_puede_crear_evento()
    {
        $datosEvento = [
            'name_evento' => 'Congreso PHP 2026',
            'fecha_inicio' => '2026-12-01',
            'fecha_fin' => '2026-12-05',
            'duracion' => '5 días',
            'capacidad' => 300,
            'ubicacion' => 'Auditorio Principal', // Simularemos que esta ubicación existe o pasará si no hay validación estricta de FK
            'estado' => 'PRÓXIMO',
            'tipo_puntos' => 'individual',
            'enviar_whatsapp_auto' => 1,
            'clases_obligatorias' => 0
        ];

        $response = $this->actingAs($this->admin)->post('/eventos', $datosEvento);

        // Debe redirigir a la lista de eventos tras guardarlo
        $response->assertRedirect('/eventos');

        // Verificamos que se guardó en la base de datos
        $this->assertDatabaseHas('evento', [
            'name_evento' => 'Congreso PHP 2026',
            'capacidad' => 300
        ]);
    }
}
