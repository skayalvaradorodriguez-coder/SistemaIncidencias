<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('asignaciones', 'rol')) {
            Schema::table('asignaciones', function (Blueprint $table) {
                $table->string('rol', 30)->default('Responsable')->after('usuario_id');
            });
        }

        if (!Schema::hasColumn('asignaciones', 'fecha_asignacion')) {
            Schema::table('asignaciones', function (Blueprint $table) {
                $table->timestamp('fecha_asignacion')->useCurrent()->after('rol');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('asignaciones', 'fecha_asignacion')) {
            Schema::table('asignaciones', function (Blueprint $table) {
                $table->dropColumn('fecha_asignacion');
            });
        }

        if (Schema::hasColumn('asignaciones', 'rol')) {
            Schema::table('asignaciones', function (Blueprint $table) {
                $table->dropColumn('rol');
            });
        }
    }
};