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
                    <h4>Tambah ODP</h4>
                    <a href="{{ route('odps.index') }}" class="btn btn-secondary">Kembali</a>
                </div>

                <div class="card">
                    <div class="card-body">

                        <form action="{{ route('odps.store') }}" method="POST">
                            @csrf

                            <!-- Sumber Fiber -->
                            <div class="mb-3">
                                <label>Sumber Fiber</label>

                                <select id="sumber_fiber" class="form-control">
                                    <option value="odc">Dari ODC</option>
                                    <option value="odp">Dari ODP</option>
                                </select>

                            </div>


                            <!-- ODC -->
                            <div class="mb-3" id="odc_form">

                                <label>Pilih ODC</label>

                                <select name="odc_id" class="form-control">

                                    <option value="">-- Pilih ODC --</option>

                                    @foreach($odcs as $odc)

                                        <option value="{{ $odc->id }}">{{ $odc->nama }}</option>

                                    @endforeach

                                </select>

                            </div>


                            <!-- ODP Parent -->
                            <div class="mb-3" id="parent_odp_form" style="display:none">

                                <label>Pilih ODP Sumber</label>

                                <select name="parent_odp_id" class="form-control">

                                    <option value="">-- Pilih ODP --</option>

                                    @foreach($odps as $odp)

                                        <option value="{{ $odp->id }}">{{ $odp->nama }}</option>

                                    @endforeach

                                </select>

                            </div>


                            <!-- Nama -->
                            <div class="mb-3">

                                <label>Nama ODP</label>

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


                            <!-- Latitude & Longitude -->
                            <div class="mb-3">

                                <div class="row">

                                    <div class="col-md-6">
                                        <input type="text" name="lat" class="form-control" placeholder="Latitude">
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" name="lng" class="form-control" placeholder="Longitude">
                                    </div>

                                </div>

                            </div>


                            <!-- Map -->
                            <div class="mb-3">

                                <label>Pilih Lokasi di Map</label>

                                <div id="map" style="height:300px;border-radius:10px;"></div>

                            </div>


                            <button class="btn btn-primary mt-3">Simpan</button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-end />

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>

        //////////////////////////////////////////////////////
        // SWITCH SUMBER FIBER
        //////////////////////////////////////////////////////

        var sumber = document.getElementById('sumber_fiber');

        sumber.addEventListener('change', function () {

            if (this.value === 'odc') {

                document.getElementById('odc_form').style.display = 'block';

                document.getElementById('parent_odp_form').style.display = 'none';

            }
            else {

                document.getElementById('odc_form').style.display = 'none';

                document.getElementById('parent_odp_form').style.display = 'block';

            }

        });


        //////////////////////////////////////////////////////
        // INIT MAP SATELIT
        //////////////////////////////////////////////////////

        var satellite = L.tileLayer(
            'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
            {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 21
            });

        var labels = L.tileLayer(
            'https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',
            {
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                maxZoom: 21
            });

        var map = L.map('map', {
            center: [-6.3840, 108.2780],
            zoom: 16,
            maxZoom: 21,
            layers: [satellite, labels]
        });


        var marker;


        //////////////////////////////////////////////////////
        // CLICK MAP
        //////////////////////////////////////////////////////

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


        //////////////////////////////////////////////////////
        // AUTO LOKASI USER
        //////////////////////////////////////////////////////

        map.locate({ setView: true, maxZoom: 16 });

        map.on('locationfound', function (e) {

            if (marker) map.removeLayer(marker);

            marker = L.marker(e.latlng)
                .addTo(map)
                .bindPopup("Lokasi kamu")
                .openPopup();

            document.querySelector('input[name="lat"]').value = e.latlng.lat.toFixed(7);
            document.querySelector('input[name="lng"]').value = e.latlng.lng.toFixed(7);

        });

    </script>

</body>

</html>