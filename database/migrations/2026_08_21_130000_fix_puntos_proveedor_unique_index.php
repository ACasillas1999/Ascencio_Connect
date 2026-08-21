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
            $indexes = DB::select("SHOW INDEX FROM `puntos_proveedor` WHERE Non_unique = 0 AND Key_name != 'PRIMARY'");
            foreach ($indexes as $idx) {
                DB::statement("ALTER TABLE `puntos_proveedor` DROP INDEX `" . $idx->Key_name . "`");
            }
        } catch (\Exception $e) {
            \Log::info("Migración fix_puntos_proveedor: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
