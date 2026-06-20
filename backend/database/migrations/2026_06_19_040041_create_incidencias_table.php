<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->id();

            // Usuario que reporta la incidencia
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Ubicación
            $table->foreignId('ciudad_id')
                  ->constrained('ciudades')
                  ->onDelete('restrict');

            // Clasificación
            $table->foreignId('tipo_incidencia_id')
                  ->constrained('tipos_incidencia')
                  ->onDelete('restrict');

            $table->foreignId('subtipo_incidencia_id')
                  ->constrained('subtipos_incidencia')
                  ->onDelete('restrict');

            // Estado actual
            $table->foreignId('estado_incidencia_id')
                  ->constrained('estados_incidencia')
                  ->onDelete('restrict');

            // Información principal
            $table->string('titulo', 200);

            $table->text('descripcion');

            $table->enum('prioridad', [
                'Baja',
                'Media',
                'Alta',
                'Crítica'
            ])->default('Media');

            // Georreferenciación
            $table->decimal('latitud', 10, 8)->nullable();

            $table->decimal('longitud', 11, 8)->nullable();

            $table->string('direccion', 255)->nullable();

            // Evidencia fotográfica
            $table->string('foto')->nullable();

            // Fecha del reporte
            $table->timestamp('fecha_reporte')
                  ->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencias');
    }
};