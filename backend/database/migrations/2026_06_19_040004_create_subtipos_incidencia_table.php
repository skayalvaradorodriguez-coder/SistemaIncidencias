<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subtipos_incidencia', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tipo_incidencia_id')
                  ->constrained('tipos_incidencia')
                  ->onDelete('cascade');

            $table->string('nombre', 100);

            $table->text('descripcion')->nullable();

            $table->timestamps();

            $table->unique(['tipo_incidencia_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subtipos_incidencia');
    }
};