<!DOCTYPE html>
<html lang="en">

<x-head />

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<style>
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        padding: 5px 10px;
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 8px;
    }
</style>

<body>

    <x-navbar />

    <div class="az-content az-content-dashboard">
        <div class="container">
            <div class="az-content-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

    <h4 class="mb-0">Data Pelanggan</h4>

    <div class="d-flex">

        <button class="btn btn-success mr-2" data-toggle="modal" data-target="#importExcel">
            <i class="fa fa-file-excel"></i> Import Excel
        </button>

        <a href="{{ route('pelanggans.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Tambah
        </a>

    </div>

</div>

                <div class="card p-3">
                    <div class="table-responsive">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                            </div>
                        @endif
                        <table class="table table-hover" id="datatable">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>ODP</th>
                                    <th>ODC</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($pelanggans as $i => $p)

                                    <tr data-id="{{ $p->id }}" data-nama="{{ $p->nama }}" data-odp="{{ $p->odp_id }}"
                                        data-port="{{ $p->port }}" data-alamat='@json($p->alamat)' data-lat="{{ $p->lat }}"
                                        data-lng="{{ $p->lng }}">

                                        <td>{{ $i + 1 }}</td>

                                        <td>{{ $p->nama }}</td>

                                        <td>{{ $p->odp->nama ?? '-' }}</td>

                                        <td>

                                            @if($p->odp)

                                                @if($p->odp->odc)
                                                    {{ $p->odp->odc->nama }}

                                                @elseif($p->odp->parent && $p->odp->parent->odc)
                                                    {{ $p->odp->parent->odc->nama }}

                                                @else
                                                    -
                                                @endif

                                            @else
                                                -

                                            @endif

                                        </td>


                                        <td>
                                            <a href="{{ route('pelanggans.show', $p->id) }}"
                                                class="btn btn-info btn-sm">Detail</a>
                                            <button class="btn btn-warning btn-sm btn-edit">Edit</button>
                                            <button class="btn btn-danger btn-sm btn-delete">Hapus</button>
                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>
                        </table>

                    </div>
                </div>


                <div class="card mt-4 p-3">
                    <h5>Map Pelanggan</h5>
                    <div id="map" style="height:400px;"></div>
                </div>


            </div>
        </div>
    </div>


<div class="modal fade" id="importExcel" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Import Data Pelanggan</h5>
<button type="button" class="close" data-dismiss="modal">
<span>&times;</span>
</button>
</div>

<form action="{{ route('pelanggans.import') }}" method="POST" enctype="multipart/form-data">
@csrf

<div class="modal-body">

<div class="form-group">
<label>Upload File Excel</label>
<input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
</div>
<div class="alert alert-info">

<b>Format Excel wajib seperti ini :</b>

<div class="table-responsive mt-2">
<table class="table table-sm table-bordered mb-0">
<thead class="thead-light">
<tr>
<th>odp_id</th>
<th>converter_id</th>
<th>nama</th>
<th>username_pppoe</th>
<th>onu_sn</th>
<th>port</th>
<th>alamat</th>
<th>lat</th>
<th>lng</th>
<th>status</th>
</tr>
</thead>
</table>
</div>

</div>

</div>

<div class="modal-footer">

<button type="button" class="btn btn-secondary" data-dismiss="modal">
Batal
</button>

<button type="submit" class="btn btn-success">
Upload Excel
</button>

</div>

</form>

</div>
</div>
</div>




    <!-- MODAL EDIT -->

    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog modal-lg">

            <form method="POST" id="formEdit">

                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Edit Pelanggan + Lokasi</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>ODP</label>

                            <select name="odp_id" id="edit_odp" class="form-control">
                                @foreach($odps as $o)
                                    <option value="{{ $o->id }}">{{ $o->nama }}</option>
                                @endforeach
                            </select>

                        </div>

                        <!-- FIX -->
                        <div class="form-group">
                            <label>Port</label>
                            <input type="number" name="port" id="edit_port" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control"></textarea>
                        </div>

                        <div class="row">

                            <div class="col">
                                <label>Latitude</label>
                                <input type="text" name="lat" id="edit_lat" class="form-control">
                            </div>

                            <div class="col">
                                <label>Longitude</label>
                                <input type="text" name="lng" id="edit_lng" class="form-control">
                            </div>

                        </div>

                        <div class="mt-3">
                            <div id="mapEdit" style="height:300px;"></div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Update</button>
                    </div>

                </div>

            </form>

        </div>
    </div>



    <!-- MODAL DELETE -->

   <div class="modal fade" id="modalEdit">
