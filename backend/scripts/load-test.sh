#!/bin/bash

# =====================================================
# Prueba de carga básica - Sistema de Incidencias
# Ejecutar dentro del contenedor backend:
# docker compose exec backend bash scripts/load-test.sh
# =====================================================

set -u

BASE_URL="http://nginx"
EMAIL="admin@incidencias.com"
PASSWORD="Admin123!"

TOTAL_SECUENCIAL=50
CONCURRENTES=10
RONDAS=5

CONNECT_TIMEOUT=5
MAX_TIME=30

echo "======================================================"
echo " PRUEBA DE CARGA - Sistema de Incidencias"
echo " Fecha: $(date '+%Y-%m-%d %H:%M:%S')"
echo "======================================================"

# ---------- 1. Obtener token ----------
echo ""
echo "[1/3] Autenticando..."

LOGIN_RESPONSE=$(curl -sS \
    --connect-timeout "$CONNECT_TIMEOUT" \
    --max-time "$MAX_TIME" \
    -X POST "$BASE_URL/api/login" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d "{\"email\":\"$EMAIL\",\"password\":\"$PASSWORD\"}")

TOKEN=$(echo "$LOGIN_RESPONSE" \
    | grep -o '"token":"[^"]*' \
    | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "ERROR: no se pudo obtener el token."
    echo "Respuesta recibida:"
    echo "$LOGIN_RESPONSE"
    exit 1
fi

echo "Token obtenido correctamente."

# ---------- 2. Prueba secuencial ----------
echo ""
echo "[2/3] Prueba secuencial: $TOTAL_SECUENCIAL peticiones a GET /api/incidencias"

TIEMPOS_FILE=$(mktemp)

limpiar_archivos() {
    rm -f "$TIEMPOS_FILE"
}

trap limpiar_archivos EXIT

OK=0
ERRORES=0

INICIO=$(date +%s.%N)

for i in $(seq 1 "$TOTAL_SECUENCIAL"); do
    RESULTADO=$(curl -sS \
        --connect-timeout "$CONNECT_TIMEOUT" \
        --max-time "$MAX_TIME" \
        -o /dev/null \
        -w "%{time_total} %{http_code}" \
        -H "Authorization: Bearer $TOKEN" \
        -H "Accept: application/json" \
        "$BASE_URL/api/incidencias")

    CURL_STATUS=$?

    if [ "$CURL_STATUS" -ne 0 ]; then
        TIEMPO="$MAX_TIME"
        CODIGO="000"
    else
        TIEMPO=$(echo "$RESULTADO" | awk '{print $1}')
        CODIGO=$(echo "$RESULTADO" | awk '{print $2}')
    fi

    echo "$TIEMPO" >> "$TIEMPOS_FILE"

    if [ "$CODIGO" = "200" ]; then
        OK=$((OK + 1))
        ESTADO="OK"
    else
        ERRORES=$((ERRORES + 1))
        ESTADO="ERROR"
    fi

    echo "  Petición $i/$TOTAL_SECUENCIAL - HTTP $CODIGO - ${TIEMPO}s - $ESTADO"
done

FIN=$(date +%s.%N)
DURACION=$(awk -v f="$FIN" -v i="$INICIO" 'BEGIN { printf "%.2f", f - i }')

PROMEDIO=$(awk '
    NF > 0 {
        suma += $1
        cantidad++
    }
    END {
        if (cantidad > 0) {
            printf "%.3f", suma / cantidad
        } else {
            printf "0.000"
        }
    }
' "$TIEMPOS_FILE")

MINIMO=$(sort -n "$TIEMPOS_FILE" | head -1)
MAXIMO=$(sort -n "$TIEMPOS_FILE" | tail -1)

RPS=$(awk -v t="$TOTAL_SECUENCIAL" -v d="$DURACION" 'BEGIN { if (d > 0) printf "%.2f", t / d; else printf "0.00" }')

echo ""
echo "Resumen secuencial:"
echo "  Peticiones exitosas (200): $OK"
echo "  Peticiones con error:      $ERRORES"
echo "  Tiempo promedio:           ${PROMEDIO}s"
echo "  Tiempo mínimo:             ${MINIMO}s"
echo "  Tiempo máximo:             ${MAXIMO}s"
echo "  Duración total:            ${DURACION}s"
echo "  Peticiones por segundo:    $RPS"

# ---------- 3. Prueba concurrente ----------
echo ""
echo "[3/3] Prueba concurrente: $RONDAS rondas de $CONCURRENTES peticiones simultáneas"

INICIO=$(date +%s.%N)
OK_CONC=0
ERROR_CONC=0

for ronda in $(seq 1 "$RONDAS"); do
    PIDS=()
    CODIGOS_FILE=$(mktemp)

    for i in $(seq 1 "$CONCURRENTES"); do
        (
            CODIGO=$(curl -sS \
                --connect-timeout "$CONNECT_TIMEOUT" \
                --max-time "$MAX_TIME" \
                -o /dev/null \
                -w "%{http_code}" \
                -H "Authorization: Bearer $TOKEN" \
                -H "Accept: application/json" \
                "$BASE_URL/api/incidencias")

            CURL_STATUS=$?

            if [ "$CURL_STATUS" -ne 0 ]; then
                echo "000" >> "$CODIGOS_FILE"
            else
                echo "$CODIGO" >> "$CODIGOS_FILE"
            fi
        ) &

        PIDS+=($!)
    done

    for pid in "${PIDS[@]}"; do
        wait "$pid"
    done

    EXITOSAS=$(grep -c '^200$' "$CODIGOS_FILE" || true)
    TOTAL_RONDA=$(wc -l < "$CODIGOS_FILE")
    FALLIDAS=$((TOTAL_RONDA - EXITOSAS))

    OK_CONC=$((OK_CONC + EXITOSAS))
    ERROR_CONC=$((ERROR_CONC + FALLIDAS))

    echo "  Ronda $ronda: $EXITOSAS/$CONCURRENTES exitosas"

    rm -f "$CODIGOS_FILE"
done

FIN=$(date +%s.%N)
DURACION=$(awk -v f="$FIN" -v i="$INICIO" 'BEGIN { printf "%.2f", f - i }')
TOTAL_CONC=$((RONDAS * CONCURRENTES))

RPS_CONC=$(awk -v t="$TOTAL_CONC" -v d="$DURACION" 'BEGIN { if (d > 0) printf "%.2f", t / d; else printf "0.00" }')

echo ""
echo "======================================================"
echo " RESUMEN CONCURRENTE"
echo "  Total peticiones:       $TOTAL_CONC"
echo "  Exitosas:               $OK_CONC"
echo "  Con error:              $ERROR_CONC"
echo "  Duración:               ${DURACION}s"
echo "  Peticiones por segundo: $RPS_CONC"
echo "======================================================"
echo "Prueba de carga finalizada."