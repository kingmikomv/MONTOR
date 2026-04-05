<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

<x-navbar />

<div class="container">
    <div class="az-content az-content-dashboard">
        <div class="az-content-body">

            <!-- Header -->
            <div class="d-flex justify-content-between mb-3">
                <h4>Detail ODC</h4>
                <a href="{{ route('odcs.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- INFO ODC -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($odc->foto)
                                <img src="{{ asset('storage/' . $odc->foto) }}" class="img-fluid rounded" style="max-height:250px;">
                            @else
                                <p class="text-muted">Tidak ada foto</p>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <table class="table table-sm">
                                <tr>
                                    <th width="150">Nama</th>
                                    <td>{{ $odc->nama }}</td>
                                </tr>
                                <tr>
                                    <th>OLT</th>
                                    <td>{{ $odc->olt->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>PON</th>
                                    <td>PON {{ $odc->pon->pon_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Splitter</th>
                                    <td>{{ $odc->splitter }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $odc->kapasitas }}</td>
                                </tr>
                                <tr>
                                    <th>Terpakai</th>
                                    <td>
                                        <b>{{ $odc->port_terpakai }}</b> / {{ $odc->kapasitas }}

                                        @if($odc->port_terpakai >= $odc->kapasitas)
                                            <span class="badge bg-danger">FULL</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $odc->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td>
                                        <a href="https://www.google.com/maps?q={{ $odc->lat }},{{ $odc->lng }}"
                                           target="_blank"
                                           class="btn btn-sm btn-primary">
                                            📍 Google Maps
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- TREE ODP -->
            <div class="card shadow-sm">
                <div class="card-body">

                    <h5 class="mb-3">Struktur ODP</h5>

                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama ODP</th>
                                <th width="120">Splitter</th>
                                <th width="120">Kapasitas</th>
                                <th width="120">Terpakai</th>
                            </tr>
                        </thead>
                        <tbody>

                        @php
                            function renderOdp($odps, $level = 0) {
                                foreach ($odps as $odp) {

                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level) . ($level > 0 ? '├─ ' : '');

                                    echo '<tr>';
                                    echo '<td>' . $indent . e($odp->nama) . '</td>';
                                    echo '<td>' . e($odp->splitter) . '</td>';
                                    echo '<td>' . e($odp->kapasitas) . '</td>';
                                    echo '<td><b>' . e($odp->port_terpakai) . '</b></td>';
                                    echo '</tr>';

                                    if ($odp->childrenRecursive && $odp->childrenRecursive->count()) {
                                        renderOdp($odp->childrenRecursive, $level + 1);
                                    }
                                }
                            }
                        @endphp

                        @if($odc->odps->count())
                            @php renderOdp($odc->odps); @endphp
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada ODP</td>
                            </tr>
                        @endif

                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<x-end />

</body>
</html>