@extends('layouts.app')

@section('title', 'Directorio de Emergencias')

@section('styles')
<style>
    .tarjeta-emergencia {
        border-radius: 10px;
        border-left: 5px solid var(--color-e, #6c757d);
        transition: transform 0.15s;
    }
    .tarjeta-emergencia:hover { transform: translateY(-3px); }
    .icono-emergencia {
        width: 56px; height: 56px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; color: #fff; background: var(--color-e, #6c757d);
        flex-shrink: 0;
    }
    .btn-llamar { min-width: 120px; }
    .subtitulo-seccion {
        font-size: 1.05rem;
        font-weight: 700;
        margin: 28px 0 14px;
        padding-bottom: 6px;
        border-bottom: 2px solid rgba(255,255,255,0.12);
    }
    .btn-correo {
        font-size: 0.78rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <h1 class="mb-2">Directorio de Emergencias</h1>
    <p class="text-muted mb-4">
        Si la incidencia representa un riesgo inmediato para la vida o la seguridad,
        comunícate directamente con las autoridades. Este sistema no reemplaza a los servicios de emergencia.
    </p>

    <!-- ===== Servicios de emergencia nacionales ===== -->
    <div class="subtitulo-seccion">
        <i class="fas fa-ambulance mr-2"></i>Servicios de emergencia
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
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card tarjeta-emergencia h-100" style="--color-e: {{ $c['color'] }};">
                    <div class="card-body d-flex align-items-center">
                        <div class="icono-emergencia mr-3">
                            <i class="fas {{ $c['icono'] }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1" style="font-size: 1rem;">{{ $c['nombre'] }}</h5>
                            <p class="text-muted mb-2" style="font-size: 0.82rem;">{{ $c['desc'] }}</p>
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

    <!-- ===== Gobiernos municipales de la provincia de Santa Elena ===== -->
    <div class="subtitulo-seccion">
        <i class="fas fa-landmark mr-2"></i>Municipios de la provincia de Santa Elena
    </div>
    <p class="text-muted mb-3" style="font-size: 0.85rem;">
        Para incidencias de servicios públicos (vías, alumbrado, alcantarillado, recolección) comunícate con el GAD del cantón correspondiente.
    </p>

    <div class="row">
        @php
            $municipios = [
                ['nombre' => 'GAD Municipal de Santa Elena', 'desc' => 'Cabecera cantonal y provincial. Av. 18 de Agosto y Sucre, Santa Elena.', 'tel' => '042597700', 'telMostrar' => '(04) 259-7700', 'correo' => 'alcaldiaciudadanasantaelena@gmail.com', 'icono' => 'fa-city', 'color' => '#20c997'],
                ['nombre' => 'GAD Municipal de Salinas', 'desc' => 'Av. 10 de Agosto entre Estados Unidos y Av. 22 de Diciembre, Salinas.', 'tel' => '042930004', 'telMostrar' => '(04) 293-0004', 'correo' => 'alcaldia@salinas.gob.ec', 'icono' => 'fa-city', 'color' => '#0dcaf0'],
                ['nombre' => 'GAD Municipal de La Libertad', 'desc' => 'Av. Eleodoro Solórzano y Calle 11, Barrio 28 de Mayo, La Libertad.', 'tel' => '043711955', 'telMostrar' => '(04) 371-1955', 'correo' => 'alcaldia@lalibertad.gob.ec', 'icono' => 'fa-city', 'color' => '#6f42c1'],
            ];
        @endphp

        @foreach($municipios as $c)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card tarjeta-emergencia h-100" style="--color-e: {{ $c['color'] }};">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="icono-emergencia mr-3">
                                <i class="fas {{ $c['icono'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1" style="font-size: 1rem;">{{ $c['nombre'] }}</h5>
                                <p class="text-muted mb-0" style="font-size: 0.8rem;">{{ $c['desc'] }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap" style="gap: 6px;">
                            <a href="tel:{{ $c['tel'] }}" class="btn btn-sm btn-llamar"
                               style="background: {{ $c['color'] }}; color: #fff;">
                                <i class="fas fa-phone mr-1"></i> {{ $c['telMostrar'] }}
                            </a>
                            <a href="mailto:{{ $c['correo'] }}" class="btn btn-sm btn-outline-light btn-correo">
                                <i class="fas fa-envelope mr-1"></i> Correo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection