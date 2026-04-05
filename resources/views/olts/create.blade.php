
<!DOCTYPE html>
<x-head />

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<body>

<x-navbar />

<div class="az-content az-content-dashboard">
<div class="container">
<div class="az-content-body">

<div class="d-flex justify-content-between align-items-center mb-3">
<h4>Tambah OLT</h4>
<a href="{{ route('olts.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div class="card">
<div class="card-body">

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form action="{{ route('olts.store') }}" method="POST">
@csrf

<div class="form-group mb-3">
<label>Server</label>
<select name="server_id" class="form-control" required>
<option value="">-- Pilih Server --</option>
@foreach($servers as $server)
<option value="{{ $server->id }}">{{ $server->nama }}</option>
@endforeach
</select>
</div>

<div class="form-group mb-3">
<label>Nama OLT</label>
<input type="text" name="nama" class="form-control" required>
</div>

<div class="form-group mb-3">
<label>IP OLT</label>
<input type="text" name="ip" class="form-control">
</div>

<div class="form-group mb-3">
<label>Tipe</label>
<select name="tipe" class="form-control" required>
<option value="EPON">EPON</option>
<option value="GPON">GPON</option>
</select>
</div>

<div class="form-group mb-3">
<label>Jumlah PON</label>
<input type="number" name="jumlah_pon" class="form-control" required>
</div>

<hr>

<h5>Lokasi OLT</h5>

<div class="row">

<div class="col-md-6 mb-3">
<label>Latitude</label>
<input type="text" id="lat" name="lat" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Longitude</label>
<input type="text" id="lng" name="lng" class="form-control" required>
</div>

</div>

<div id="map" style="height:400px;border-radius:10px;"></div>

<br>

<button class="btn btn-primary">Simpan</button>
<a href="{{ route('olts.index') }}" class="btn btn-secondary">Batal</a>

</form>

</div>
</div>

</div>
</div>
</div>

<x-end />


<script>

document.addEventListener("DOMContentLoaded",function(){

var map=L.map('map').setView([-6.3840, 108.2780],13);


// GOOGLE SATELLITE

L.tileLayer(
'https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
{
zoom: 17,
                maxZoom: 21,
subdomains:['mt0','mt1','mt2','mt3']
}
).addTo(map);


// LABELS

L.tileLayer(
'https://{s}.google.com/vt/lyrs=h&x={x}&y={y}&z={z}',
{
maxZoom:20,
subdomains:['mt0','mt1','mt2','mt3']
}
).addTo(map);



var marker;


// KLIK MAP

map.on('click',function(e){

var lat=e.latlng.lat;
var lng=e.latlng.lng;

document.getElementById('lat').value=lat;
document.getElementById('lng').value=lng;

if(marker){
marker.setLatLng(e.latlng);
}else{

marker=L.marker(e.latlng,{
draggable:true
}).addTo(map);

marker.on('dragend',function(event){

var pos=event.target.getLatLng();

document.getElementById('lat').value=pos.lat;
document.getElementById('lng').value=pos.lng;

});

}

});

});

</script>

</body>
</html>
