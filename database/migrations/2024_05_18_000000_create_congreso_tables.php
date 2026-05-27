<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('actividades')) {
            Schema::create('actividades', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento');
                $table->string('Actividad', 255);
                $table->string('Descripcion', 255);
                $table->integer('capacidad');
                $table->boolean('Exclusiva')->default(0);
                $table->integer('Puntos_Default')->default(0);
            });
        }

        if (!Schema::hasTable('agenda')) {
            Schema::create('agenda', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento');
                $table->string('Salon', 100)->nullable();
                $table->date('Fecha');
                $table->string('Horario', 255);
                $table->string('Actividad', 255)->nullable();
                $table->integer('Puntos_Asistencia')->default(0);
            });
        }

        if (!Schema::hasTable('canjes')) {
            Schema::create('canjes', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento')->nullable();
                $table->integer('ID_Participante')->nullable();
                $table->integer('ID_Premio')->nullable();
                $table->integer('Cantidad')->nullable();
                $table->timestamp('Fecha')->useCurrent();
            });
        }

        if (!Schema::hasTable('clase')) {
            Schema::create('clase', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Agenda');
                $table->integer('ID_Participante');
                $table->boolean('Asistio')->default(0);
                $table->dateTime('Asistencia_Fecha')->nullable();
                $table->integer('Asistencia_Usuario')->nullable();
                $table->boolean('Tipo_Inscripcion')->default(0);
            });
        }

        if (!Schema::hasTable('configuracion_css')) {
            Schema::create('configuracion_css', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('nombre_variable', 50)->unique();
                $table->string('valor_css', 255);
            });
        }

        if (!Schema::hasTable('evento')) {
            Schema::create('evento', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->string('name_evento', 255);
                $table->string('duracion', 255);
                $table->string('estado', 50);
                $table->date('fecha_inicio');
                $table->date('fecha_fin');
                $table->string('ubicacion', 255);
                $table->integer('capacidad');
                $table->enum('tipo_puntos', ['ninguno', 'individual', 'grupal'])->nullable()->default('ninguno');
                // Nuevas columnas de configuración
                $table->string('machote_gafete', 255)->nullable();
                $table->string('machote_horario', 255)->nullable();
                $table->boolean('enviar_whatsapp_auto')->default(0);
                $table->string('wa_template_name', 255)->nullable();
                $table->boolean('clases_obligatorias')->default(0);
            });
        }

        if (!Schema::hasTable('participante')) {
            Schema::create('participante', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento')->nullable();
                $table->string('Sucursal', 100)->nullable();
                $table->string('Vendedor', 100)->nullable();
                $table->string('Nombre', 255)->nullable();
                $table->string('RFC', 20);
                $table->string('Proveedor', 255)->nullable();
                $table->string('QR_Code', 255)->nullable();
                $table->string('Telefono', 15);
                $table->string('Ruta_Gafete', 255)->nullable();
                $table->string('Ruta_Horario', 500)->nullable();
                $table->integer('Puntos')->nullable()->default(0);
                $table->string('Puesto', 100)->nullable();
            });
        }

        if (!Schema::hasTable('premios_evento')) {
            Schema::create('premios_evento', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento')->nullable();
                $table->string('NombrePremio', 100)->nullable();
                $table->integer('PuntosNecesarios')->nullable();
                $table->boolean('Disponible')->nullable()->default(1);
            });
        }

        if (!Schema::hasTable('proveedor_evento')) {
            Schema::create('proveedor_evento', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->integer('ID_Evento');
                $table->string('NombreProveedor', 100);
                $table->integer('Puntos');
                $table->boolean('Activo')->nullable()->default(1);
            });
        }

        if (!Schema::hasTable('puntos_rfc')) {
            Schema::create('puntos_rfc', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->string('RFC', 20)->nullable()->unique();
                $table->integer('ID_Evento')->nullable();
                $table->integer('Puntos')->nullable()->default(0);
            });
        }

        if (!Schema::hasTable('ubicaciones')) {
            Schema::create('ubicaciones', function (Blueprint $table) {
                $table->integer('ID', true);
                $table->string('Nombre', 255);
                $table->string('Direccion', 255);
                $table->integer('Salones');
                $table->integer('Capacidad_por_salon');
                $table->integer('capacidad_total');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
        Schema::dropIfExists('agenda');
        Schema::dropIfExists('canjes');
        Schema::dropIfExists('clase');
        Schema::dropIfExists('configuracion_css');
        Schema::dropIfExists('evento');
        Schema::dropIfExists('participante');
        Schema::dropIfExists('premios_evento');
        Schema::dropIfExists('proveedor_evento');
        Schema::dropIfExists('puntos_rfc');
        Schema::dropIfExists('ubicaciones');
    }
};
