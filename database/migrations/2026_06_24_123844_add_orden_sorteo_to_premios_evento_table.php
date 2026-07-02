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
        Schema::table('premios_evento', function (Blueprint $table) {
            $table->integer('OrdenSorteo')->default(0)->after('TipoPremio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('premios_evento', function (Blueprint $table) {
            $table->dropColumn('OrdenSorteo');
        });
    }
};
