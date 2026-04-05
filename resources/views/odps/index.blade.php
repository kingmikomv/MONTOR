<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

    <x-navbar />

    <div class="container">

        <div class="az-content az-content-dashboard">
            <div class="az-content-body">

                <div class="d-flex justify-content-between mb-3">

                    <h4>Data ODP</h4>

                    <a href="{{ route('odps.create') }}" class="btn btn-primary">
                        + Tambah ODP
                    </a>

                </div>

                <div class="table-responsive">

                    <table id="datatable" class="table table-bordered table-hover">

                        <thead class="thead-dark">
                            <tr>
                                <th width="40"></th>
                                <th>Nama</th>
                                <th>ODC</th>
                                <th>Splitter</th>
                                <th>Kapasitas</th>
                                <th>Terpakai</th>
                                <th width="200">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($odps->whereNull('parent_odp_id') as $odp)

                                <tr data-id="{{ $odp->id }}">

                                    <td class="text-center">

                                        @if($odp->children_count > 0)
                                            <span class="details-control" style="cursor:pointer;font-weight:bold;">
                                                +
                                            </span>
                                        @endif

                                    </td>

                                    <td>{{ $odp->nama }}</td>

                                    <td>{{ $odp->odc->nama ?? '-' }}</td>

                                    <td>{{ $odp->splitter }}</td>

                                    <td>{{ $odp->kapasitas }}</td>

                                    <td>{{ $odp->port_terpakai }}</td>

                                    <td>

                                        <a href="{{ route('odps.show', $odp->id) }}" class="btn btn-info btn-sm">
                                            Detail
                                        </a>

                                        <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $odp->id }}"
                                            data-nama="{{ $odp->nama }}" data-odc="{{ $odp->odc_id }}"
                                            data-parent="{{ $odp->parent_odp_id }}" data-splitter="{{ $odp->splitter }}"
                                            data-kapasitas="{{ $odp->kapasitas }}">
                                            Edit
                                        </button>

                                        <button class="btn btn-danger btn-sm btn-hapus" data-id="{{ $odp->id }}">
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



    <!-- MODAL EDIT -->

    <div class="modal fade" id="modalEdit">

        <div class="modal-dialog">

            <form method="POST" id="formEdit">

                @csrf
                @method('PUT')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Edit ODP</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" id="edit_nama" class="form-control">
                        </div>


                        <div class="form-group">
                            <label>Parent ODP</label>

                            <select name="parent_odp_id" id="edit_parent_odp" class="form-control">

                                <option value="">-- Dari ODC --</option>

                                @foreach($odps as $o)

                                    <option value="{{ $o->id }}">
                                        {{ $o->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="form-group">
                            <label>ODC</label>

                            <select name="odc_id" id="edit_odc" class="form-control">

                                <option value="">-- Pilih ODC --</option>

                                @foreach($odcs as $odc)

                                    <option value="{{ $odc->id }}">
                                        {{ $odc->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="form-group">
                            <label>Splitter</label>

                            <select name="splitter" id="edit_splitter" class="form-control">

                                <option value="">-- Pilih Splitter --</option>
                                <option value="1:2">1:2</option>
                                <option value="1:4">1:4</option>
                                <option value="1:8">1:8</option>
                                <option value="1:16">1:16</option>

                            </select>

                        </div>


                        <div class="form-group">
                            <label>Kapasitas</label>

                            <input type="number" name="kapasitas" id="edit_kapasitas" class="form-control" readonly>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-success">
                            Update
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>



    <!-- MODAL HAPUS -->

    <div class="modal fade" id="modalHapus">

        <div class="modal-dialog">

            <form method="POST" id="formHapus">

                @csrf
                @method('DELETE')

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Hapus ODP</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        Yakin ingin menghapus data ini?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger">
                            Hapus
                        </button>
                    </div>

                </div>

            </form>

        </div>

    </div>



    <x-end />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{asset('lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


    <script>

        function format(children) {

            let html = `
<table class="table table-sm table-bordered mb-0">
<tbody>
`;

            children.forEach(function (child) {

                html += `

<tr data-id="${child.id}">

<td width="40" class="text-center">

${child.children_count > 0 ?
                        '<span class="details-control-child" style="cursor:pointer;font-weight:bold;">+</span>'
                        : ''}

</td>

<td style="padding-left:20px;">└ ${child.nama}</td>

<td>${child.splitter ?? '-'}</td>

<td>${child.kapasitas ?? '-'}</td>

<td>${child.port_terpakai ?? 0}</td>

<td>

<a href="/odps/${child.id}"
class="btn btn-info btn-sm">
Detail
</a>

<button class="btn btn-warning btn-sm btn-edit"
data-id="${child.id}"
data-nama="${child.nama}"
data-odc="${child.odc_id}"
data-parent="${child.parent_odp_id}"
data-splitter="${child.splitter}"
data-kapasitas="${child.kapasitas}">
Edit
</button>

<button class="btn btn-danger btn-sm btn-hapus"
data-id="${child.id}">
Hapus
</button>

</td>

</tr>

`;

            });

            html += `</tbody></table>`;

            return html;

        }



        $(document).ready(function () {

            let table = $('#datatable').DataTable();



            //////////////////////////////////////////////////////
            // EXPAND LEVEL 1
            //////////////////////////////////////////////////////

            $('#datatable tbody').on('click', '.details-control', function () {

                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let id = tr.data('id');

                if (row.child.isShown()) {

                    row.child.hide();
                    $(this).text('+');

                } else {

                    $.get('/odps/' + id + '/children', function (data) {

                        if (data.length > 0) {

                            row.child(format(data)).show();
                            tr.find('.details-control').text('-');

                        }

                    });

                }

            });



            //////////////////////////////////////////////////////
            // EXPAND LEVEL 2 (ODP DIDALAM ODP)
            //////////////////////////////////////////////////////

            $(document).on('click', '.details-control-child', function () {

                let tr = $(this).closest('tr');
                let id = tr.data('id');

                if (tr.next().hasClass('child-row')) {

                    tr.next().remove();
                    $(this).text('+');

                } else {

                    let btn = $(this);

                    $.get('/odps/' + id + '/children', function (data) {

                        if (data.length > 0) {

                            let html = format(data);

                            tr.after('<tr class="child-row"><td colspan="6">' + html + '</td></tr>');

                            btn.text('-');

                        }

                    });

                }

            });



            //////////////////////////////////////////////////////
            // SPLITTER AUTO KAPASITAS
            //////////////////////////////////////////////////////

            $('#edit_splitter').change(function () {

                let kapasitasMap = {

                    "1:2": 2,
                    "1:4": 4,
                    "1:8": 8,
                    "1:16": 16

                };

                let val = $(this).val();

                if (kapasitasMap[val]) {

                    $('#edit_kapasitas').val(kapasitasMap[val]);

                }

            });



            //////////////////////////////////////////////////////
            // EDIT
            //////////////////////////////////////////////////////

            $(document).on('click', '.btn-edit', function () {

                let id = $(this).data('id');

                $('#edit_nama').val($(this).data('nama'));
                $('#edit_odc').val($(this).data('odc'));
                $('#edit_parent_odp').val($(this).data('parent'));
                $('#edit_splitter').val($(this).data('splitter'));
                $('#edit_kapasitas').val($(this).data('kapasitas'));

                $('#formEdit').attr('action', '/odps/' + id);

                $('#modalEdit').modal('show');

            });



            //////////////////////////////////////////////////////
            // HAPUS
            //////////////////////////////////////////////////////

            $(document).on('click', '.btn-hapus', function () {

                let id = $(this).data('id');

                $('#formHapus').attr('action', '/odps/' + id);

                $('#modalHapus').modal('show');

            });

        });

    </script>

</body>

</html>