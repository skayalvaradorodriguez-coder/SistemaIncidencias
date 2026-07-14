# Sistema Web de Gestión de Incidencias Georreferenciadas

Proyecto integrador — Carrera de Software, Facultad de Sistemas y
Telecomunicaciones.

## Arquitectura

- **Backend:** Laravel 12 (API REST) + Sanctum para autenticación por token.
- **Frontend:** Blade + Bootstrap (AdminLTE) para el shell de las páginas.
- **Cliente:** JavaScript con `fetch` (`public/js/auth.js` y scripts en cada
  vista) que consume la API REST para todas las operaciones de creación,
  edición, eliminación, cambio de estado, comentarios, asignaciones y
  notificaciones.
- **Base de datos:** PostgreSQL.
- **Despliegue:** Docker (contenedores `backend`, `nginx`, `postgres`).

> Nota: el backend expone tanto vistas Blade (`routes/web.php`) como una API
> REST (`routes/api.php`). No son redundantes: Blade entrega el HTML inicial
> de cada página y el JavaScript del lado del cliente llama a la API para
> todas las operaciones de datos, tal como exige el alcance del proyecto
> ("Cliente: JavaScript (fetch)").

## Cómo ejecutar el sistema

### 1. Requisitos

- Docker y Docker Compose instalados.

### 2. Configuración

```bash
cp backend/.env.example backend/.env
```

El `.env.example` ya viene configurado para conectarse al contenedor de
Postgres del `docker-compose.yml` (host `postgres`, base `incidencias_db`).
Ajusta las credenciales solo si cambias el `docker-compose.yml`.

### 3. Levantar el stack

```bash
docker compose up -d --build
```

Al iniciar, el contenedor `backend` automáticamente:

1. Instala las dependencias de Composer (si no existen).
2. Genera `APP_KEY` (si falta).
3. Espera a que PostgreSQL esté disponible.
4. Ejecuta las migraciones (`php artisan migrate --force`).
5. Ejecuta los seeders base (roles, estados, ubicación, tipos, usuario
   administrador y usuarios de prueba). Los seeders son idempotentes: se
   pueden ejecutar varias veces sin duplicar datos.

Revisa el progreso con:

```bash
docker compose logs -f backend
```

### 4. Acceder al sistema

- URL: [http://localhost:8080](http://localhost:8080)

## Credenciales de acceso

| Rol | Email | Contraseña |
|---|---|---|
| Administrador | `admin@incidencias.com` | `Admin123!` |
| Responsable | `responsable@incidencias.com` | `Responsable123!` |
| Ciudadano | `ciudadano@incidencias.com` | `Ciudadano123!` |

## Datos de ejemplo (opcional)

Para poblar el sistema con incidencias de ejemplo (útil para capturas de
pantalla y la demostración funcional):

```bash
docker compose exec backend php artisan db:seed --class=IncidenciasDemoSeeder
```

## Pruebas automatizadas

```bash
docker compose exec backend php artisan test
```

Ver `docs/CALIDAD.md` para el detalle de la estrategia de pruebas y cómo
generar las evidencias exigidas por la rúbrica.

## Prueba de carga

```bash
./scripts/load-test.sh http://localhost:8080 admin@incidencias.com Admin123! 100 10
```

## Generar el dump de base de datos (entregable)

```bash
docker compose exec postgres pg_dump -U incidencias_user -d incidencias_db \
    --no-owner --no-privileges > database/scripts/incidencias_dump.sql
```

## Estructura del repositorio