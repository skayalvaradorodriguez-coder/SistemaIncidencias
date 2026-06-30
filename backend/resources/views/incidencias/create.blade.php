@extends('layouts.app')

@section('title', 'Nueva Incidencia')

@section('content')

<div class="container-fluid">

    <h1 class="mb-4">Nueva Incidencia</h1>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registrar nueva incidencia</h3>
        </div>

        <div class="card-body">

            <form>

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" class="form-control" placeholder="Ej: Bache en avenida principal">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea class="form-control" rows="3" placeholder="Describa la incidencia"></textarea>
                </div>

                <div class="form-group">
                    <label>País</label>
                    <select id="pais_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($paises as $pais)
                            <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Provincia</label>
                    <select id="provincia_id" class="form-control">
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ciudad</label>
                    <select id="ciudad_id" class="form-control">
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipo de Incidencia</label>
                    <select id="tipo_incidencia_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Subtipo de Incidencia</label>
                    <select id="subtipo_incidencia_id" class="form-control">
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Prioridad</label>
                    <select class="form-control">
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" class="form-control" placeholder="Dirección aproximada">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Latitud</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Longitud</label>
                            <input type="text" class="form-control">
                        </div>
                    </div>
                </div>

                <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="button" class="btn btn-primary">
                    Guardar Incidencia
                </button>

            </form>

        </div>
    </div>

</div>

@endsection

@section('scripts')

<script>
    const paises = @json($paises);
    const tipos = @json($tipos);

    const paisSelect = document.getElementById('pais_id');
    const provinciaSelect = document.getElementById('provincia_id');
    const ciudadSelect = document.getElementById('ciudad_id');

    const tipoSelect = document.getElementById('tipo_incidencia_id');
    const subtipoSelect = document.getElementById('subtipo_incidencia_id');

    paisSelect.addEventListener('change', function () {
        const paisId = this.value;

        provinciaSelect.innerHTML = '<option value="">Seleccione...</option>';
        ciudadSelect.innerHTML = '<option value="">Seleccione...</option>';

        const pais = paises.find(p => p.id == paisId);

        if (pais && pais.provincias) {
            pais.provincias.forEach(provincia => {
                provinciaSelect.innerHTML += `
                    <option value="${provincia.id}">
                        ${provincia.nombre}
                    </option>
                `;
            });
        }
    });

    provinciaSelect.addEventListener('change', function () {
        const paisId = paisSelect.value;
        const provinciaId = this.value;

        ciudadSelect.innerHTML = '<option value="">Seleccione...</option>';

        const pais = paises.find(p => p.id == paisId);

        if (pais && pais.provincias) {
            const provincia = pais.provincias.find(pr => pr.id == provinciaId);

            if (provincia && provincia.ciudades) {
                provincia.ciudades.forEach(ciudad => {
                    ciudadSelect.innerHTML += `
                        <option value="${ciudad.id}">
                            ${ciudad.nombre}
                        </option>
                    `;
                });
            }
        }
    });

    tipoSelect.addEventListener('change', function () {
        const tipoId = this.value;

        subtipoSelect.innerHTML = '<option value="">Seleccione...</option>';

        const tipo = tipos.find(t => t.id == tipoId);

        if (tipo && tipo.subtipos) {
            tipo.subtipos.forEach(subtipo => {
                subtipoSelect.innerHTML += `
                    <option value="${subtipo.id}">
                        ${subtipo.nombre}
                    </option>
                `;
            });
        }
    });
</script>

@endsection