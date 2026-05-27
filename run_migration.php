<?php
if (!\Illuminate\Support\Facades\Schema::hasTable('apariencias')) {
    \Illuminate\Support\Facades\Schema::create('apariencias', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->string('logo_path')->nullable();
        $table->string('color_primario')->default('#c9a227');
        $table->string('color_secundario')->default('#3b82f6');
        $table->string('fondo_login')->default('arbol');
        $table->string('fade_gradient_start')->default('rgba(234, 90, 12, 0.63)');
        $table->string('fade_gradient_end')->default('rgba(2, 6, 23, 1)');
        $table->timestamps();
    });
    \App\Models\Apariencia::create([]);
    echo "Tabla creada.\n";
} else {
    echo "Tabla ya existe.\n";
}
