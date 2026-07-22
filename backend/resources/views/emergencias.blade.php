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

        .btn-llamar {
            min-width: 0;
            width: 100%;
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

    <div class="row">
        @php
            $contactos = [
                ['nombre' => 'ECU 911 — Emergencias', 'desc' => 'Central integrada de emergencias del Ecuador (policía, salud, incendios, rescate).', 'tel' => '911', 'icono' => 'fa-phone-volume', 'color' => '#dc3545'],
                ['nombre' => 'Policía Nacional', 'desc' => 'Delitos en curso, seguridad ciudadana y denuncias urgentes.', 'tel' => '101', 'icono' => 'fa-shield-alt', 'color' => '#0d6efd'],
                ['nombre' => 'Cuerpo de Bomberos', 'desc' => 'Incendios, rescates y materiales peligrosos.', 'tel' => '102', 'icono' => 'fa-fire-extinguisher', 'color' => '#fd7e14'],
                ['nombre' => 'Cruz Roja Ecuatoriana', 'desc' => 'Atención prehospitalaria y emergencias médicas.', 'tel' => '131', 'icono' => 'fa-briefcase-medical', 'color' => '#e83e8c'],
                ['nombre' => 'CNEL — Emergencias eléctricas', 'desc' => 'Postes caídos, cables sueltos y cortes de energía.', 'tel' => '136', 'icono' => 'fa-bolt', 'color' => '#ffc107'],
                ['nombre' => 'GAD Municipal La Libertad', 'desc' => 'Servicios públicos, vías y alumbrado del cantón.', 'tel' => '042775634', 'icono' => 'fa-city', 'color' => '#20c997'],
            ];
        @endphp

        @foreach($contactos as $c)
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

</div>
@endsection