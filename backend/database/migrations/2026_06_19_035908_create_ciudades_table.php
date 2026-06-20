<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ciudades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('provincia_id')
                  ->constrained('provincias')
                  ->onDelete('cascade');

            $table->string('nombre', 100);

            $table->timestamps();

            $table->unique(['provincia_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ciudades');
    }
};