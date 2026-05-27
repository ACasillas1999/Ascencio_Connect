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
        Schema::create('apariencias', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('color_primario')->default('#c9a227'); // Gold by default
            $table->string('color_secundario')->default('#3b82f6'); // Blue by default
            $table->string('fondo_login')->default('arbol'); // arbol, solo_logo, particulas
            $table->string('fade_gradient_start')->default('rgba(234, 90, 12, 0.63)');
            $table->string('fade_gradient_end')->default('rgba(2, 6, 23, 1)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apariencias');
    }
};
