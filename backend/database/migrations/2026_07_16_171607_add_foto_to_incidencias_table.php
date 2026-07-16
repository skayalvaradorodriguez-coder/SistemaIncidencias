<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('incidencias', 'foto')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->string('foto')->nullable()->after('direccion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('incidencias', 'foto')) {
            Schema::table('incidencias', function (Blueprint $table) {
                $table->dropColumn('foto');
            });
        }
    }
};