<div class="modal-dialog modal-lg">

<form method="POST" id="formEdit">

@csrf
@method('PUT')

<div class="modal-content">

<div class="modal-header">
<h5 class="modal-title">Edit Pelanggan + Lokasi</h5>
<button type="button" class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">

<div class="row">

<div class="col-md-6">
<label>Nama</label>
<input type="text" name="nama" id="edit_nama" class="form-control">
</div>

<div class="col-md-6">
<label>Port</label>
<input type="number" name="port" id="edit_port" class="form-control">
</div>

</div>

<div class="form-group mt-3">
<label>ODP</label>

<select name="odp_id" id="edit_odp" class="form-control">
@foreach($odps as $o)
<option value="{{ $o->id }}">{{ $o->nama }}</option>
@endforeach
</select>

</div>

<div class="form-group">
<label>Alamat</label>
<textarea name="alamat" id="edit_alamat" class="form-control"></textarea>
</div>

<div class="row">

<div class="col-md-6">
<label>Latitude</label>
<input type="text" name="lat" id="edit_lat" class="form-control">
</div>

<div class="col-md-6">
<label>Longitude</label>
<input type="text" name="lng" id="edit_lng" class="form-control">
</div>

</div>

<div class="mt-3">
<div id="mapEdit" style="height:300px;"></div>
</div>

</div>

<div class="modal-footer">
<button type="submit" class="btn btn-primary">
Update
</button>
</div>

</div>

</form>

</div>
</div>


    <x-end />


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <script>

$(document).ready(function(){

/* ================================
   MAP UTAMA
================================ */

var map = L.map('map').setView([-6.39,108.32],14);

L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{
    subdomains:['mt0','mt1','mt2','mt3'],
    maxZoom:20
}).addTo(map);

var pelanggans = @json($pelanggans);

pelanggans.forEach(function(p){

    if(p.lat && p.lng){

        L.marker([p.lat,p.lng])
        .addTo(map)
        .bindPopup("👤 "+p.nama);

    }

});


/* ================================
   DATATABLE
================================ */

var table = $('#datatable').DataTable({
    pageLength:10,
    responsive:true
});


/* ================================
   ZOOM MAP SAAT ROW DIKLIK
================================ */

$('#datatable tbody').on('click','tr',function(){

    let lat = $(this).data('lat');
    let lng = $(this).data('lng');

    if(lat && lng){

        map.setView([lat,lng],17);

    }

});


/* ================================
   EDIT MODAL
================================ */

let mapEdit;
let markerEdit;

$(document).on('click','.btn-edit',function(e){

    e.stopPropagation();

    let row = $(this).closest('tr');

    let id = row.data('id');

    $('#edit_nama').val(row.data('nama'));
    $('#edit_odp').val(row.data('odp'));
    $('#edit_port').val(row.data('port'));
    $('#edit_alamat').val(row.data('alamat'));
    $('#edit_lat').val(row.data('lat'));
    $('#edit_lng').val(row.data('lng'));

    $('#formEdit').attr('action','/pelanggans/'+id);

    $('#modalEdit').modal('show');

});


/* ================================
   MAP EDIT
================================ */

$('#modalEdit').on('shown.bs.modal',function(){

setTimeout(function(){

    if(mapEdit){

        mapEdit.remove();

    }

    mapEdit = L.map('mapEdit').setView([-6.39,108.32],14);

    L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{
        subdomains:['mt0','mt1','mt2','mt3'],
        maxZoom:20
    }).addTo(mapEdit);

    let lat = $('#edit_lat').val();
    let lng = $('#edit_lng').val();

    if(lat && lng){

        markerEdit = L.marker([lat,lng]).addTo(mapEdit);

        mapEdit.setView([lat,lng],17);

    }

    mapEdit.on('click',function(e){

        let lat = e.latlng.lat;
        let lng = e.latlng.lng;

        $('#edit_lat').val(lat);
        $('#edit_lng').val(lng);

        if(markerEdit){

            markerEdit.setLatLng(e.latlng);

        }else{

            markerEdit = L.marker(e.latlng).addTo(mapEdit);

        }

    });

},300);

});


/* ================================
   DELETE MODAL
================================ */

$(document).on('click','.btn-delete',function(e){

    e.stopPropagation();

    let row = $(this).closest('tr');

    let id = row.data('id');
    let nama = row.data('nama');

    $('#delete_nama').text(nama);
    $('#formDelete').attr('action','/pelanggans/'+id);

    $('#modalDelete').modal('show');

});


});

</script>

</body>

</html>