<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tareas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('incidencia_id')
                  ->constrained('incidencias')
                  ->onDelete('cascade');

            // Responsable al que se le asigna esta tarea puntual
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Administrador que creó la tarea
            $table->foreignId('creado_por')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();

            $table->enum('estado', ['Pendiente', 'En Proceso', 'Completada'])
                  ->default('Pendiente');

            // Nota que el Responsable va dejando sobre su avance
            $table->text('nota_avance')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
