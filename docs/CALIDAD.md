# Aseguramiento de Calidad — Sistema de Gestión de Incidencias Georreferenciadas

**Equipo:** Skay Alvarado, Peter Villón, Byron Velecela
**Carrera de Software — UPSE — Período 2026-1**

## 1. Validaciones implementadas

| Capa | Validación |
|---|---|
| Frontend | Campos obligatorios, formato de correo, requisitos de contraseña en vivo (8+ caracteres, mayúscula, minúscula, número), coordenadas capturadas solo desde el mapa (campos de solo lectura) |
| Backend | Validación de peticiones en todos los endpoints (título máx. 200, prioridad en lista cerrada, coordenadas en rango −90/90 y −180/180, existencia de claves foráneas) |
| Backend | Control de acceso por roles (middleware VerificarRol), límite de intentos de login (5/minuto), política de contraseñas fuertes, protección contra autodesactivación del administrador |
| Frontend | Escape de HTML en los mensajes del chat de seguimiento (prevención de XSS) |
| Servidor | Cabeceras X-Frame-Options, X-Content-Type-Options y Referrer-Policy; server_tokens off en Nginx |

## 2. Casos de prueba funcionales (automatizados)

Suite de 19 pruebas ejecutadas con PHPUnit sobre base de datos SQLite en memoria (`php artisan test`), independiente de la base de producción.

| Módulo | Casos | Qué verifica |
|---|---|---|
| Autenticación (AuthTest) | 7 | Login correcto/incorrecto, usuario inactivo (403), registro con rol Ciudadano, rechazo de contraseña débil (422), bloqueo por throttle (429), acceso sin token (401) |
| Seguridad por roles (SeguridadRolesTest) | 6 | Ciudadano bloqueado en rutas administrativas (403), permisos correctos de Administrador y Responsable, protección de autodesactivación (422) |
| Incidencias (IncidenciaTest) | 6 | Creación con historial y notificaciones a gestores, validación de campos y coordenadas, cambio de estado con trazabilidad y notificación al reportante, rechazo de estado repetido, filtros del listado |
| Comentarios (ComentarioTest) | 3 | Registro de autor, notificación al reportante, rechazo de comentario vacío, incidencia inexistente (404) |

**Resultado: 19/19 pruebas exitosas.** Evidencia en anexos del documento técnico.

## 3. Prueba de carga

Script propio: `backend/scripts/load-test.sh` (curl + bash), ejecutado dentro del contenedor backend contra Nginx, con autenticación real por token.

**Escenario 1 — Secuencial:** 50 peticiones GET /api/incidencias autenticadas.

| Métrica | Resultado |
|---|---|
| Peticiones exitosas | 50/50 (100 %) |
| Tiempo promedio | 0.254 s |
| Tiempo mínimo / máximo | 0.055 s / 3.198 s |
| Duración total | 13.41 s |
| Peticiones por segundo | 3.73 |

**Escenario 2 — Concurrente:** 5 rondas de 10 peticiones simultáneas (50 en total).

| Métrica | Resultado |
|---|---|
| Peticiones exitosas | 50/50 (100 %) |
| Duración total | 2.13 s |
| Peticiones por segundo | 23.47 |

Los picos aislados (~1.5–3 s) corresponden a la revalidación periódica de OPcache; el resto de las peticiones se sirvió entre 55 y 135 ms.

## 4. Optimización de rendimiento (antes / después)

Durante las pruebas de carga iniciales se detectó un problema grave de rendimiento, diagnosticado con los logs de Nginx (errores `upstream timed out`).

| Métrica | Antes | Después |
|---|---|---|
| Tiempo por petición | 30–60 s | 0.055–0.25 s |
| Errores bajo carga | 504 Gateway Timeout recurrentes | 0 errores en 100 peticiones |
| Advertencias PHP-FPM | `pm.max_children (5)` alcanzado | Sin advertencias (10 workers) |

**Causas identificadas:** (1) ausencia de OPcache, que obligaba a recompilar ~11 000 archivos PHP en cada petición sobre un volumen Docker montado desde Windows; (2) proyecto ubicado en carpeta sincronizada por OneDrive, que bloqueaba y ralentizaba el acceso a archivos; (3) solo 5 procesos PHP-FPM disponibles.

**Soluciones aplicadas:** instalación y configuración de OPcache en la imagen Docker, aumento de `pm.max_children` a 10, y reubicación del proyecto fuera de OneDrive. Mejora medida: **más de 100× en tiempo de respuesta**.

## 5. Herramientas de apoyo utilizadas

- **PHPUnit** — pruebas funcionales automatizadas
- **curl + bash** — script de prueba de carga secuencial y concurrente
- **psql** — verificación de esquema, trigger y vistas SQL
- **Chrome DevTools** — verificación de cabeceras de seguridad y códigos de respuesta (403/429)
- **Logs de Nginx y PHP-FPM** — diagnóstico de rendimiento
- **Git/GitHub** — control de versiones y trazabilidad de cambios

## 6. Métricas e indicadores del sistema

- Vistas SQL de monitoreo: `vista_incidencias_por_estado` (distribución porcentual), `vista_incidencias_por_tipo_ciudad` y `vista_tiempo_resolucion` (promedio/mín/máx de horas de resolución), alimentada por el trigger `trg_fecha_resolucion` que registra automáticamente la fecha de resolución.
- Tiempo promedio de resolución registrado: 229.30 horas (con los datos actuales de prueba).
- Rendimiento del sistema: 23.47 peticiones/segundo bajo concurrencia, 100 % de éxito.

## 7. Incidencias detectadas durante el desarrollo y soluciones

| Problema | Causa | Solución |
|---|---|---|
| Las notificaciones no se generaban | Columna `incidencia_id` ausente (esquema desincronizado de las migraciones) | Migración correctiva idempotente con `Schema::hasColumn` |
| Error SQL al asignar responsables | Columna `rol` ausente en `asignaciones` | Migración correctiva |
| Errores 500/504 generalizados | `APP_KEY` vacía en `.env`; workers PHP-FPM saturados por reintentos | `php artisan key:generate` + reinicio de servicios |
| Editar una incidencia borraba sus coordenadas | El formulario de edición no enviaba lat/lng y el update las sobrescribía con null | Campos y mapa agregados al formulario de edición |
| Rendimiento degradado (peticiones de 30–60 s) | Sin OPcache + volumen Docker sobre carpeta OneDrive + 5 workers | OPcache, reubicación del proyecto y 10 workers (ver sección 4) |