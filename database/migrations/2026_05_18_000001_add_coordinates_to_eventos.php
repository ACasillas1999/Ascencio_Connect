<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->integer('gafete_qr_x')->default(1755);
            $table->integer('gafete_qr_y')->default(280);
            $table->integer('gafete_nombre_x')->default(202);
            $table->integer('gafete_nombre_y')->default(1050);
            $table->integer('gafete_font_size')->default(60);
        });
    }

    public function down(): void
    {
        Schema::table('evento', function (Blueprint $table) {
            $table->dropColumn(['gafete_qr_x', 'gafete_qr_y', 'gafete_nombre_x', 'gafete_nombre_y', 'gafete_font_size']);
        });
    }
};
