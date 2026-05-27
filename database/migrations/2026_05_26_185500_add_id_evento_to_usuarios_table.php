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
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('ID_Evento')->nullable();
            
            // Si la tabla evento usa 'ID' como primary key:
            $table->foreign('ID_Evento')->references('ID')->on('evento')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['ID_Evento']);
            $table->dropColumn('ID_Evento');
        });
    }
};
