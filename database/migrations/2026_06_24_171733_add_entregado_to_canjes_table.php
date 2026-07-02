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
        Schema::table('canjes', function (Blueprint $table) {
            $table->boolean('Entregado')->default(false)->after('Fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('canjes', function (Blueprint $table) {
            $table->dropColumn('Entregado');
        });
    }
};
