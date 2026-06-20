<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incidencia_id')
                  ->constrained('incidencias')
                  ->onDelete('cascade');

            $table->foreignId('estado_anterior_id')
                  ->nullable()
                  ->constrained('estados_incidencia')
                  ->nullOnDelete();

            $table->foreignId('estado_nuevo_id')
                  ->constrained('estados_incidencia')
                  ->onDelete('restrict');

            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('observacion')->nullable();

            $table->timestamp('fecha_cambio')
                  ->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
    }
};