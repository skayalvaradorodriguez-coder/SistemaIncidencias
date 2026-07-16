@extends('layouts.app')

@section('title', 'Nueva Incidencia')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #mapa { height: 380px; border-radius: 4px; z-index: 1; }
</style>
@endsection

@section('content')

<div class="container-fluid">

    <h1 class="mb-4">Nueva Incidencia</h1>

    <div id="alertBox" class="alert d-none"></div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registrar nueva incidencia</h3>
        </div>

        <div class="card-body">

            <form id="formIncidencia">

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" id="titulo" class="form-control" placeholder="Ej: Bache en avenida principal">
                </div>

                <div class="form-group">
                    <label>Descripción</label>
                    <textarea id="descripcion" class="form-control" rows="3" placeholder="Describa la incidencia"></textarea>
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
                    <select id="prioridad" class="form-control">
                        <option value="Baja">Baja</option>
                        <option value="Media">Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Dirección</label>
                    <input type="text" id="direccion" class="form-control" placeholder="Dirección aproximada">
                </div>

                <div class="form-group">
                    <label>Fotografía de la incidencia <span class="text-muted">(opcional)</span></label>
                    <div class="custom-file">
                        <input type="file" id="foto" class="custom-file-input"
                               accept="image/jpeg,image/png,image/webp" capture="environment">
                        <label class="custom-file-label" for="foto" id="fotoLabel">Tomar foto o elegir archivo...</label>
                    </div>
                    <small class="form-text text-muted">JPG, PNG o WEBP. Máximo 4 MB. En el celular se abrirá la cámara.</small>
                    <img id="fotoPreview" class="img-fluid rounded mt-2 d-none" style="max-height: 220px;">
                </div>

                <div class="form-group">
                    <label>Ubicación en el mapa</label>
                    <small class="form-text text-muted mb-2">
                        Haga clic en el mapa para marcar el punto exacto de la incidencia, o use su ubicación actual.
                    </small>
                    <div id="mapa"></div>
                    <button type="button" id="btnMiUbicacion" class="btn btn-info btn-sm mt-2">
                        <i class="fas fa-location-arrow"></i> Usar mi ubicación
                    </button>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Latitud</label>
                            <input type="text" id="latitud" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Longitud</label>
                            <input type="text" id="longitud" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <a href="{{ route('incidencias.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>

                <button type="submit" id="btnGuardar" class="btn btn-primary">
                    Guardar Incidencia
                </button>

            </form>

        </div>
    </div>

</div>

@endsection

@section('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // ================== MAPA ==================
    const mapa = L.map('mapa').setView([-2.2276, -80.8585], 12);

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

    mapa.on('click', e => colocarMarcador(e.latlng.lat, e.latlng.lng));

    document.getElementById('btnMiUbicacion').addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Su navegador no soporta geolocalización.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            pos => {
                colocarMarcador(pos.coords.latitude, pos.coords.longitude);
                mapa.setView([pos.coords.latitude, pos.coords.longitude], 16);
            },
            () => alert('No se pudo obtener su ubicación. Marque el punto manualmente en el mapa.')
        );
    });

    // ================== COMBOS ==================
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
                provinciaSelect.innerHTML += `<option value="${provincia.id}">${provincia.nombre}</option>`;
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
                    ciudadSelect.innerHTML += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
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
                subtipoSelect.innerHTML += `<option value="${subtipo.id}">${subtipo.nombre}</option>`;
            });
        }
    });

    // ================== FOTO (vista previa) ==================
    document.getElementById('foto').addEventListener('change', function () {
        const archivo = this.files[0];
        const preview = document.getElementById('fotoPreview');
        const etiqueta = document.getElementById('fotoLabel');

        if (!archivo) {
            preview.classList.add('d-none');
            etiqueta.textContent = 'Tomar foto o elegir archivo...';
            return;
        }

        etiqueta.textContent = archivo.name;
        preview.src = URL.createObjectURL(archivo);
        preview.classList.remove('d-none');
    });

    // ================== GUARDAR ==================
    document.getElementById('formIncidencia').addEventListener('submit', async function (e) {
        e.preventDefault();

        const alertBox = document.getElementById('alertBox');
        alertBox.className = 'alert d-none';

        const formData = new FormData();
        formData.append('titulo', document.getElementById('titulo').value);
        formData.append('descripcion', document.getElementById('descripcion').value);
        formData.append('ciudad_id', ciudadSelect.value);
        formData.append('tipo_incidencia_id', tipoSelect.value);
        formData.append('subtipo_incidencia_id', subtipoSelect.value);
        formData.append('prioridad', document.getElementById('prioridad').value);
        formData.append('direccion', document.getElementById('direccion').value);

        if (document.getElementById('latitud').value) {
            formData.append('latitud', document.getElementById('latitud').value);
            formData.append('longitud', document.getElementById('longitud').value);
        }

        const archivoFoto = document.getElementById('foto').files[0];
        if (archivoFoto) {
            formData.append('foto', archivoFoto);
        }

        try {
            // Se usa fetch directo: FormData define su propio Content-Type (multipart)
            const response = await fetch('/api/incidencias', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                const errores = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Error al guardar');

                alertBox.textContent = errores;
                alertBox.classList.remove('d-none');
                alertBox.classList.add('alert-danger');
                return;
            }

            window.location.href = "{{ route('incidencias.index') }}";

        } catch (err) {
            alertBox.textContent = 'No se pudo conectar con el servidor';
            alertBox.classList.remove('d-none');
            alertBox.classList.add('alert-danger');
        }
    });
</script>

@endsection