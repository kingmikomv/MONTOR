<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

<x-navbar />

<div class="container">
    <div class="az-content az-content-dashboard">
        <div class="az-content-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Data ODC</h4>
                <a href="{{ route('odcs.create') }}" class="btn btn-primary">+ Tambah ODC</a>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>OLT</th>
                            <th>PON</th>
                            <th>Splitter</th>
                            <th>Kapasitas</th>
                            <th>Terpakai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($odcs as $odc)
                        <tr>
                            <td>{{ $odc->nama }}</td>
                            <td>{{ $odc->olt->nama }}</td>
                            <td>PON {{ $odc->pon->pon_number }}</td>
                            <td>{{ $odc->splitter }}</td>
                            <td>{{ $odc->kapasitas }}</td>
                            <td>
                                {{ $odc->port_terpakai }} / {{ $odc->kapasitas }}
                                @if($odc->port_terpakai >= $odc->kapasitas)
                                    <span class="badge badge-danger">FULL</span>
                                @endif
                            </td>
                            <td>
                                <!-- DETAIL -->
                                <a href="{{ route('odcs.show', $odc->id) }}" class="btn btn-info btn-sm">
                                    Detail
                                </a>

                                <!-- EDIT -->
                                <button 
                                    class="btn btn-warning btn-sm btn-edit"
                                    data-id="{{ $odc->id }}"
                                    data-nama="{{ $odc->nama }}"
                                    data-splitter="{{ $odc->splitter }}"
                                    data-kapasitas="{{ $odc->kapasitas }}"
                                >
                                    Edit
                                </button>

                                <!-- HAPUS -->
                                <button 
                                    class="btn btn-danger btn-sm btn-hapus"
                                    data-id="{{ $odc->id }}"
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
                <h5>Edit ODC</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Splitter</label>
<select name="splitter" id="edit_splitter" class="form-control" required>
    <option value="">-- Pilih Splitter --</option>
    <option value="1:2">1:2</option>
    <option value="1:4">1:4</option>
    <option value="1:8">1:8</option>
    <option value="1:16">1:16</option>
</select>                </div>

                <div class="form-group">
                    <label>Kapasitas</label>
                    <input type="number" name="kapasitas" id="edit_kapasitas" class="form-control" required>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Update</button>
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
                <h5>Hapus ODC</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                Yakin mau hapus data ini?
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </div>
    </form>
  </div>
</div>

<x-end />



<script>
$(document).ready(function () {

    // DataTable
    $('#datatable').DataTable();

    // EDIT
    $('.btn-edit').click(function(){
        let id = $(this).data('id');

        $('#edit_nama').val($(this).data('nama'));
        $('#edit_splitter').val($(this).data('splitter'));
        $('#edit_kapasitas').val($(this).data('kapasitas'));

        $('#formEdit').attr('action', '/odcs/' + id);

        $('#modalEdit').modal('show');
    });

    // HAPUS
    $('.btn-hapus').click(function(){
        let id = $(this).data('id');

        $('#formHapus').attr('action', '/odcs/' + id);

        $('#modalHapus').modal('show');
    });

});
</script>

</body>
</html>