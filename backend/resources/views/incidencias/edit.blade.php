@extends('layouts.app')

@section('title', 'Editar Incidencia')

@section('content')

<div class="container-fluid">

    <h1 class="mb-4">Editar Incidencia #{{ $incidencia->id }}</h1>

    <div id="alertBox" class="alert d-none"></div>

    <div class="card">
        <div class="card-body">

            <form id="formEditar">

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="titulo" class="form-control" value="{{ $incidencia->titulo }}">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" class="form-control" rows="3">{{ $incidencia->descripcion }}</textarea>
                </div>

                <div class="form-group">
                    <label>País</label>
                    <select id="pais_id" class="form-control">
                        <option value="">Seleccione...</option>
                        @foreach($paises as $pais)
                            <option value="{{ $pais->id }}" {{ $incidencia->ciudad->provincia->pais->id == $pais->id ? 'selected' : '' }}>
                                {{ $pais->nombre }}
                            </option>
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
                            <option value="{{ $tipo->id }}" {{ $incidencia->tipo_incidencia_id == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
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
                    <select id="prioridad" class="form-control">
                        @foreach(['Baja', 'Media', 'Alta', 'Crítica'] as $p)
                            <option value="{{ $p }}" {{ $incidencia->prioridad == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" id="direccion" class="form-control" value="{{ $incidencia->direccion }}">
                </div>

                <a href="{{ route('incidencias.show', $incidencia->id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" id="btnActualizar" class="btn btn-primary">Guardar Cambios</button>

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

    const ciudadActualId = {{ $incidencia->ciudad_id }};
    const provinciaActualId = {{ $incidencia->ciudad->provincia_id }};
    const subtipoActualId = {{ $incidencia->subtipo_incidencia_id ?? 'null' }};

    function cargarProvincias(paisId, provinciaSeleccionada = null) {
        provinciaSelect.innerHTML = '<option value="">Seleccione...</option>';
        ciudadSelect.innerHTML = '<option value="">Seleccione...</option>';

        const pais = paises.find(p => p.id == paisId);
        if (pais && pais.provincias) {
            pais.provincias.forEach(provincia => {
                const sel = provincia.id == provinciaSeleccionada ? 'selected' : '';
                provinciaSelect.innerHTML += `<option value="${provincia.id}" ${sel}>${provincia.nombre}</option>`;
            });
        }
    }

    function cargarCiudades(paisId, provinciaId, ciudadSeleccionada = null) {
        ciudadSelect.innerHTML = '<option value="">Seleccione...</option>';

        const pais = paises.find(p => p.id == paisId);
        if (pais && pais.provincias) {
            const provincia = pais.provincias.find(pr => pr.id == provinciaId);
            if (provincia && provincia.ciudades) {
                provincia.ciudades.forEach(ciudad => {
                    const sel = ciudad.id == ciudadSeleccionada ? 'selected' : '';
                    ciudadSelect.innerHTML += `<option value="${ciudad.id}" ${sel}>${ciudad.nombre}</option>`;
                });
            }
        }
    }

    function cargarSubtipos(tipoId, subtipoSeleccionado = null) {
        subtipoSelect.innerHTML = '<option value="">Seleccione...</option>';

        const tipo = tipos.find(t => t.id == tipoId);
        if (tipo && tipo.subtipos) {
            tipo.subtipos.forEach(subtipo => {
                const sel = subtipo.id == subtipoSeleccionado ? 'selected' : '';
                subtipoSelect.innerHTML += `<option value="${subtipo.id}" ${sel}>${subtipo.nombre}</option>`;
            });
        }
    }

    // Precarga inicial
    if (paisSelect.value) {
        cargarProvincias(paisSelect.value, provinciaActualId);
        cargarCiudades(paisSelect.value, provinciaActualId, ciudadActualId);
    }
    if (tipoSelect.value) {
        cargarSubtipos(tipoSelect.value, subtipoActualId);
    }

    paisSelect.addEventListener('change', function () {
        cargarProvincias(this.value);
    });

    provinciaSelect.addEventListener('change', function () {
        cargarCiudades(paisSelect.value, this.value);
    });

    tipoSelect.addEventListener('change', function () {
        cargarSubtipos(this.value);
    });

    document.getElementById('formEditar').addEventListener('submit', async function (e) {
        e.preventDefault();

        const alertBox = document.getElementById('alertBox');
        alertBox.className = 'alert d-none';

        const payload = {
            titulo: document.getElementById('titulo').value,
            descripcion: document.getElementById('descripcion').value,
            ciudad_id: ciudadSelect.value,
            tipo_incidencia_id: tipoSelect.value,
            subtipo_incidencia_id: subtipoSelect.value,
            prioridad: document.getElementById('prioridad').value,
            direccion: document.getElementById('direccion').value,
        };

        try {
            const response = await authFetch('/api/incidencias/{{ $incidencia->id }}', {
                method: 'PUT',
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                const errores = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Error al actualizar');
                alertBox.textContent = errores;
                alertBox.classList.remove('d-none');
                alertBox.classList.add('alert-danger');
                return;
            }

            window.location.href = "{{ route('incidencias.show', $incidencia->id) }}";

        } catch (err) {
            alertBox.textContent = 'No se pudo conectar con el servidor';
            alertBox.classList.remove('d-none');
            alertBox.classList.add('alert-danger');
        }
    });
</script>
@endsection