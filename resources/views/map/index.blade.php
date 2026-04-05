<!DOCTYPE html>
<html lang="en">

<x-head />

<!-- 🔥 LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<body>

<x-navbar />

<div class="container mt-4">
    <h4 class="mb-3">Peta Jaringan</h4>

    <div id="map" style="height:500px; border-radius:10px;"></div>
</div>

<x-end />

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // 🔥 INIT MAP
    var map = L.map('map').setView([-6.2, 106.8], 10);

    // 🔥 GOOGLE MAP STYLE (HYBRID)
    var hybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        subdomains:['mt0','mt1','mt2','mt3'],
        maxZoom: 20
    }).addTo(map);

    // 🔥 OPTIONAL: NORMAL MODE
    var normal = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        subdomains:['mt0','mt1','mt2','mt3'],
        maxZoom: 20
    });

    // 🔁 SWITCH LAYER
    L.control.layers({
        "Normal": normal,
        "Hybrid": hybrid
    }).addTo(map);

    // 🔥 DATA DARI LARAVEL
    var odps = @json($odps);
    var selectedOdp = @json($selectedOdp);

    // 🔵 ICON NORMAL
    var iconNormal = L.icon({
        iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
        iconSize: [32, 32]
    });

    // 🔴 ICON AKTIF
    var iconActive = L.icon({
        iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
        iconSize: [32, 32]
    });

    var group = new L.featureGroup();

    // 🔥 LOOP SEMUA ODP
    odps.forEach(function(odp){

        if(odp.lat && odp.lng){

            let isActive = selectedOdp && selectedOdp.id === odp.id;

            let marker = L.marker([odp.lat, odp.lng], {
                icon: isActive ? iconActive : iconNormal
            })
            .addTo(map)
            .bindPopup(`
                📍 <b>${odp.nama}</b><br>
                <a href="/map?odp_id=${odp.id}">Lihat Lokasi</a>
            `)
            .bindTooltip(odp.nama);

            group.addLayer(marker);

            // klik marker → focus ulang
            marker.on('click', function(){
                window.location.href = "/map?odp_id=" + odp.id;
            });

            // 🔥 AUTO ZOOM + HIGHLIGHT
            if(isActive){
                map.setView([odp.lat, odp.lng], 17);

                marker.openPopup();

                L.circle([odp.lat, odp.lng], {
                    radius: 50,
                    color: 'red',
                    fillOpacity: 0.2
                }).addTo(map);
            }
        }

    });

    // 🔥 AUTO FIT (kalau tidak pilih ODP)
    if(!selectedOdp){
        map.fitBounds(group.getBounds());
    }

</script>

</body>
</html>