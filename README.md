# Sistema Web de Gestión de Incidencias Georreferenciadas

Proyecto integrador — **Carrera de Software**, Facultad de Sistemas y Telecomunicaciones,
Universidad Estatal Península de Santa Elena (UPSE). Período académico 2026-1.

Sistema web para el registro, seguimiento y resolución de incidencias urbanas
(alumbrado, vías, alcantarillado, ambiente) con georreferenciación en mapa,
trazabilidad completa del ciclo de vida y control de acceso por roles.

---

## Tabla de contenidos

1. [Equipo de desarrollo](#equipo-de-desarrollo)
2. [Arquitectura](#arquitectura)
3. [Stack tecnológico](#stack-tecnológico)
4. [Estructura del repositorio](#estructura-del-repositorio)
5. [Roles y permisos](#roles-y-permisos)
6. [Módulos del sistema](#módulos-del-sistema)
7. [Instalación y ejecución](#instalación-y-ejecución)
8. [Despliegue en Render](#despliegue-en-render)
9. [Credenciales de acceso](#credenciales-de-acceso)
10. [Base de datos](#base-de-datos)
11. [Pruebas automatizadas](#pruebas-automatizadas)
12. [Rendimiento y optimización](#rendimiento-y-optimización)
13. [Criterios adicionales (bonificación extra)](#criterios-adicionales-bonificación-extra)
14. [Comandos útiles](#comandos-útiles)
15. [Solución de problemas](#solución-de-problemas)

---

## Equipo de desarrollo

| Integrante | Rol principal |
|---|---|
| Skay Gisell Alvarado Rodriguez | Backend y desarrollo frontend |
| Kerlly Belinda Mite Chalen | Desarrollo frontend |
| María De Los Ángeles Llerena Hernández | Desarrollo frontend |
| Peter Leonardo Villón Orrala | Seguridad, pruebas y calidad de software |
| Byron Andrés Velecela Méndez | Seguridad, pruebas y calidad de software |

---

## Arquitectura

El sistema sigue una arquitectura de **tres capas desplegadas en contenedores Docker**:

```
┌─────────────┐     ┌──────────────┐     ┌─────────────────┐     ┌──────────────┐
│  Navegador  │────▶│    Nginx     │────▶│  PHP-FPM 8.4    │────▶│ PostgreSQL15 │
│ (JS fetch)  │◀────│  :8080 → :80 │◀────│  Laravel + API  │◀────│    :5432     │
└─────────────┘     └──────────────┘     └─────────────────┘     └──────────────┘
   Cliente          Servidor web           Aplicación             Base de datos
                    (proxy FastCGI)        (OPcache activo)       (volumen persistente)
```

- **Blade** entrega el HTML inicial de cada página (el "shell" de la interfaz).
- **JavaScript (`fetch`)** consume la **API REST** para todas las operaciones de datos:
  creación, edición, eliminación, cambio de estado, comentarios, asignaciones y notificaciones.
- **Laravel Sanctum** gestiona la autenticación por token (sin sesiones en servidor),
  lo que permite escalar horizontalmente sin sesiones pegajosas.

> **Nota sobre `web.php` vs `api.php`:** no son redundantes. `routes/web.php` sirve las
> vistas Blade; `routes/api.php` expone los endpoints REST que consume el JavaScript del
> cliente, tal como exige el alcance del proyecto.

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Servidor web | Nginx (alpine) con cabeceras de seguridad |
| Backend | PHP 8.4-FPM + Laravel 12 + Sanctum |
| Frontend | Blade + AdminLTE 3 + Bootstrap 4 |
| Cliente | JavaScript nativo (`fetch`), sin frameworks |
| Mapas | Leaflet 1.9.4 + OpenStreetMap + Leaflet.heat |
| Gráficos | Chart.js 2.x |
| Base de datos | PostgreSQL 15 |
| Contenedores | Docker + Docker Compose |
| Pruebas | PHPUnit (19 casos automatizados) |

---

## Estructura del repositorio

```
Sistema_Incidencias/
│
├── backend/                          # Aplicación Laravel
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/          # Controladores (API + vistas)
│   │   │   │   ├── AuthController.php            # Login, registro, perfil
│   │   │   │   ├── IncidenciaController.php      # CRUD y vistas de incidencias
│   │   │   │   ├── UsuarioController.php         # Gestión de usuarios (admin)
│   │   │   │   ├── ComentarioController.php      # Chat de seguimiento
│   │   │   │   ├── AsignacionController.php      # Asignación de responsables
│   │   │   │   ├── NotificacionController.php    # Notificaciones internas
│   │   │   │   ├── DashboardController.php       # Métricas y analítica
│   │   │   │   ├── EstadoController.php          # Catálogo de estados
│   │   │   │   ├── TipoController.php            # Catálogo de tipos/subtipos
│   │   │   │   └── UbicacionController.php       # País → provincia → ciudad
│   │   │   └── Middleware/
│   │   │       └── VerificarRol.php              # Control de acceso por rol
│   │   ├── Models/                   # Modelos Eloquent
│   │   │   ├── Incidencia.php              ├── User.php
│   │   │   ├── HistorialEstado.php         ├── Rol.php
│   │   │   ├── Asignacion.php              ├── Pais.php
│   │   │   ├── Comentario.php              ├── Provincia.php
│   │   │   ├── Notificacion.php            ├── Ciudad.php
│   │   │   ├── EstadoIncidencia.php        ├── TipoIncidencia.php
│   │   │   └── SubtipoIncidencia.php
│   │   └── Services/
│   │       └── NotificacionService.php           # Lógica de notificaciones
│   │
│   ├── database/
│   │   ├── migrations/               # 21 migraciones (esquema + trigger + vistas SQL)
│   │   └── seeders/
│   │       ├── RolesSeeder.php               # Administrador, Responsable, Ciudadano
│   │       ├── EstadosIncidenciaSeeder.php   # Pendiente, En Proceso, Resuelto, Rechazado
│   │       ├── TiposIncidenciaSeeder.php     # Tipos y subtipos de incidencia
│   │       ├── UbicacionSeeder.php           # Provincias y ciudades del Ecuador
│   │       ├── AdminSeeder.php               # Usuario administrador
│   │       └── DemoUsersSeeder.php           # Usuarios de prueba
│   │
│   ├── resources/views/              # Vistas Blade
│   │   ├── layouts/app.blade.php     # Plantilla base (menú, notificaciones)
│   │   ├── login.blade.php           # Inicio de sesión y registro
│   │   ├── dashboard.blade.php       # Panel principal con mapa y analítica
│   │   ├── reportes.blade.php        # Centro de reportes con exportación PDF
│   │   ├── emergencias.blade.php     # Directorio de contactos de emergencia
│   │   ├── perfil.blade.php          # Datos personales y cambio de contraseña
│   │   ├── incidencias/
│   │   │   ├── index.blade.php       # Listado con filtros y exportación CSV
│   │   │   ├── create.blade.php      # Registro con mapa y foto
│   │   │   ├── edit.blade.php        # Edición
│   │   │   ├── show.blade.php        # Detalle, historial, chat, asignaciones
│   │   │   ├── tablero.blade.php     # Tablero Kanban (drag & drop)
│   │   │   └── mis-reportes.blade.php # Vista del ciudadano
│   │   └── usuarios/                 # CRUD de usuarios (solo administrador)
│   │
│   ├── routes/
│   │   ├── web.php                   # Rutas de vistas Blade
│   │   └── api.php                   # Endpoints REST (agrupados por rol)
│   │
│   ├── public/
│   │   ├── js/auth.js                # Token, sesión y helpers de rol
│   │   ├── dist/ y plugins/          # AdminLTE, Bootstrap, Chart.js
│   │   └── storage/                  # Enlace a fotos de incidencias
│   │
│   ├── tests/Feature/                # Pruebas automatizadas (PHPUnit)
│   │   ├── AuthTest.php              # 7 casos: login, registro, throttle
│   │   ├── SeguridadRolesTest.php    # 6 casos: control de acceso (403)
│   │   ├── IncidenciaTest.php        # 6 casos: ciclo de vida
│   │   └── ComentarioTest.php        # 3 casos: seguimiento
│   │
│   └── scripts/
│       └── load-test.sh              # Prueba de carga (curl + bash)
│
├── database/
│   └── scripts/
│       └── incidencias_db.sql        # Dump completo (esquema + datos + trigger + vistas)
│
├── docker/
│   ├── php/
│   │   ├── Dockerfile                # Imagen PHP 8.4-FPM con OPcache
│   │   └── entrypoint.sh             # Arranque: composer, migrate, seed, cachés
│   ├── nginx/
│   │   └── default.conf              # Proxy FastCGI, balanceo round-robin y cabeceras de seguridad
│   └── render/                       # Configuración para despliegue en Render
│       ├── Dockerfile                # Imagen combinada (Nginx + PHP-FPM)
│       ├── entrypoint.sh
│       ├── nginx.conf.template
│       └── supervisord.conf
│
├── docs/
│   └── CALIDAD.md                    # Estrategia de pruebas, métricas y hallazgos
│
├── docker-compose.yml                # Orquestación de 4 contenedores (2 instancias backend + nginx + postgres)
├── .gitattributes                    # Normalización de finales de línea (LF)
├── .gitignore
└── README.md
```

---

## Roles y permisos

| Acción | Ciudadano | Responsable | Administrador |
|---|:---:|:---:|:---:|
| Registrar incidencia | ✅ | ✅ | ✅ |
| Consultar listado y detalle | ✅ | ✅ | ✅ |
| Comentar (chat de seguimiento) | ✅ | ✅ | ✅ |
| Ver "Mis Reportes" y descargar PDF | ✅ | ✅ | ✅ |
| Cambiar estado de una incidencia | ❌ | ✅ | ✅ |
| Asignar responsables | ❌ | ✅ | ✅ |
| Tablero Kanban | ❌ | ✅ | ✅ |
| Centro de Reportes | ❌ | ✅ | ✅ |
| Analítica del dashboard | ❌ | ✅ | ✅ |
| Gestionar usuarios | ❌ | ❌ | ✅ |
| Eliminar incidencias | ❌ | ❌ | ✅ |
| Administrar catálogos | ❌ | ❌ | ✅ |

> El control de acceso se aplica **en el servidor** mediante el middleware
> `VerificarRol`, no solo ocultando botones en la interfaz. Un usuario sin
> permisos recibe **HTTP 403** aunque invoque la API directamente.

---

## Módulos del sistema

| Módulo | Descripción |
|---|---|
| **Autenticación** | Login y registro con token (Sanctum), límite de 5 intentos/minuto, contraseñas fuertes |
| **Incidencias** | CRUD completo con ubicación en mapa, fotografía opcional, prioridad y clasificación jerárquica |
| **Ciclo de vida** | Estados Pendiente → En Proceso → Resuelto (+ Rechazado), con historial inmutable |
| **Tablero Kanban** | Gestión visual con arrastrar y soltar; en móvil, selector táctil de estado |
| **Mapa georreferenciado** | Marcadores por estado y mapa de calor de zonas críticas (Leaflet) |
| **Dashboard analítico** | Indicadores, tasa de resolución, tiempo promedio, tendencia mensual y top ciudades/provincias |
| **Centro de Reportes** | Reportes filtrables por estado y de usuarios, con KPIs y exportación a PDF |
| **Chat de seguimiento** | Conversación por incidencia entre ciudadano y gestores |
| **Notificaciones** | Avisos internos por registro, cambio de estado, asignación y comentarios |
| **Mis Reportes** | Vista del ciudadano con progreso de sus incidencias y reporte PDF |
| **Emergencias** | Directorio de servicios (ECU 911, Policía, Bomberos) y GAD municipales por provincia |
| **Mi Perfil** | Actualización de datos personales y cambio de contraseña |
| **Usuarios** | Alta, edición y desactivación de cuentas (solo administrador) |

---

## Instalación y ejecución

### 1. Requisitos previos

- Docker Desktop y Docker Compose instalados.
- Git.

> **Importante (Windows):** clona el proyecto en una ruta local corta y **fuera de
> OneDrive** (por ejemplo `C:\Proyectos\`). Las carpetas sincronizadas por la nube
> bloquean archivos y degradan gravemente el rendimiento de los volúmenes Docker.

### 2. Clonar y configurar

```bash
git clone https://github.com/skayalvaradorodriguez-coder/SistemaIncidencias.git
cd SistemaIncidencias
cp backend/.env.example backend/.env
```

El archivo `.env.example` ya viene configurado para el contenedor de PostgreSQL
definido en `docker-compose.yml` (host `postgres`, base `incidencias_db`).

### 3. Levantar el stack

```bash
docker compose up -d --build
```

Al iniciar, el contenedor `backend` ejecuta automáticamente:

1. Instalación de dependencias de Composer (si no existen).
2. Generación de `APP_KEY` (si falta).
3. Espera a que PostgreSQL esté disponible (healthcheck).
4. Migraciones (`php artisan migrate --force`).
5. Seeders base (idempotentes: no duplican datos al re-ejecutarse).
6. Limpieza y cacheo de configuración.

Sigue el progreso con:

```bash
docker compose logs -f backend
```

El sistema está listo cuando aparece `ready to handle connections`
(el arranque completo toma 1–2 minutos; antes de eso Nginx puede devolver **502**).

### 4. Acceder

**http://localhost:8080**

---

## Despliegue en Render

El proyecto incluye una imagen combinada (Nginx + PHP-FPM en un solo contenedor,
orquestados con `supervisord`) en `docker/render/`, pensada para plataformas
tipo Render que solo aceptan **un** contenedor web por servicio.

### Configuración del servicio

- **Root Directory:** la raíz del repositorio (donde está este `README.md`).
- **Dockerfile Path:** `docker/render/Dockerfile`
- **Docker Context:** raíz del repositorio (el Dockerfile copia `backend/` desde ahí).
- **Base de datos:** un servicio de PostgreSQL aparte en Render (o externo), conectado
  por las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

### Variables de entorno obligatorias

| Variable | Valor |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://<tu-servicio>.onrender.com` (la URL real que asigna Render, **con `https://`**) |
| `APP_KEY` | se genera solo en el primer arranque si falta |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | credenciales del servicio de PostgreSQL |
| `DB_SEED` | `true` solo en el primer despliegue, para ejecutar los seeders iniciales |

### Importante: HTTPS detrás del proxy de Render

Render termina el HTTPS en su proxy y reenvía las peticiones al contenedor por
HTTP interno. Si Laravel no confía en ese proxy, genera los enlaces de
`asset()` (CSS, JS, imágenes) con `http://` en vez de `https://`, y el
navegador los bloquea por **contenido mixto** — el síntoma es un sitio
funcional pero completamente sin estilos, como HTML plano.

Esto ya está resuelto en el código con dos cambios:

1. **`backend/bootstrap/app.php`** — `trustProxies(at: '*')` para que Laravel
   reconozca el encabezado `X-Forwarded-Proto` que manda Render.
2. **`backend/app/Providers/AppServiceProvider.php`** — `URL::forceScheme('https')`
   como respaldo cuando `APP_ENV=production`.

Si en un despliegue nuevo los estilos no cargan, revisa primero que `APP_URL`
esté configurada con `https://` en las variables de entorno de Render, y luego
haz **Manual Deploy → Clear build cache & deploy** (el entrypoint corre
`php artisan config:cache`, así que una config vieja puede quedar cacheada).

### Primer despliegue

1. Crea el servicio en Render apuntando al Dockerfile de `docker/render/`.
2. Configura las variables de entorno de la tabla anterior.
3. Despliega. El `entrypoint.sh` de `docker/render/` se encarga de:
   - Instalar dependencias de Composer si `vendor/` no existe.
   - Generar `APP_KEY` si falta.
   - Esperar a que PostgreSQL acepte conexiones.
   - Ejecutar migraciones (`php artisan migrate --force`).
   - Ejecutar seeders si `DB_SEED=true`.
   - Cachear la configuración y arrancar Nginx + PHP-FPM vía `supervisord`.
4. Verifica los logs del servicio hasta ver `Listo. Iniciando supervisord...`.

---

## Credenciales de acceso

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@incidencias.com` | `Admin123!` |
| Responsable | `responsable@incidencias.com` | `Responsable123!` |
| Ciudadano | `ciudadano@incidencias.com` | `Ciudadano123!` |

---

## Base de datos

### Modelo

15 tablas relacionadas. Núcleo del sistema:

- `incidencias` — registro principal (título, descripción, prioridad, coordenadas, foto, estado)
- `historial_estados` — trazabilidad de cada transición (solo inserción)
- `asignaciones` — responsables asignados por incidencia
- `comentarios` — chat de seguimiento
- `notificaciones` — avisos internos
- `users` / `roles` — autenticación y autorización
- `paises` → `provincias` → `ciudades` — ubicación normalizada
- `tipos_incidencia` → `subtipos_incidencia` — clasificación jerárquica
- `estados_incidencia` — catálogo del ciclo de vida

### Programación SQL

Incluida en la migración `crear_vistas_y_trigger_sql`:

- **Función + trigger** `trg_fecha_resolucion`: registra automáticamente
  `fecha_resolucion` cuando una incidencia pasa a estado *Resuelto*.
- **Vistas de monitoreo:**
  - `vista_incidencias_por_estado` — distribución porcentual
  - `vista_incidencias_por_tipo_ciudad` — concentración geográfica
  - `vista_tiempo_resolucion` — promedio, mínimo y máximo de horas

### Restaurar el dump

```bash
docker cp database/scripts/incidencias_db.sql incidencias_db:/tmp/restore.sql
docker compose exec postgres psql -U incidencias_user -d incidencias_db -f /tmp/restore.sql
```

> **Windows:** usa siempre `docker cp` + `psql -f`. Nunca `Get-Content archivo.sql |`,
> porque PowerShell corrompe los acentos y la eñe al pasar el archivo por la tubería.

### Generar un dump nuevo

```bash
docker compose exec postgres pg_dump -U incidencias_user -d incidencias_db -f /tmp/dump.sql
docker cp incidencias_db:/tmp/dump.sql database/scripts/incidencias_db.sql
```

---

## Pruebas automatizadas

19 casos con PHPUnit sobre SQLite en memoria (no afectan la base real):

```bash
docker compose exec backend php artisan test
```

| Suite | Casos | Cobertura |
|---|:---:|---|
| `AuthTest` | 7 | Login, usuario inactivo, registro, contraseña débil, throttle (429), acceso sin token (401) |
| `SeguridadRolesTest` | 6 | Bloqueo de ciudadano en rutas administrativas (403), permisos por rol |
| `IncidenciaTest` | 6 | Creación con historial y notificaciones, validaciones, cambio de estado, filtros |
| `ComentarioTest` | 3 | Registro de autor, notificación, validación de contenido |

Detalle completo en [`docs/CALIDAD.md`](docs/CALIDAD.md).

---

## Rendimiento y optimización

Resultados de la prueba de carga (`backend/scripts/load-test.sh`):

| Escenario | Peticiones | Éxito | Promedio | Throughput |
|---|:---:|:---:|:---:|:---:|
| Secuencial | 50 | 100 % | 0.254 s | 3.73 req/s |
| Concurrente (10 simultáneos) | 50 | 100 % | — | 23.47 req/s |

**Optimizaciones aplicadas** (mejora medida superior a **100×**):

1. **OPcache** habilitado (192 MB, 20 000 archivos) — evita recompilar el framework en cada petición.
2. **`pm.max_children`** de 5 a 10 workers PHP-FPM.
3. **Volúmenes nativos** para `vendor/` y `storage/framework` — elimina la latencia del
   montaje Windows→Linux, que era el principal cuello de botella.
4. Proyecto **fuera de carpetas sincronizadas** por la nube.

Ejecutar la prueba:

```bash
docker compose exec backend bash scripts/load-test.sh
```

---

## Criterios adicionales (bonificación extra)

Además de los requerimientos mínimos, el proyecto implementa las tres mejoras
avanzadas contempladas en la rúbrica (sección "Criterios adicionales" de los
lineamientos, hasta 5 puntos extra):

### 1. Despliegue con múltiples instancias (escalamiento básico)

`docker-compose.yml` levanta **dos instancias independientes** del backend
Laravel (`backend` y `backend2`), cada una en su propio contenedor, ambas
conectadas a la misma base de datos PostgreSQL:

```
                    ┌──────────────────┐
                    │  Nginx (:8080)   │
                    │  upstream        │
                    │  round-robin     │
                    └───────┬──────────┘
                ┌───────────┴───────────┐
                ▼                       ▼
      ┌──────────────────┐    ┌──────────────────┐
      │  backend (1)     │    │  backend2 (2)     │
      │  PHP-FPM 8.4     │    │  PHP-FPM 8.4      │
      └──────────────────┘    └──────────────────┘
                └───────────┬───────────┘
                            ▼
                  ┌──────────────────┐
                  │   PostgreSQL 15  │
                  └──────────────────┘
```

Nginx (`docker/nginx/default.conf`) define un bloque `upstream php_backend`
que reparte cada petición PHP entre `backend:9000` y `backend2:9000` en
**round-robin**, con `max_fails`/`fail_timeout` para sacar automáticamente de
la rotación a la instancia que falle.

### 2. Configuraciones adicionales en contenedores

- **Límites de recursos** (`deploy.resources.limits`) de `1 CPU` / `512 MB`
  por cada instancia del backend, para simular un entorno de producción con
  cuotas controladas.
- **Healthchecks** propios en los tres servicios (`postgres`, `backend`,
  `backend2`) — Nginx y la segunda instancia solo arrancan cuando sus
  dependencias están realmente listas (`condition: service_healthy`), no solo
  "iniciadas".
- **Volúmenes nombrados separados** por instancia
  (`framework_cache_1` / `framework_cache_2`) para que las cachés de vistas
  de cada contenedor no choquen entre sí, además de un volumen compartido
  (`vendor_data`) para no reinstalar Composer dos veces.
- **Compresión gzip** habilitada en Nginx para las respuestas de texto/CSS/JS/JSON.

### 3. Optimización más allá de los requerimientos mínimos

Documentado con datos medibles en la sección
[Rendimiento y optimización](#rendimiento-y-optimización):
OPcache (192 MB / 20 000 archivos), `pm.max_children` ajustado de 5 a 10, y
volúmenes nativos para `vendor/` y `storage/framework` en vez de bind mounts
directos — la combinación llevó el throughput de una petición aislada a más
de **23 req/s** en escenario concurrente, una mejora superior a 100× frente a
la configuración por defecto.

> **Nota:** el despliegue con múltiples instancias descrito aquí aplica al
> entorno local con `docker-compose.yml`. El despliegue en Render
> (ver [Despliegue en Render](#despliegue-en-render)) usa una sola instancia
> combinada por restricciones del plan gratuito de la plataforma.

---

## Comandos útiles

```bash
# Estado de los contenedores
docker compose ps

# Logs
docker compose logs -f backend
docker compose logs --tail=30 nginx

# Limpiar cachés tras cambiar rutas o controladores
docker compose exec backend php artisan optimize:clear

# Limpiar solo vistas (tras editar archivos .blade.php)
docker compose exec backend php artisan view:clear

# Consola de la base de datos
docker compose exec postgres psql -U incidencias_user -d incidencias_db

# Consultar las vistas de monitoreo
docker compose exec postgres psql -U incidencias_user -d incidencias_db \
  -c "SELECT * FROM vista_incidencias_por_estado;"

# Detener el sistema (conserva los datos)
docker compose down
```

> ⚠️ **Nunca uses `docker compose down -v`**: la bandera `-v` elimina el volumen
> de PostgreSQL y con él **todos los datos**.

---

## Solución de problemas

| Síntoma | Causa | Solución |
|---|---|---|
| **502 Bad Gateway** al arrancar | PHP-FPM aún no está listo | Esperar a `ready to handle connections` en `docker compose logs backend` |
| **502 persistente** | Nginx apunta a una IP antigua del backend | `docker compose restart nginx` |
| `entrypoint.sh: not found` | El archivo tiene finales de línea CRLF | Ya resuelto con `.gitattributes`; si persiste: `docker compose build --no-cache backend` |
| Ruta nueva da **404** | Rutas cacheadas | `docker compose exec backend php artisan optimize:clear` |
| Cambios en vistas no se reflejan | Vistas compiladas en caché | `php artisan view:clear` + `Ctrl+Shift+R` en el navegador |
| Acentos corruptos (`p??blico`) | Dump restaurado con tubería de PowerShell | Restaurar con `docker cp` + `psql -f` |
| Sistema muy lento | Proyecto en carpeta sincronizada (OneDrive) | Mover a una ruta local como `C:\Proyectos\` |
| No se puede iniciar sesión | Contraseña modificada en pruebas | Restablecerla con `php artisan tinker` dentro del contenedor |
| **En Render: página sin estilos** (HTML plano, sin CSS/JS) | Laravel genera los `asset()` con `http://` detrás del proxy HTTPS de Render (contenido mixto bloqueado por el navegador) | Ver [Despliegue en Render](#despliegue-en-render): confirmar `APP_URL=https://...` y que `trustProxies` esté configurado en `bootstrap/app.php` |

---

## Licencia

Proyecto académico desarrollado para la Universidad Estatal Península de Santa Elena.
Uso educativo.