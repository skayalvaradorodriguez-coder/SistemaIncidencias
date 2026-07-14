@extends('layouts.app')

@section('title', 'Editar Incidencia')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapa { height: 380px; border-radius: 4px; z-index: 1; }
</style>
@endsection

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

                <div class="form-group">
                    <label>Ubicación en el mapa</label>
                    <small class="form-text text-muted mb-2">
                        Haga clic en el mapa o arrastre el marcador para actualizar la ubicación.
                    </small>
                    <div id="mapa"></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Latitud</label>
                            <input type="text" id="latitud" class="form-control" readonly
                                   value="{{ $incidencia->latitud }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Longitud</label>
                            <input type="text" id="longitud" class="form-control" readonly
                                   value="{{ $incidencia->longitud }}">
                        </div>
                    </div>
                </div>

                <a href="{{ route('incidencias.show', $incidencia->id) }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" id="btnActualizar" class="btn btn-primary">Guardar Cambios</button>

            </form>

        </div>
    </div>

</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ================== MAPA ==================
    const latInicial = {{ $incidencia->latitud ?? 'null' }};
    const lngInicial = {{ $incidencia->longitud ?? 'null' }};

    const mapa = L.map('mapa').setView(
        latInicial !== null ? [latInicial, lngInicial] : [-2.2276, -80.8585],
        latInicial !== null ? 16 : 12
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    let marcador = null;

    function setCoords(lat, lng) {
        document.getElementById('latitud').value = lat.toFixed(6);
        document.getElementById('longitud').value = lng.toFixed(6);
    }

    function colocarMarcador(lat, lng) {
        if (marcador) {
            marcador.setLatLng([lat, lng]);
        } else {
            marcador = L.marker([lat, lng], { draggable: true }).addTo(mapa);
            marcador.on('dragend', e => {
                const p = e.target.getLatLng();
                setCoords(p.lat, p.lng);
            });
        }
        setCoords(lat, lng);
    }

    if (latInicial !== null && lngInicial !== null) {
        colocarMarcador(latInicial, lngInicial);
    }

    mapa.on('click', e => colocarMarcador(e.latlng.lat, e.latlng.lng));

    // ================== COMBOS ==================
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

    // ================== GUARDAR ==================
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
            latitud: document.getElementById('latitud').value || null,
            longitud: document.getElementById('longitud').value || null,
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