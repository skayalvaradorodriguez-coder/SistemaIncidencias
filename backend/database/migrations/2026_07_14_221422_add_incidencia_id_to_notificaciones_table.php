<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Solo agrega la columna si no existe (bases creadas con la migración antigua)
        if (!Schema::hasColumn('notificaciones', 'incidencia_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->foreignId('incidencia_id')
                      ->nullable()
                      ->after('usuario_id')
                      ->constrained('incidencias')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('notificaciones', 'incidencia_id')) {
            Schema::table('notificaciones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('incidencia_id');
            });
        }
    }
};