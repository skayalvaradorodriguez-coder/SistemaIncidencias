#!/bin/bash
# =====================================================
# Prueba de carga básica - Sistema de Incidencias
# Ejecutar dentro del contenedor backend:
# docker compose exec backend bash scripts/load-test.sh
# =====================================================

BASE_URL="http://nginx"
EMAIL="admin@incidencias.com"
PASSWORD="Admin123!"
TOTAL_SECUENCIAL=50
CONCURRENTES=10
RONDAS=5

echo "======================================================"
echo " PRUEBA DE CARGA - Sistema de Incidencias"
echo " Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================"

# ---------- 1. Obtener token ----------
echo ""
echo "[1/3] Autenticando..."

TOKEN=$(curl -s -X POST "$BASE_URL/api/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}" \
    | grep -o '"token":"[^"]*' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "ERROR: no se pudo obtener el token. Verifique credenciales y que el sistema esté arriba."
    exit 1
fi

echo "Token obtenido correctamente."

# ---------- 2. Prueba secuencial ----------
echo ""
echo "[2/3] Prueba secuencial: $TOTAL_SECUENCIAL peticiones a GET /api/incidencias"

TIEMPOS_FILE=$(mktemp)
OK=0
ERRORES=0

INICIO=$(date +%s.%N)

for i in $(seq 1 $TOTAL_SECUENCIAL); do
    RESULTADO=$(curl -s -o /dev/null -w "%{time_total} %{http_code}" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        "$BASE_URL/api/incidencias")

    TIEMPO=$(echo "$RESULTADO" | cut -d' ' -f1)
    CODIGO=$(echo "$RESULTADO" | cut -d' ' -f2)

    echo "$TIEMPO" >> "$TIEMPOS_FILE"

    if [ "$CODIGO" = "200" ]; then
        OK=$((OK+1))
    else
        ERRORES=$((ERRORES+1))
    fi
done

FIN=$(date +%s.%N)
DURACION=$(echo "$FIN - $INICIO" | bc)

PROMEDIO=$(awk '{s+=$1} END {printf "%.3f", s/NR}' "$TIEMPOS_FILE")
MINIMO=$(sort -n "$TIEMPOS_FILE" | head -1)
MAXIMO=$(sort -n "$TIEMPOS_FILE" | tail -1)
RPS=$(echo "scale=2; $TOTAL_SECUENCIAL / $DURACION" | bc)

echo "  Peticiones exitosas (200): $OK"
echo "  Peticiones con error:      $ERRORES"
echo "  Tiempo promedio:           ${PROMEDIO}s"
echo "  Tiempo mínimo:             ${MINIMO}s"
echo "  Tiempo máximo:             ${MAXIMO}s"
echo "  Duración total:            ${DURACION}s"
echo "  Peticiones por segundo:    $RPS"

rm -f "$TIEMPOS_FILE"

# ---------- 3. Prueba concurrente ----------
echo ""
echo "[3/3] Prueba concurrente: $RONDAS rondas de $CONCURRENTES peticiones simultáneas"

INICIO=$(date +%s.%N)
OK_CONC=0

for ronda in $(seq 1 $RONDAS); do
    PIDS=()
    CODIGOS_FILE=$(mktemp)

    for i in $(seq 1 $CONCURRENTES); do
        (curl -s -o /dev/null -w "%{http_code}\n" \
            -H "Authorization: Bearer $TOKEN" \
            -H "Accept: application/json" \
            "$BASE_URL/api/incidencias" >> "$CODIGOS_FILE") &
        PIDS+=($!)
    done

    for pid in "${PIDS[@]}"; do
        wait "$pid"
    done

    EXITOSAS=$(grep -c "200" "$CODIGOS_FILE")
    OK_CONC=$((OK_CONC + EXITOSAS))
    echo "  Ronda $ronda: $EXITOSAS/$CONCURRENTES exitosas"
    rm -f "$CODIGOS_FILE"
done

FIN=$(date +%s.%N)
DURACION=$(echo "$FIN - $INICIO" | bc)
TOTAL_CONC=$((RONDAS * CONCURRENTES))
RPS_CONC=$(echo "scale=2; $TOTAL_CONC / $DURACION" | bc)

echo ""
echo "======================================================"
echo " RESUMEN CONCURRENTE"
echo "  Total peticiones:       $TOTAL_CONC"
echo "  Exitosas:               $OK_CONC"
echo "  Duración:               ${DURACION}s"
echo "  Peticiones por segundo: $RPS_CONC"
echo "======================================================"
echo "Prueba de carga finalizada."