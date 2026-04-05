<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

<x-navbar />

<div class="container">

    <div class="az-content az-content-dashboard">
        <div class="az-content-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Data OLT</h4>
                <a href="{{ route('olts.create') }}" class="btn btn-primary">+ Tambah OLT</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama OLT</th>
                            <th>Server</th>
                            <th>Tipe</th>
                            <th>Jumlah PON</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($olts as $key => $olt)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $olt->nama }}</td>
                            <td>{{ $olt->server->nama ?? '-' }}</td>
                            <td>{{ $olt->tipe }}</td>
                            <td>{{ $olt->jumlah_pon }}</td>
                            <td>

                                <!-- EDIT -->
                                <button 
                                    type="button"
                                    class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $olt->id }}"
                                    data-nama="{{ $olt->nama }}"
                                    data-server="{{ $olt->server_id }}"
                                    data-tipe="{{ $olt->tipe }}"
                                    data-pon="{{ $olt->jumlah_pon }}"
                                    data-toggle="modal"
                                    data-target="#modalEdit"
                                >
                                    Edit
                                </button>

                                <!-- HAPUS -->
                                <button 
                                    type="button"
                                    class="btn btn-sm btn-danger btn-hapus"
                                    data-id="{{ $olt->id }}"
                                    data-nama="{{ $olt->nama }}"
                                    data-toggle="modal"
                                    data-target="#modalHapus"
                                >
                                    Hapus
                                </button>

                            </td>
                        </tr>
                        
                       
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ================= -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="formEdit">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit OLT</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="mb-2">
                        <label>Nama</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Server</label>
                        <select name="server_id" id="edit_server" class="form-control">
                            @foreach($servers as $server)
                                <option value="{{ $server->id }}">{{ $server->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Tipe</label>
                        <input type="text" name="tipe" id="edit_tipe" class="form-control">
                    </div>

                    <div class="mb-2">
                        <label>Jumlah PON</label>
                        <input type="number" name="jumlah_pon" id="edit_pon" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL HAPUS ================= -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="formHapus">
            @csrf
            @method('DELETE')

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Hapus OLT</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p>Yakin hapus <b id="hapus_nama"></b> ?</p>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<x-end />

<!-- ✅ WAJIB UNTUK AZIA (BOOTSTRAP 4) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // EDIT
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {

            let id = this.dataset.id;

            document.getElementById('edit_nama').value = this.dataset.nama;
            document.getElementById('edit_server').value = this.dataset.server;
            document.getElementById('edit_tipe').value = this.dataset.tipe;
            document.getElementById('edit_pon').value = this.dataset.pon;

            document.getElementById('formEdit').action = `/olts/${id}`;
        });
    });

    // HAPUS
    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function () {

            let id = this.dataset.id;

            document.getElementById('hapus_nama').innerText = this.dataset.nama;
            document.getElementById('formHapus').action = `/olts/${id}`;
        });
    });

});
</script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Sebelumnya",
                        next: "Berikutnya"
                    }
                }
            });
        });
    </script>
</body>
</html>