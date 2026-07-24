@extends('layouts.app')

@section('title', 'Directorio de Emergencias')

@section('styles')
<style>
    .pagina-header {
        background: linear-gradient(135deg, rgba(30,58,138,0.35) 0%, rgba(29,78,216,0.25) 45%, rgba(14,165,233,0.18) 100%);
        border: 1px solid var(--border-subtle);
        border-radius: 14px;
        padding: 18px 22px;
    }

    .tarjeta-emergencia {
        border-radius: 12px;
        border-left: 5px solid var(--color-e, #6c757d);
        transition: transform 0.15s, box-shadow 0.15s;
    }

    .tarjeta-emergencia:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    }

    .icono-emergencia {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #fff;
        background: var(--color-e, #6c757d);
        flex-shrink: 0;
        box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    }

    .btn-llamar {
        min-width: 130px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
    }

    .btn-correo {
        font-size: 0.78rem;
        border-radius: 8px;
    }

    .seccion-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin: 28px 0 6px;
        padding-bottom: 6px;
        border-bottom: 2px solid rgba(255,255,255,0.12);
    }

    .subtitulo-seccion {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
    }

    .selector-provincia {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .selector-provincia label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0;
        white-space: nowrap;
    }

    .selector-provincia select {
        min-width: 210px;
    }

    .tarjeta-sin-datos {
        border-radius: 12px;
        border: 1px dashed var(--border-subtle);
        padding: 26px 20px;
        text-align: center;
        color: var(--text-muted);
    }

    .tarjeta-sin-datos i {
        font-size: 1.8rem;
        margin-bottom: 10px;
        opacity: 0.6;
    }

    .btn-ghost {
        background: rgba(148,163,184,0.12);
        border: 1px solid var(--border-subtle);
        color: var(--text-main);
    }

    .btn-ghost:hover,
    .btn-ghost:focus {
        background: rgba(148,163,184,0.22);
        color: var(--text-main);
        border-color: var(--border-subtle);
    }

    /* =========================================================
       Responsive
       ========================================================= */
    @media (max-width: 767.98px) {

        .pagina-header {
            padding: 16px;
        }

        .pagina-header h1 {
            font-size: 1.35rem;
        }

        .icono-emergencia {
            width: 46px;
            height: 46px;
            font-size: 1.15rem;
        }

        .tarjeta-emergencia .card-body {
            padding: 14px;
        }

        .btn-llamar,
        .btn-correo {
            min-width: 0;
            width: 100%;
            justify-content: center;
        }

        .seccion-header-row {
            flex-direction: column;
            align-items: flex-start;
            margin: 20px 0 10px;
        }

        .selector-provincia {
            width: 100%;
        }

        .selector-provincia select {
            flex-grow: 1;
            min-width: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <div class="pagina-header mb-4">
        <h1 class="mb-1"><i class="fas fa-phone-alt mr-2"></i>Directorio de Emergencias</h1>
        <span style="color:var(--text-muted); font-size:0.88rem;">
            Si la incidencia representa un riesgo inmediato para la vida o la seguridad,
            comunícate directamente con las autoridades. Este sistema no reemplaza a los servicios de emergencia.
        </span>
    </div>

    <!-- ===== Servicios de emergencia nacionales ===== -->
    <div class="seccion-header-row">
        <div class="subtitulo-seccion">
            <i class="fas fa-ambulance mr-2"></i>Servicios de emergencia
        </div>
    </div>

    <div class="row">
        @php
            $emergencias = [
                ['nombre' => 'ECU 911 — Emergencias', 'desc' => 'Central integrada de emergencias del Ecuador (policía, salud, incendios, rescate).', 'tel' => '911', 'icono' => 'fa-phone-volume', 'color' => '#dc3545'],
                ['nombre' => 'Policía Nacional', 'desc' => 'Delitos en curso, seguridad ciudadana y denuncias urgentes.', 'tel' => '101', 'icono' => 'fa-shield-alt', 'color' => '#0d6efd'],
                ['nombre' => 'Cuerpo de Bomberos', 'desc' => 'Incendios, rescates y materiales peligrosos.', 'tel' => '102', 'icono' => 'fa-fire-extinguisher', 'color' => '#fd7e14'],
                ['nombre' => 'Cruz Roja Ecuatoriana', 'desc' => 'Atención prehospitalaria y emergencias médicas.', 'tel' => '131', 'icono' => 'fa-briefcase-medical', 'color' => '#e83e8c'],
                ['nombre' => 'CNEL — Emergencias eléctricas', 'desc' => 'Postes caídos, cables sueltos y cortes de energía.', 'tel' => '136', 'icono' => 'fa-bolt', 'color' => '#ffc107'],
            ];
        @endphp

        @foreach($emergencias as $c)
            <div class="col-sm-6 col-lg-4 mb-3">
                <div class="card tarjeta-emergencia h-100" style="--color-e: {{ $c['color'] }};">
                    <div class="card-body d-flex align-items-center">
                        <div class="icono-emergencia mr-3">
                            <i class="fas {{ $c['icono'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" style="font-size: 1rem;">{{ $c['nombre'] }}</h5>
                            <p class="mb-2" style="font-size: 0.82rem; color: var(--text-muted);">{{ $c['desc'] }}</p>
                            <a href="tel:{{ $c['tel'] }}" class="btn btn-sm btn-llamar"
                               style="background: {{ $c['color'] }}; color: #fff;">
                                <i class="fas fa-phone mr-1"></i> {{ $c['tel'] }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ===== Municipios (con selector de provincia) ===== -->
    <div class="seccion-header-row">
        <div class="subtitulo-seccion">
            <i class="fas fa-landmark mr-2"></i>Municipios
        </div>

        <div class="selector-provincia">
            <label for="selectProvinciaMunicipio"><i class="fas fa-map-marker-alt"></i> Provincia:</label>
            <select id="selectProvinciaMunicipio" class="form-control form-control-sm"></select>
        </div>
    </div>

    <p class="mb-3" style="font-size: 0.85rem; color: var(--text-muted);">
        Para incidencias de servicios públicos (vías, alumbrado, alcantarillado, recolección) comunícate con el GAD municipal de la provincia seleccionada.
    </p>

    <div class="row" id="contenedorMunicipios"></div>

</div>
@endsection

@section('scripts')
<script>
    // ============================================================
    // Contactos verificados (solo se listan provincias confirmadas
    // manualmente, para no exponer datos de contacto no revisados)
    // ============================================================
    const municipiosPorProvincia = {
        'Santa Elena': [
            { nombre: 'GAD Municipal de Santa Elena', desc: 'Cabecera cantonal y provincial. Av. 18 de Agosto y Sucre, Santa Elena.', tel: '042597700', telMostrar: '(04) 259-7700', correo: 'alcaldiaciudadanasantaelena@gmail.com', color: '#20c997' },
            { nombre: 'GAD Municipal de Salinas', desc: 'Av. 10 de Agosto entre Estados Unidos y Av. 22 de Diciembre, Salinas.', tel: '042930004', telMostrar: '(04) 293-0004', correo: 'alcaldia@salinas.gob.ec', color: '#0dcaf0' },
            { nombre: 'GAD Municipal de La Libertad', desc: 'Av. Eleodoro Solórzano y Calle 11, Barrio 28 de Mayo, La Libertad.', tel: '043711955', telMostrar: '(04) 371-1955', correo: 'alcaldia@lalibertad.gob.ec', color: '#6f42c1' },
        ],
    };

    // ============================================================
    // Sitios web OFICIALES (.gob.ec) verificados uno por uno para
    // cada capital de provincia. No se listan teléfonos/correos
    // aquí porque no se pudieron confirmar de forma fiable; se
    // enlaza directo a la fuente oficial en su lugar.
    // ============================================================
    const sitioOficialPorProvincia = {
        'Esmeraldas': { ciudad: 'Esmeraldas', url: 'https://esmeraldas.gob.ec' },
        'Manabí': { ciudad: 'Portoviejo', url: 'https://www.portoviejo.gob.ec' },
        'Guayas': { ciudad: 'Guayaquil', url: 'https://guayaquil.gob.ec' },
        'Los Ríos': { ciudad: 'Babahoyo', url: 'https://www.babahoyo.gob.ec' },
        'El Oro': { ciudad: 'Machala', url: 'https://www.machala.gob.ec' },
        'Santo Domingo de los Tsáchilas': { ciudad: 'Santo Domingo', url: 'https://www.santodomingo.gob.ec' },
        'Carchi': { ciudad: 'Tulcán', url: 'https://www.gmtulcan.gob.ec' },
        'Imbabura': { ciudad: 'Ibarra', url: 'https://www.ibarra.gob.ec' },
        'Pichincha': { ciudad: 'Quito', url: 'https://www.quito.gob.ec' },
        'Cotopaxi': { ciudad: 'Latacunga', url: 'https://www.latacunga.gob.ec' },
        'Tungurahua': { ciudad: 'Ambato', url: 'https://ambato.gob.ec' },
        'Bolívar': { ciudad: 'Guaranda', url: 'https://www.guaranda.gob.ec' },
        'Chimborazo': { ciudad: 'Riobamba', url: 'https://www.gadmriobamba.gob.ec' },
        'Cañar': { ciudad: 'Azogues', url: 'https://www.azogues.gob.ec' },
        'Azuay': { ciudad: 'Cuenca', url: 'https://www.cuenca.gob.ec' },
        'Loja': { ciudad: 'Loja', url: 'https://www.loja.gob.ec' },
        'Sucumbíos': { ciudad: 'Nueva Loja (Lago Agrio)', url: 'https://www.lagoagrio.gob.ec' },
        'Napo': { ciudad: 'Tena', url: 'https://tena.gob.ec' },
        'Orellana': { ciudad: 'Puerto Francisco de Orellana (Coca)', url: 'https://www.orellana.gob.ec' },
        'Pastaza': { ciudad: 'Puyo', url: 'https://www.puyo.gob.ec' },
        'Morona Santiago': { ciudad: 'Macas', url: 'https://www.morona.gob.ec' },
        'Zamora Chinchipe': { ciudad: 'Zamora', url: 'https://zamora.gob.ec' },
        'Galápagos': { ciudad: 'Puerto Baquerizo Moreno', url: 'https://gadmsc.gob.ec' },
    };

    // Todas las provincias del sistema (mismas 24 del seeder de ubicación)
    const todasLasProvincias = [
        'Esmeraldas', 'Manabí', 'Santa Elena', 'Guayas', 'Los Ríos', 'El Oro', 'Santo Domingo de los Tsáchilas',
        'Carchi', 'Imbabura', 'Pichincha', 'Cotopaxi', 'Tungurahua', 'Bolívar', 'Chimborazo', 'Cañar', 'Azuay', 'Loja',
        'Sucumbíos', 'Napo', 'Orellana', 'Pastaza', 'Morona Santiago', 'Zamora Chinchipe',
        'Galápagos',
    ];

    const select = document.getElementById('selectProvinciaMunicipio');
    const contenedor = document.getElementById('contenedorMunicipios');

    todasLasProvincias.forEach(prov => {
        const option = document.createElement('option');
        option.value = prov;
        let marca = '';
        if (municipiosPorProvincia[prov]) {
            marca = ' ✓';
        } else if (sitioOficialPorProvincia[prov]) {
            marca = ' 🔗';
        }
        option.textContent = prov + marca;
        select.appendChild(option);
    });

    select.value = 'Santa Elena';

    function pintarMunicipios(provincia) {
        contenedor.innerHTML = '';

        // Nivel 1: contacto completo verificado (teléfono + correo)
        const lista = municipiosPorProvincia[provincia];

        if (lista && lista.length > 0) {
            lista.forEach(c => {
                contenedor.innerHTML += `
                    <div class="col-sm-6 col-lg-4 mb-3">
                        <div class="card tarjeta-emergencia h-100" style="--color-e: ${c.color};">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="icono-emergencia mr-3">
                                        <i class="fas fa-city"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1" style="font-size: 1rem;">${c.nombre}</h5>
                                        <p class="mb-0" style="font-size: 0.8rem; color: var(--text-muted);">${c.desc}</p>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap" style="gap: 6px;">
                                    <a href="tel:${c.tel}" class="btn btn-sm btn-llamar" style="background: ${c.color}; color: #fff;">
                                        <i class="fas fa-phone mr-1"></i> ${c.telMostrar}
                                    </a>
                                    <a href="mailto:${c.correo}" class="btn btn-sm btn-outline-light btn-correo">
                                        <i class="fas fa-envelope mr-1"></i> Correo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            return;
        }

        // Nivel 2: sin teléfono/correo confirmado, pero sí sitio oficial
        // .gob.ec verificado uno por uno -> se enlaza directo a la fuente,
        // sin inventar un dato de contacto.
        const sitio = sitioOficialPorProvincia[provincia];

        if (sitio) {
            contenedor.innerHTML = `
                <div class="col-sm-6 col-lg-4 mb-3">
                    <div class="card tarjeta-emergencia h-100" style="--color-e: #6c757d;">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icono-emergencia mr-3">
                                    <i class="fas fa-landmark"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1" style="font-size: 1rem;">GAD Municipal de ${sitio.ciudad}</h5>
                                    <p class="mb-0" style="font-size: 0.8rem; color: var(--text-muted);">
                                        Teléfono y correo no confirmados. Sitio oficial verificado disponible.
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap" style="gap: 6px;">
                                <a href="${sitio.url}" target="_blank" rel="noopener" class="btn btn-sm btn-llamar" style="background: #6c757d; color: #fff;">
                                    <i class="fas fa-external-link-alt mr-1"></i> Sitio oficial
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        // Nivel 3: provincia sin contacto verificado ni sitio confirmado
        // todavía: no se inventa el dato, se ofrece un acceso directo
        // a una búsqueda de la fuente oficial.
        const busqueda = encodeURIComponent('GAD Municipal de ' + provincia + ' teléfono contacto oficial');

        contenedor.innerHTML = `
            <div class="col-12">
                <div class="tarjeta-sin-datos">
                    <i class="fas fa-map-signs d-block"></i>
                    Todavía no tenemos un contacto verificado para <strong>${provincia}</strong>.
                    <div class="mt-3">
                        <a href="https://www.google.com/search?q=${busqueda}" target="_blank" rel="noopener"
                           class="btn btn-sm btn-ghost">
                            <i class="fas fa-search mr-1"></i> Buscar GAD Municipal de ${provincia}
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    select.addEventListener('change', () => pintarMunicipios(select.value));

    pintarMunicipios('Santa Elena');
</script>
@endsection