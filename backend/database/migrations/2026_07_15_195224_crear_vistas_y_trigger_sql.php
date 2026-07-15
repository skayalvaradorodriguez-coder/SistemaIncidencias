<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Solo aplica en PostgreSQL (SQLite de pruebas no soporta esta sintaxis)
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Columna para registrar cuándo se resolvió la incidencia
        DB::statement("
            ALTER TABLE incidencias
            ADD COLUMN IF NOT EXISTS fecha_resolucion TIMESTAMP NULL
        ");

        // FUNCIÓN + TRIGGER: registra fecha_resolucion automáticamente
        // cuando la incidencia pasa al estado Resuelto
        DB::statement("
            CREATE OR REPLACE FUNCTION registrar_fecha_resolucion()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.estado_incidencia_id = (
                    SELECT id FROM estados_incidencia WHERE nombre = 'Resuelto' LIMIT 1
                ) AND (OLD.estado_incidencia_id IS DISTINCT FROM NEW.estado_incidencia_id) THEN
                    NEW.fecha_resolucion := NOW();
                END IF;
                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql
        ");

        DB::statement("DROP TRIGGER IF EXISTS trg_fecha_resolucion ON incidencias");

        DB::statement("
            CREATE TRIGGER trg_fecha_resolucion
            BEFORE UPDATE ON incidencias
            FOR EACH ROW
            EXECUTE FUNCTION registrar_fecha_resolucion()
        ");

        // VISTA 1: resumen de incidencias por estado
        DB::statement("
            CREATE OR REPLACE VIEW vista_incidencias_por_estado AS
            SELECT
                e.nombre AS estado,
                COUNT(i.id) AS total,
                ROUND(COUNT(i.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM incidencias), 0), 1) AS porcentaje
            FROM estados_incidencia e
            LEFT JOIN incidencias i ON i.estado_incidencia_id = e.id
            GROUP BY e.nombre
            ORDER BY total DESC
        ");

        // VISTA 2: resumen por tipo y ubicación
        DB::statement("
            CREATE OR REPLACE VIEW vista_incidencias_por_tipo_ciudad AS
            SELECT
                t.nombre AS tipo,
                c.nombre AS ciudad,
                COUNT(i.id) AS total
            FROM incidencias i
            JOIN tipos_incidencia t ON t.id = i.tipo_incidencia_id
            JOIN ciudades c ON c.id = i.ciudad_id
            GROUP BY t.nombre, c.nombre
            ORDER BY total DESC
        ");

        // VISTA 3: tiempo promedio de resolución (usa el trigger)
        DB::statement("
            CREATE OR REPLACE VIEW vista_tiempo_resolucion AS
            SELECT
                COUNT(*) AS incidencias_resueltas,
                ROUND(AVG(EXTRACT(EPOCH FROM (fecha_resolucion - created_at)) / 3600)::numeric, 2) AS horas_promedio,
                ROUND(MIN(EXTRACT(EPOCH FROM (fecha_resolucion - created_at)) / 3600)::numeric, 2) AS horas_minimo,
                ROUND(MAX(EXTRACT(EPOCH FROM (fecha_resolucion - created_at)) / 3600)::numeric, 2) AS horas_maximo
            FROM incidencias
            WHERE fecha_resolucion IS NOT NULL
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement("DROP VIEW IF EXISTS vista_tiempo_resolucion");
        DB::statement("DROP VIEW IF EXISTS vista_incidencias_por_tipo_ciudad");
        DB::statement("DROP VIEW IF EXISTS vista_incidencias_por_estado");
        DB::statement("DROP TRIGGER IF EXISTS trg_fecha_resolucion ON incidencias");
        DB::statement("DROP FUNCTION IF EXISTS registrar_fecha_resolucion");
        DB::statement("ALTER TABLE incidencias DROP COLUMN IF EXISTS fecha_resolucion");
    }
};