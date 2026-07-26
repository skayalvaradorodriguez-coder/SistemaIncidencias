@extends('layouts.app')

@section('title', 'Centro de Ayuda')

@section('styles')
<style>
    .pagina-header {
        background: linear-gradient(135deg, rgba(10,17,40,0.35) 0%, rgba(22,35,63,0.25) 45%, rgba(201,169,97,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 22px;
    }

    .buscador-ayuda {
        max-width: 460px;
    }

    .buscador-ayuda .input-group-text {
        background: var(--input-bg);
        border: 1px solid var(--border-subtle);
        color: var(--text-muted);
    }

    .buscador-ayuda input {
        border-left: none;
    }

    /* ===== Accesos rápidos ===== */
    .acceso-rapido {
        display: block;
        text-decoration: none;
        border-radius: 12px;
        border: 1px solid var(--border-subtle);
        background: var(--bg-card);
        padding: 18px;
        height: 100%;
        transition: transform 0.15s, box-shadow 0.15s, border-color 0.15s;
    }

    .acceso-rapido:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.28);
        border-color: rgba(201,169,97,0.5);
        text-decoration: none;
    }

    .acceso-rapido .icono-acceso {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: #fff;
        background: linear-gradient(135deg, #16233F, #0A1128);
        margin-bottom: 12px;
    }

    .acceso-rapido h5 {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 4px;
    }

    .acceso-rapido p {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* ===== Encabezados de sección ===== */
    .seccion-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin: 34px 0 14px;
        padding-bottom: 8px;
        border-bottom: 2px solid rgba(255,255,255,0.12);
    }

    .subtitulo-seccion {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
    }

    /* ===== Pasos (stepper) ===== */
    .paso-item {
        display: flex;
        gap: 16px;
        padding: 16px 4px;
        border-bottom: 1px dashed var(--border-subtle);
    }

    .paso-item:last-child {
        border-bottom: none;
    }

    .paso-numero {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #E3CD8F, #C9A961);
        color: #0A1128;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 10px rgba(0,0,0,0.25);
    }

    .paso-titulo {
        font-weight: 700;
        font-size: 0.95rem;
        margin-bottom: 3px;
        color: var(--text-main);
    }

    .paso-texto {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    .paso-opcional {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        background: rgba(148,163,184,0.18);
        color: var(--text-muted);
        border-radius: 20px;
        padding: 1px 9px;
        margin-left: 8px;
        vertical-align: middle;
    }

    /* ===== Tarjetas de sección del sistema ===== */
    .tarjeta-seccion {
        border-radius: 12px;
        border-left: 5px solid var(--color-s, #6c757d);
        height: 100%;
    }

    .icono-seccion {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        color: #fff;
        background: var(--color-s, #6c757d);
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.28);
    }

    .badge-rol-restringido {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        background: rgba(201,169,97,0.18);
        color: #C9A961;
        border: 1px solid rgba(201,169,97,0.4);
        border-radius: 20px;
        padding: 2px 9px;
        white-space: nowrap;
    }

    .badge-nuevo {
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        background: linear-gradient(135deg, #E3CD8F, #C9A961);
        color: #0A1128;
        border-radius: 20px;
        padding: 2px 9px;
        margin-left: 6px;
        vertical-align: middle;
    }

    .aviso-correo {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        background: rgba(14,165,233,0.08);
        border: 1px solid rgba(14,165,233,0.3);
        border-left: 4px solid #0ea5e9;
        border-radius: 10px;
        padding: 14px 16px;
        margin: 6px 0 18px 0;
    }

    .aviso-correo .icono-aviso {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #0ea5e9;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.85rem;
    }

    .aviso-correo p {
        margin: 0;
        font-size: 0.83rem;
        color: var(--text-muted);
        line-height: 1.5;
    }

    .aviso-correo strong {
        color: var(--text-main);
    }

    .solo-gestion-ayuda { display: none; }

    /* ===== FAQ (accordion) ===== */
    .faq-item {
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .faq-pregunta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        cursor: pointer;
        padding: 14px 16px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
    }

    .faq-pregunta .fa-chevron-down {
        transition: transform 0.2s;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    .faq-pregunta[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }

    .faq-respuesta {
        padding: 0 16px 16px 16px;
        font-size: 0.85rem;
        color: var(--text-muted);
        line-height: 1.55;
    }

    .faq-respuesta strong {
        color: var(--text-main);
    }

    .badge-estado-ayuda {
        display: inline-block;
        font-size: 0.72rem;
        font-weight: 600;
        color: #fff;
        border-radius: 20px;
        padding: 2px 10px;
        margin: 0 2px;
    }

    #sinResultadosFaq {
        display: none;
        text-align: center;
        color: var(--text-muted);
        padding: 24px;
    }

    /* ===== CTA final ===== */
    .cta-soporte {
        background: var(--brand-gradient);
        border: none;
        border-radius: 14px;
        padding: 30px 24px;
        text-align: center;
        margin-top: 34px;
        box-shadow: 0 10px 26px rgba(0,0,0,0.28);
        position: relative;
        overflow: hidden;
    }

    /* Brillo sutil decorativo, igual espíritu que el header de login */
    .cta-soporte::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(201,169,97,0.35) 0%, transparent 70%);
        pointer-events: none;
    }

    .cta-soporte .cta-icono {
        font-size: 1.9rem;
        color: #E3CD8F;
        margin-bottom: 10px;
        display: inline-block;
        position: relative;
    }

    .cta-soporte h4 {
        color: #ffffff !important;
        font-weight: 700;
        margin-bottom: 8px;
        position: relative;
    }

    .cta-soporte .cta-texto {
        color: rgba(255,255,255,0.78) !important;
        font-size: 0.88rem;
        max-width: 620px;
        margin: 0 auto 20px;
        position: relative;
    }

    .cta-soporte .btn-ghost {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.35);
        color: #ffffff;
    }

    .cta-soporte .btn-ghost:hover,
    .cta-soporte .btn-ghost:focus {
        background: rgba(255,255,255,0.22);
        border-color: rgba(255,255,255,0.5);
        color: #ffffff;
    }

    @media (max-width: 767.98px) {
        .pagina-header { padding: 16px; }
        .pagina-header h1 { font-size: 1.35rem; }
        .buscador-ayuda { max-width: 100%; }
        .seccion-header-row { margin: 26px 0 12px; }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="pagina-header mb-4">
        <h1 class="mb-1"><i class="fas fa-life-ring mr-2"></i>Centro de Ayuda</h1>
        <p class="mb-3" style="color:var(--text-muted); font-size:0.9rem;">
            Todo lo que necesitas para reportar, dar seguimiento y entender cada parte del sistema.
        </p>

        <div class="input-group buscador-ayuda">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" id="buscadorFaq" class="form-control" placeholder="Busca una pregunta, por ejemplo: contraseña, estado, PDF...">
        </div>
    </div>

    <!-- ===== Accesos rápidos ===== -->
    <div class="row">
        <div class="col-6 col-md-3 mb-3">
            <a href="#guia-reportar" class="acceso-rapido">
                <div class="icono-acceso"><i class="fas fa-plus"></i></div>
                <h5>Crear una incidencia</h5>
                <p>Guía paso a paso para reportar.</p>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <a href="#guia-seguimiento" class="acceso-rapido">
                <div class="icono-acceso"><i class="fas fa-file-pdf"></i></div>
                <h5>Seguimiento y PDF</h5>
                <p>Revisa el avance y descarga tu reporte.</p>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <a href="#secciones" class="acceso-rapido">
                <div class="icono-acceso"><i class="fas fa-th-large"></i></div>
                <h5>Secciones del sistema</h5>
                <p>Qué encuentras en cada menú.</p>
            </a>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <a href="#faq" class="acceso-rapido">
                <div class="icono-acceso"><i class="fas fa-question"></i></div>
                <h5>Preguntas frecuentes</h5>
                <p>Dudas comunes resueltas.</p>
            </a>
        </div>
    </div>

    <!-- ===== Guía: cómo reportar una incidencia ===== -->
    <div class="seccion-header-row" id="guia-reportar">
        <div class="subtitulo-seccion"><i class="fas fa-clipboard-list mr-2"></i>Cómo crear una incidencia</div>
        <a href="{{ route('incidencias.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-1"></i>Reportar ahora
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="paso-item">
                <div class="paso-numero">1</div>
                <div>
                    <div class="paso-titulo">Título y descripción</div>
                    <p class="paso-texto">Resume el problema en pocas palabras (ej. "Bache en avenida principal") y descríbelo con el mayor detalle posible.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">2</div>
                <div>
                    <div class="paso-titulo">Ubicación administrativa</div>
                    <p class="paso-texto">Selecciona país, provincia y ciudad. Esta información permite dirigir tu incidencia al equipo correcto.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">3</div>
                <div>
                    <div class="paso-titulo">Tipo, subtipo y prioridad</div>
                    <p class="paso-texto">Elige la categoría que mejor describe el caso (vías, alumbrado, seguridad, etc.) y qué tan urgente lo consideras: Baja, Media, Alta o Crítica.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">4</div>
                <div>
                    <div class="paso-titulo">Dirección y fotografía <span class="paso-opcional">Opcional</span></div>
                    <p class="paso-texto">Agrega una dirección de referencia y, si puedes, una foto (JPG, PNG o WEBP, máximo 4 MB). En el celular se abrirá la cámara automáticamente.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">5</div>
                <div>
                    <div class="paso-titulo">Ubicación en el mapa <span class="paso-opcional">Opcional</span></div>
                    <p class="paso-texto">Marca el punto exacto en el mapa haciendo clic, arrastra el marcador para ajustarlo, o usa el botón "Usar mi ubicación".</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">6</div>
                <div>
                    <div class="paso-titulo">Guardar</div>
                    <p class="paso-texto">Presiona <strong>"Guardar Incidencia"</strong>. Tu reporte quedará con estado <span class="badge-estado-ayuda" style="background:#ffc107; color:#0A1128;">Pendiente</span>, podrás verlo de inmediato en "Mis Reportes" y te llegará un <strong>correo de confirmación</strong> a tu bandeja de entrada.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===== Guía: seguimiento y PDF ===== -->
    <div class="seccion-header-row" id="guia-seguimiento">
        <div class="subtitulo-seccion"><i class="fas fa-route mr-2"></i>Seguimiento y descarga de reportes</div>
        <a href="{{ route('incidencias.mis') }}" class="btn btn-sm btn-ghost">
            <i class="fas fa-clipboard-list mr-1"></i>Ir a Mis Reportes
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="paso-item">
                <div class="paso-numero">1</div>
                <div>
                    <div class="paso-titulo">Entra a "Mis Reportes"</div>
                    <p class="paso-texto">Ahí verás únicamente las incidencias que tú has creado, cada una en una tarjeta con su barra de progreso.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">2</div>
                <div>
                    <div class="paso-titulo">Revisa el estado</div>
                    <p class="paso-texto">
                        La barra avanza según el estado actual:
                        <span class="badge-estado-ayuda" style="background:#ffc107; color:#0A1128;">Pendiente</span>
                        <span class="badge-estado-ayuda" style="background:#007bff;">En Proceso</span>
                        <span class="badge-estado-ayuda" style="background:#28a745;">Resuelto</span>
                        <span class="badge-estado-ayuda" style="background:#dc3545;">Rechazado</span>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <div class="aviso-correo">
        <div class="icono-aviso"><i class="fas fa-envelope"></i></div>
        <p>
            <strong>Notificaciones por correo <span class="badge-nuevo">Nuevo</span></strong><br>
            Además de la campana de notificaciones dentro del sistema, ahora también recibirás un <strong>correo electrónico</strong> cada vez que tu incidencia cambie de estado (al recibirla, al pasar a En Proceso, y al quedar Resuelta o Rechazada). Si es rechazada, el correo incluye el motivo que dejó el equipo. Solo te llegan avisos de tus propias incidencias.
        </p>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="paso-item">
                <div class="paso-numero">3</div>
                <div>
                    <div class="paso-titulo">Entra al detalle para conversar con el equipo</div>
                    <p class="paso-texto">Haz clic en "Ver detalle y seguimiento" para ver quién la está atendiendo y dejar comentarios de seguimiento.</p>
                </div>
            </div>

            <div class="paso-item">
                <div class="paso-numero">4</div>
                <div>
                    <div class="paso-titulo">Descarga tu reporte en PDF</div>
                    <p class="paso-texto">Con el botón <strong>"Descargar PDF"</strong>, en la parte superior de "Mis Reportes", generas un documento con el detalle de todas tus incidencias, listo para imprimir o enviar.</p>
                </div>
            </div>

        </div>
    </div>

    <!-- ===== Secciones del sistema ===== -->
    <div class="seccion-header-row" id="secciones">
        <div class="subtitulo-seccion"><i class="fas fa-th-large mr-2"></i>Qué encuentras en cada sección</div>
    </div>

    <div class="row">

        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card tarjeta-seccion h-100" style="--color-s:#0ea5e9;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-home"></i></div>
                    <div>
                        <h5 style="font-size:1rem;">Dashboard</h5>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Panorama general: totales por estado, mapa georreferenciado de incidencias (marcadores o mapa de calor), gráficos por estado y por tipo, y las incidencias más recientes.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card tarjeta-seccion h-100" style="--color-s:#f59e0b;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-exclamation-triangle"></i></div>
                    <div>
                        <h5 style="font-size:1rem;">Incidencias</h5>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Listado completo de las incidencias registradas en el sistema por todos los ciudadanos, con filtros para explorarlas. Desde aquí también puedes crear una nueva.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card tarjeta-seccion h-100" style="--color-s:#22c55e;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-clipboard-list"></i></div>
                    <div>
                        <h5 style="font-size:1rem;">Mis Reportes</h5>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Solo tus propias incidencias, con barra de progreso, detalle y seguimiento con comentarios, y descarga de tu historial en PDF.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3 solo-gestion-ayuda">
            <div class="card tarjeta-seccion h-100" style="--color-s:#6366f1;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-columns"></i></div>
                    <div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 style="font-size:1rem;">Tablero Kanban</h5>
                            <span class="badge-rol-restringido">Gestión</span>
                        </div>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Vista de columnas para que el equipo mueva las incidencias entre estados y las asigne a responsables.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3 solo-gestion-ayuda">
            <div class="card tarjeta-seccion h-100" style="--color-s:#e11d48;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-chart-pie"></i></div>
                    <div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 style="font-size:1rem;">Reportes</h5>
                            <span class="badge-rol-restringido">Gestión</span>
                        </div>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Estadísticas detalladas por tipo, estado, ciudad y provincia, exportables en PDF, para análisis y toma de decisiones.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3 solo-gestion-ayuda">
            <div class="card tarjeta-seccion h-100" style="--color-s:#0f766e;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 style="font-size:1rem;">Usuarios</h5>
                            <span class="badge-rol-restringido">Administrador</span>
                        </div>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Administración de las cuentas del sistema: creación, edición y asignación de roles.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card tarjeta-seccion h-100" style="--color-s:#dc3545;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-phone-alt"></i></div>
                    <div>
                        <h5 style="font-size:1rem;">Emergencias</h5>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Directorio de contactos oficiales (ECU 911, policía, bomberos, Cruz Roja, empresa eléctrica y municipios) para riesgos inmediatos que no pueden esperar.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4 mb-3">
            <div class="card tarjeta-seccion h-100" style="--color-s:#C9A961;">
                <div class="card-body d-flex">
                    <div class="icono-seccion mr-3"><i class="fas fa-user-circle"></i></div>
                    <div>
                        <h5 style="font-size:1rem;">Mi Perfil</h5>
                        <p class="mb-0" style="font-size:0.82rem; color:var(--text-muted);">
                            Actualiza tus datos personales y cambia tu contraseña cuando lo necesites.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ===== FAQ ===== -->
    <div class="seccion-header-row" id="faq">
        <div class="subtitulo-seccion"><i class="fas fa-question-circle mr-2"></i>Preguntas frecuentes</div>
    </div>

    <div id="listaFaq">

        @php
            $faqs = [
                [
                    'q' => '¿Qué significan los estados de una incidencia?',
                    'a' => '<span class="badge-estado-ayuda" style="background:#ffc107;color:#0A1128;">Pendiente</span> significa que fue recibida y aún no se ha revisado. <span class="badge-estado-ayuda" style="background:#007bff;">En Proceso</span> indica que el equipo ya la está atendiendo. <span class="badge-estado-ayuda" style="background:#28a745;">Resuelto</span> indica que se solucionó. <span class="badge-estado-ayuda" style="background:#dc3545;">Rechazado</span> indica que no procede (por ejemplo, datos insuficientes o fuera de competencia).',
                ],
                [
                    'q' => '¿Qué significa la prioridad que elijo al crear una incidencia?',
                    'a' => 'Es tu apreciación de qué tan urgente es el caso: <strong>Baja, Media, Alta o Crítica</strong>. Ayuda al equipo a organizar su trabajo, pero el estado real y el orden de atención los define el equipo de gestión según cada situación.',
                ],
                [
                    'q' => '¿Puedo editar una incidencia después de enviarla?',
                    'a' => 'Sí. Desde el detalle de tu reporte puedes actualizar título, descripción y ubicación. El cambio de estado (por ejemplo, marcarla como Resuelta) lo realiza el equipo de gestión, no el ciudadano.',
                ],
                [
                    'q' => '¿Cómo hablo con el equipo que está atendiendo mi caso?',
                    'a' => 'Entra al detalle de tu incidencia (botón "Ver detalle y seguimiento" desde Mis Reportes) y usa la sección de comentarios al final de la página para enviar y leer mensajes de seguimiento.',
                ],
                [
                    'q' => '¿Cómo sé si hay novedades en mis reportes?',
                    'a' => 'Revisa el ícono de la campana en la barra superior. Ahí aparecen tus notificaciones (cambios de estado, asignaciones, comentarios nuevos) con un contador de las no leídas.',
                ],
                [
                    'q' => '¿Me van a llegar correos por cada incidencia, incluso las de otros ciudadanos?',
                    'a' => 'No. Solo recibes correo por <strong>tus propias</strong> incidencias: uno de confirmación cuando la registras, y uno cada vez que cambia de estado (En Proceso, Resuelto o Rechazado). Es un canal adicional a la campana de notificaciones, no la reemplaza; ambos siguen funcionando en paralelo.',
                ],
                [
                    'q' => '¿Es obligatorio adjuntar foto o marcar el punto en el mapa?',
                    'a' => 'No, ambos son opcionales. Sin embargo, una foto clara y una ubicación exacta ayudan a que tu caso se atienda más rápido y con mejor precisión.',
                ],
                [
                    'q' => '¿Cómo descargo un PDF de mis reportes?',
                    'a' => 'Ve a <strong>Mis Reportes</strong> y presiona el botón rojo <strong>"Descargar PDF"</strong> en la parte superior. Se generará un documento con el detalle de todas tus incidencias.',
                ],
                [
                    'q' => '¿Qué hago si es una emergencia real, con riesgo para la vida?',
                    'a' => 'Este sistema no reemplaza a los servicios de emergencia. Ve a la sección <strong>Emergencias</strong> y comunícate directamente con ECU 911 o la entidad correspondiente.',
                ],
                [
                    'q' => '¿Cómo cambio mi contraseña o mis datos personales?',
                    'a' => 'Entra a <strong>Mi Perfil</strong> desde el menú lateral. Ahí puedes actualizar tu información y cambiar tu contraseña de forma segura.',
                ],
                [
                    'q' => 'Olvidé mi contraseña, ¿qué hago?',
                    'a' => 'En la pantalla de inicio de sesión, presiona <strong>"¿Olvidaste tu contraseña?"</strong> e ingresa tu correo para recibir instrucciones de recuperación.',
                ],
            ];
        @endphp

        @foreach($faqs as $i => $faq)
            <div class="card faq-item" data-texto="{{ strtolower($faq['q']) }}">
                <div class="faq-pregunta" data-toggle="collapse" data-target="#faqRespuesta{{ $i }}"
                     aria-expanded="false" role="button">
                    <span>{{ $faq['q'] }}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div id="faqRespuesta{{ $i }}" class="collapse">
                    <div class="faq-respuesta">{!! $faq['a'] !!}</div>
                </div>
            </div>
        @endforeach

        <div id="sinResultadosFaq">
            <i class="fas fa-search d-block mb-2" style="font-size:1.6rem; opacity:0.5;"></i>
            No encontramos preguntas con ese término.
        </div>

    </div>

    <!-- ===== CTA final ===== -->
    <div class="cta-soporte">
        <i class="fas fa-headset cta-icono"></i>
        <h4>¿No encontraste lo que buscabas?</h4>
        <p class="cta-texto">Si tu duda es sobre un reporte puntual, déjala como comentario dentro de esa incidencia. Si es una urgencia con riesgo real, usa el directorio de Emergencias.</p>
        <a href="{{ route('incidencias.mis') }}" class="btn btn-primary mr-2">
            <i class="fas fa-clipboard-list mr-1"></i>Ir a Mis Reportes
        </a>
        <a href="{{ route('emergencias') }}" class="btn btn-ghost">
            <i class="fas fa-phone-alt mr-1"></i>Ver Emergencias
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // ================== CONTROL POR ROL ==================
    (function () {
        const usuario = getUser();
        const rol = usuario && usuario.rol ? usuario.rol.nombre : null;

        if (rol === 'Administrador' || rol === 'Responsable') {
            document.querySelectorAll('.solo-gestion-ayuda').forEach(el => {
                el.style.display = 'block';
            });
        }
    })();

    // ================== BUSCADOR DE FAQ ==================
    const inputBuscador = document.getElementById('buscadorFaq');
    const itemsFaq = document.querySelectorAll('#listaFaq .faq-item');
    const sinResultados = document.getElementById('sinResultadosFaq');

    inputBuscador.addEventListener('input', function () {
        const termino = this.value.trim().toLowerCase();
        let visibles = 0;

        itemsFaq.forEach(item => {
            const coincide = item.dataset.texto.includes(termino);
            item.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });

        sinResultados.style.display = visibles === 0 ? 'block' : 'none';
    });

    // Desplazamiento suave a las secciones desde los accesos rápidos
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const destino = document.querySelector(this.getAttribute('href'));
            if (destino) {
                e.preventDefault();
                destino.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
@endsection