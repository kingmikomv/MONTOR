<!DOCTYPE html>
<html lang="en">

<x-head />

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<body>

    <x-navbar />
    <div class="container">
        <div class="az-content az-content-dashboard">

            <div class="az-content-body">

                <div class="d-flex justify-content-between mb-3">
                    <h4>Tambah ODC</h4>
                    <a href="{{ route('odcs.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('odcs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- OLT -->
                            <div class="mb-3">
                                <label>OLT</label>
                                <select name="olt_id" id="olt_id" class="form-control" required>
                                    <option value="">-- Pilih OLT --</option>
                                    @foreach($olts as $olt)
                                        <option value="{{ $olt->id }}">{{ $olt->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PON -->
                            <div class="mb-3">
                                <label>PON</label>
                                <select name="pon_id" id="pon_id" class="form-control" required>
                                    <option value="">-- Pilih PON --</option>
                                </select>
                            </div>

                            <!-- Nama -->
                            <div class="mb-3">
                                <label>Nama ODC</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <!-- Splitter -->
                            <div class="mb-3">
                                <label>Splitter</label>
                                <select name="splitter" class="form-control" required>
                                    <option value="1:2">1:2</option>
                                    <option value="1:4">1:4</option>
                                    <option value="1:8">1:8</option>
                                    <option value="1:16">1:16</option>
                                </select>
                            </div>

                            <!-- Alamat -->
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control"></textarea>
                            </div>

                            <!-- LAT LONG -->
                            <div class="mb-3">
                                <label>Lat Long</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="lat" class="form-control" placeholder="Latitude">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="lng" class="form-control" placeholder="Longitude">
                                    </div>
                                </div>
                            </div>

                            <!-- MAP -->
                            <div class="mb-3">
                                <label>Pilih Lokasi di Map</label>
                                <div id="map" style="height: 300px; border-radius:10px;"></div>
                            </div>

                            <!-- FOTO -->
                            <div class="mb-3">
                                <label>Foto ODC</label>
                                <input type="file" name="foto" class="form-control-file">
                            </div>

                            <button class="btn btn-primary mt-3">Simpan</button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-end />

    <!-- SCRIPT -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // =========================
        // LOAD PON BERDASARKAN OLT
        // =========================
        document.getElementById('olt_id').addEventListener('change', function () {
            let oltId = this.value;
            let ponSelect = document.getElementById('pon_id');

            ponSelect.innerHTML = '<option value="">-- Pilih PON --</option>';

            if (oltId) {
                fetch(`/get-pons/${oltId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function (pon) {
                            let option = document.createElement('option');
                            option.value = pon.id;
                            option.text = 'PON ' + pon.pon_number;
                            ponSelect.appendChild(option);
                        });
                    });
            }
        });

        // =========================
        // INIT MAP
        // =========================
       // SATELIT (gambar)
var satellite = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    subdomains:['mt0','mt1','mt2','mt3'],
                maxZoom: 21

});

// LABEL (nama jalan & daerah)
var labels = L.tileLayer('https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', {
    subdomains:['mt0','mt1','mt2','mt3'],
                maxZoom: 21,
    pane: 'overlayPane'
});

// INIT MAP
var map = L.map('map', {
    center: [-6.3840, 108.2780],
    zoom: 17,
                maxZoom: 21,
    layers: [satellite, labels] // digabung
});
        var marker;

        // klik map
        map.on('click', function (e) {
            var lat = e.latlng.lat.toFixed(7);
            var lng = e.latlng.lng.toFixed(7);

            // isi input
            document.querySelector('input[name="lat"]').value = lat;
            document.querySelector('input[name="lng"]').value = lng;

            // hapus marker lama
            if (marker) {
                map.removeLayer(marker);
            }

            // tambah marker baru
            marker = L.marker([lat, lng]).addTo(map);
        });

        // OPTIONAL: ambil lokasi user
        map.locate({setView: true, maxZoom: 16});

        map.on('locationfound', function(e) {
            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker(e.latlng).addTo(map)
                .bindPopup("Lokasi kamu").openPopup();

            document.querySelector('input[name="lat"]').value = e.latlng.lat.toFixed(7);
            document.querySelector('input[name="lng"]').value = e.latlng.lng.toFixed(7);
        });
    </script>

</body>
</html>