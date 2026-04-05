<!DOCTYPE html>
<html lang="en">

<x-head />
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<body>

<x-navbar />

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">

            <div class="d-flex justify-content-between mb-3">
                <h4>Tambah Pelanggan</h4>
                <a href="{{ route('pelanggans.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <div class="card">
                <div class="card-body">

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('pelanggans.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nama -->
                        <div class="mb-3">
                            <label>Nama Pelanggan</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
    <label>Username PPPoE (MikroTik)</label>
    <input type="text" name="username_pppoe" class="form-control" required placeholder="contoh: ari@pamayahan">
</div>
                        <!-- ODP -->
                        <div class="mb-3">
                            <label>Pilih ODP</label>
                            <select name="odp_id" class="form-control" required>
                                <option value="">-- Pilih ODP --</option>
                                @foreach($odps as $odp)
                                    <option value="{{ $odp->id }}">
                                        {{ $odp->nama }} ({{ $odp->port_terpakai }}/{{ $odp->kapasitas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control"></textarea>
                        </div>

                        <!-- Koordinat -->
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="lat" class="form-control" placeholder="Latitude">
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="lng" class="form-control" placeholder="Longitude">
                            </div>
                        </div>

                        <!-- Foto -->
                        <div class="mb-3 mt-3">
                            <label>Foto Rumah</label>
                            <input type="file" name="foto" class="form-control">
                        </div>
 <!-- Map -->
                        <div class="mb-3">
                            <label>Pilih Lokasi di Map</label>
                            <div id="map" style="height: 300px; border-radius:10px;"></div>
                        </div>
                        <button class="btn btn-primary">Simpan</button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<x-end />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // =========================
    // INIT MAP SATELIT + LABEL
    // =========================
    var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        subdomains:['mt0','mt1','mt2','mt3'],
            maxZoom: 21

    });

    var labels = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
        subdomains:['mt0','mt1','mt2','mt3'],
            maxZoom: 21,
        pane: 'overlayPane'
    });

    var map = L.map('map', {
        center: [-6.3840, 108.2780], // Sindangkerta, Pamayahan, Indramayu
        zoom: 16,
            maxZoom: 21,                   // bisa lebih dekat
        layers: [satellite, labels]
    });

    var marker;

    // klik map → isi lat & lng
    map.on('click', function (e) {
        var lat = e.latlng.lat.toFixed(7);
        var lng = e.latlng.lng.toFixed(7);

        document.querySelector('input[name="lat"]').value = lat;
        document.querySelector('input[name="lng"]').value = lng;

        if (marker) {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map);
    });

    // opsional: auto lokasi user
    map.locate({setView: true, maxZoom: 16});
    map.on('locationfound', function(e) {
        if (marker) map.removeLayer(marker);

        marker = L.marker(e.latlng).addTo(map)
            .bindPopup("Lokasi kamu").openPopup();

        document.querySelector('input[name="lat"]').value = e.latlng.lat.toFixed(7);
        document.querySelector('input[name="lng"]').value = e.latlng.lng.toFixed(7);
    });
</script>
</body>
</html>