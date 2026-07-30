<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Una foto en base64 (hasta 4MB según la validación actual) ocupa varios
     * miles de caracteres — no cabe en el VARCHAR(255) que tienen ahora las
     * columnas 'foto' y 'ruta_foto'. Las ampliamos a TEXT (sin límite práctico).
     *
     * Se usa SQL directo (DB::statement) en vez de $table->text()->change()
     * porque este proyecto no tiene el paquete doctrine/dbal instalado
     * (necesario para modificar columnas existentes con la sintaxis normal
     * de Laravel), y no queremos agregar dependencias nuevas.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE incidencias ALTER COLUMN foto TYPE TEXT');
        DB::statement('ALTER TABLE evidencias ALTER COLUMN ruta_foto TYPE TEXT');
    }

    public function down(): void
    {
        // Nota: si hay fotos en base64 (más de 255 caracteres) al revertir,
        // Postgres las recortará con substring para poder volver a VARCHAR(255).
        DB::statement("ALTER TABLE incidencias ALTER COLUMN foto TYPE VARCHAR(255) USING substring(foto, 1, 255)");
        DB::statement("ALTER TABLE evidencias ALTER COLUMN ruta_foto TYPE VARCHAR(255) USING substring(ruta_foto, 1, 255)");
    }
};
