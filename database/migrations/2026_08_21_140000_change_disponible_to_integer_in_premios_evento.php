<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE `premios_evento` MODIFY COLUMN `Disponible` INT NOT NULL DEFAULT 0");
        } catch (\Exception $e) {
            \Log::info("Migración change_disponible_to_integer: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
