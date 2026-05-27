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
        Schema::create('puntos_proveedor', function (Blueprint $table) {
            $table->id('ID');
            $table->integer('id_participante');
            $table->integer('id_evento');
            $table->string('usuario', 255); // Nombre del proveedor
            $table->integer('puntos');
            $table->dateTime('fecha');
            
            // Índices para mejorar rendimiento y búsquedas
            $table->index(['id_participante', 'usuario', 'id_evento']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntos_proveedor');
    }
};
