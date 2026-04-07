<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

    <x-navbar />

    <div class="container">

        <div class="az-content az-content-dashboard">
            <div class="az-content-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Data Server</h4>
                    <a href="{{ route('servers.create') }}" class="btn btn-primary">+ Tambah Server</a>
                </div>

                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Server</th>
                                <th>Lokasi</th>
                                <th>Jumlah OLT</th>
                                <th>Script</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servers as $key => $server)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $server->nama }}</td>
                                    <td>{{ $server->lokasi ?? '-' }}</td>
                                    <td>{{ $server->olts_count }}</td>

                                    <!-- 🔥 BUTTON SCRIPT -->
                                    <td>
                                        <button class="btn btn-success btn-sm btn-script" data-kode="{{ $server->kode }}">
                                            Script MikroTik
                                        </button>
                                    </td>

                                    <td>
                                        <div class="action-buttons">

                                            <a href="{{ route('servers.show', $server->id) }}"
                                                class="btn btn-action btn-info">
                                                <i class="fa fa-eye"></i>
                                                <span>Detail</span>
                                            </a>

                                            <a href="{{ route('servers.edit', $server->id) }}"
                                                class="btn btn-action btn-warning">
                                                <i class="fa fa-edit"></i>
                                                <span>Edit</span>
                                            </a>

                                            <form action="{{ route('servers.destroy', $server->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-action btn-danger"
                                                    onclick="return confirm('Hapus data?')">
                                                    <i class="fa fa-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Data tidak ada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <!-- 🔥 MODAL SCRIPT -->
    <div class="modal fade" id="modalScript" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Script MikroTik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <label>ON-UP</label>
                    <textarea id="scriptUp" class="form-control mb-3" rows="3" readonly></textarea>

                    <label>ON-DOWN</label>
                    <textarea id="scriptDown" class="form-control" rows="3" readonly></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="copyScript()">Copy Semua</button>
                </div>

            </div>
        </div>
    </div>

    <x-end />

    <!-- 🔥 LIBRARY -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 🔥 DATATABLE -->
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

    <!-- 🔥 SCRIPT MIKROTIK -->
    <script>
        $(document).on('click', '.btn-script', function () {

            let kode = $(this).data('kode');
            let domain = window.location.origin;

            let scriptUp = `:local user $user
/tool fetch url="${domain}/mikrotik/event?server=${kode}&user=$user&status=online" keep-result=no`;

            let scriptDown = `:local server "${kode}"
:local u $user

:local retry 0
:local maxRetry 5

:while ($retry < $maxRetry) do={

    :local cek [/ppp active find where name=$u]

    :if ([:len $cek] = 0) do={

        /tool fetch url=("${domain}/api/mikrotik/event?server=" . $server . "&user=" . $u . "&status=offline") keep-result=no

        :set retry $maxRetry
    } else={
        :delay 2
        :set retry ($retry + 1)
    }
}`;

            $('#scriptUp').val(scriptUp);
            $('#scriptDown').val(scriptDown);

            $('#modalScript').modal('show');
        });

        function copyScript() {
            let text = $('#scriptUp').val() + "\n\n" + $('#scriptDown').val();

            navigator.clipboard.writeText(text).then(() => {
                alert('Script berhasil dicopy!');
            });
        }
    </script>

</body>

</html>