<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function test_pantalla_carga_correctamente(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->waitForText('Ascencio Connect')
                ->assertPresent('form');
        });
    }

   
     public function test_crear_un_nuevo_evento(): void
    {
        $this->browse(function (Browser $browser) {
            
            // 1. Iniciamos sesión
            $browser->visit('/login')
                    ->type('username', 'hweg')
                    ->type('password', '1234')
                    ->press('#loginBtn')
                    ->pause(4000); // <--- ¡AQUÍ ESTÁ LA MAGIA! Espera 2 segundos a que cargue el dashboard
                    
            // 2. Ahora sí, navegamos a la página de crear evento
            $browser->visit('/eventos/create')
                    ->assertSee('Crear Evento'); 
                    
            // 3. Llenamos el formulario
            $browser->type('name_evento', 'Congreso de Prueba 2026') 
                    ->type('fecha_inicio', '2026-10-15')           
                    ->type('fecha_fin', '2026-10-18')
                    ->type('duracion', '4 días')
                    ->type('capacidad', '500')
                    ->select('ubicacion', 'hotel')
                    ->select('estado', 'PRÓXIMO')
                    ->select('tipo_puntos', 'individual')
                    ->check('clases_obligatorias') 
                    ->press('#btn-guardar')
                    ->pause(1000) // Pausa de 1 segundo para esperar que guarde en base de datos
                    ->assertPathIs('/eventos');
        });
    }

}
