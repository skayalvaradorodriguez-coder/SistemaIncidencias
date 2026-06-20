<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incidencia_id')
                  ->constrained('incidencias')
                  ->onDelete('cascade');

            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamp('fecha_asignacion')
                  ->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};