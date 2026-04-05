<!DOCTYPE html>
<html lang="en">

<x-head />

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet-search@3.0.9/dist/leaflet-search.min.css" />
<script src="https://unpkg.com/leaflet-search@3.0.9/dist/leaflet-search.min.js"></script>

<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<body>

<x-navbar />

<div class="az-content az-content-dashboard">
<div class="container">
<div class="az-content-body">

<div class="mb-4">
    <h4>Dashboard Network</h4>
    <p class="text-muted">Monitoring Infrastruktur FTTH</p>
</div>

<div class="row">

    <div class="col-md-3">
        <div class="card bg-primary text-white text-center">
            <div class="card-body">
                <h6>Server</h6>
                <h3>{{ $totalServer }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white text-center">
            <div class="card-body">
                <h6>OLT</h6>
                <h3>{{ $totalOlt }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white text-center">
            <div class="card-body">
                <h6>ODC</h6>
                <h3>{{ $totalOdc }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-danger text-white text-center">
            <div class="card-body">
                <h6>ODP Full</h6>
                <h3>{{ $odpFull }}</h3>
            </div>
        </div>
    </div>

</div>

<div class="card mt-4">
    <div class="card-body">
        <h5>⚠️ ODP Hampir / Sudah Penuh</h5>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Kapasitas</th>
            </tr>
            </thead>
            <tbody>

            @foreach($odpsWarning as $odp)
                <tr>
                    <td>{{ $odp->nama }}</td>
                    <td>{{ $odp->port_terpakai }}/{{ $odp->kapasitas }}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
</div>

<div class="row mt-4">
<div class="col-md-12">
<div class="card">
<div class="card-body">
<h5>Map Infrastruktur Fiber</h5>
<div id="map" style="height:500px;"></div>
</div>
</div>
</div>
</div>

</div>
</div>
</div>

<x-end />

<script>
document.addEventListener("DOMContentLoaded", function () {

    var map = L.map('map');

    map.createPane('labels');
    map.getPane('labels').style.zIndex = 650;
    map.getPane('labels').style.pointerEvents = 'none';

    L.tileLayer(
        'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
        { subdomains: ['mt0','mt1','mt2','mt3'], maxZoom: 21 }
    ).addTo(map);

    L.tileLayer(
        'https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',
        { subdomains: ['mt0','mt1','mt2','mt3'], maxZoom: 21, pane: 'labels' }
    ).addTo(map);

    var olts = @json($mapOlts);
    var odcs = @json($mapOdcs);
    var odps = @json($mapOdps);
    var pelanggans = @json($mapPelanggan);

    let bounds = [];
    var cluster = L.markerClusterGroup();

    // 🔥 SEARCH INDEX
    var dataSearch = {};

    var iconOLT = L.divIcon({
        html: `<div style="background:#6f42c1;color:white;padding:6px 10px;border-radius:6px;font-size:11px;font-weight:bold;">OLT</div>`,
        className: '',
        iconSize: [50,24]
    });

    var iconODC = L.divIcon({
        html: `<div style="background:#007bff;color:white;padding:5px 8px;border-radius:6px;font-size:11px;font-weight:bold;">ODC</div>`,
        className: '',
        iconSize: [50,24]
    });

    function iconODP(warna) {
        return L.divIcon({
            html: `<div style="width:16px;height:16px;background:${warna};border-radius:50%;border:3px solid white;"></div>`,
            className: '',
            iconSize: [16,16]
        });
    }

    var iconRumah = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/69/69524.png',
        iconSize: [22,22]
    });

    // 🔵 OLT
    olts.forEach(o => {

        let lat = parseFloat(o.lat);
        let lng = parseFloat(o.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        let latlng = L.latLng(lat, lng);
        dataSearch['OLT - ' + o.nama] = latlng;

        let marker = L.marker(latlng, { icon: iconOLT })
            .bindPopup(`<b>OLT:</b> ${o.nama}`);

        cluster.addLayer(marker);
        bounds.push([lat, lng]);
    });

    // 🔵 ODC
    odcs.forEach(o => {

        let lat = parseFloat(o.lat);
        let lng = parseFloat(o.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        let latlng = L.latLng(lat, lng);
        dataSearch['ODC - ' + o.nama] = latlng;

        let marker = L.marker(latlng, { icon: iconODC })
            .bindPopup(`<b>ODC:</b> ${o.nama}`);

        cluster.addLayer(marker);
        bounds.push([lat, lng]);

        if (o.olt && o.olt.lat) {
            let oltLat = parseFloat(o.olt.lat);
            let oltLng = parseFloat(o.olt.lng);

            if (!isNaN(oltLat) && !isNaN(oltLng)) {
                L.polyline([[lat, lng],[oltLat, oltLng]], {
                    color: '#ff0000',
                    weight: 5
                }).addTo(map);
            }
        }
    });

    // 🟢 ODP
    odps.forEach(odp => {

        let lat = parseFloat(odp.lat);
        let lng = parseFloat(odp.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        let latlng = L.latLng(lat, lng);

        let persen = (odp.port_terpakai / odp.kapasitas) * 100;
        let warna = persen >= 100 ? '#dc3545' : (persen >= 70 ? '#ffc107' : '#28a745');

        dataSearch['ODP - ' + odp.nama] = latlng;

        let marker = L.marker(latlng, { icon: iconODP(warna) })
            .bindPopup(`
                <b>ODP:</b> ${odp.nama}<br>
                Kapasitas: ${odp.port_terpakai}/${odp.kapasitas}<br>
                Parent: ${odp.parent ? odp.parent.nama : 'Langsung ke ODC'}
            `);

        cluster.addLayer(marker);
        bounds.push([lat, lng]);

        if (odp.parent && odp.parent.lat) {

            let pLat = parseFloat(odp.parent.lat);
            let pLng = parseFloat(odp.parent.lng);

            if (!isNaN(pLat) && !isNaN(pLng)) {
                L.polyline([[lat, lng],[pLat, pLng]], {
                    color: '#28a745',
                    weight: 4
                }).addTo(map);
            }

        } else if (odp.odc && odp.odc.lat) {

            let oLat = parseFloat(odp.odc.lat);
            let oLng = parseFloat(odp.odc.lng);

            if (!isNaN(oLat) && !isNaN(oLng)) {
                L.polyline([[lat, lng],[oLat, oLng]], {
                    color: '#28a745',
                    weight: 4
                }).addTo(map);
            }
        }
    });

    // 🏠 PELANGGAN
    pelanggans.forEach(p => {

        let lat = parseFloat(p.lat);
        let lng = parseFloat(p.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        let latlng = L.latLng(lat, lng);
        dataSearch['PLG - ' + p.nama] = latlng;

        let marker = L.marker(latlng, { icon: iconRumah });

        marker.bindPopup(`
<b>${p.nama}</b><br>
ODP: ${p.odp ? p.odp.nama : '-'}<br><br>

<a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank">
📍 Google Maps
</a>
        `);

        cluster.addLayer(marker);
        bounds.push([lat, lng]);

        if (p.odp && p.odp.lat) {

            let oLat = parseFloat(p.odp.lat);
            let oLng = parseFloat(p.odp.lng);

            if (!isNaN(oLat) && !isNaN(oLng)) {
                L.polyline([[lat, lng],[oLat, oLng]], {
                    color: '#28a745',
                    weight: 2,
                    dashArray: '6,6'
                }).addTo(map);
            }
        }
    });

    map.addLayer(cluster);

    // 🔥 SEARCH FIX FINAL
    var searchControl = new L.Control.Search({

        sourceData: function(text, callResponse) {

            let result = {};

            for (let key in dataSearch) {
                if (key.toLowerCase().includes(text.toLowerCase())) {
                    result[key] = dataSearch[key];
                }
            }

            callResponse(result);
        },

        zoom: 19,
        marker: false,
        autoCollapse: true,
        minLength: 1,
        textPlaceholder: 'Cari semua data...'

    });

    searchControl.on('search:locationfound', function(e) {
        map.setView(e.latlng, 19);
    });

    map.addControl(searchControl);

    bounds.length ? map.fitBounds(bounds) : map.setView([-6.2,106.8],14);

});
</script>

</body>
</html>