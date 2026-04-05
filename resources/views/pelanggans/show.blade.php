<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

<x-navbar />

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">

            <!-- Header -->
            <div class="d-flex justify-content-between mb-3">
                <h4>Detail Pelanggan</h4>
                <a href="{{ route('pelanggans.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- INFO PELANGGAN -->
            <div class="card mb-3">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            @if($pelanggan->foto)
                                <img src="{{ asset('storage/' . $pelanggan->foto) }}" class="img-fluid">
                            @else
                                <p>Tidak ada foto</p>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $pelanggan->nama }}</td>
                                </tr>
                                <tr>
                                    <th>ODP</th>
                                    <td>{{ $pelanggan->odp->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Port</th>
                                    <td>{{ $pelanggan->port }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $pelanggan->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td>
                                        {{ $pelanggan->lat }}, {{ $pelanggan->lng }} <br>

                                        @if($pelanggan->lat && $pelanggan->lng)
                                            <a href="https://www.google.com/maps?q={{ $pelanggan->lat }},{{ $pelanggan->lng }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-primary mt-1">
                                                📍 Google Maps
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <!-- JALUR JARINGAN -->
            <div class="card">
                <div class="card-body">

                    <h5>Jalur Jaringan</h5>

                    <table class="table table-bordered">
                        <tr>
                            <th>OLT</th>
                            <td>{{ $pelanggan->odp->odc->olt->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>PON</th>
                            <td>PON {{ $pelanggan->odp->odc->pon->pon_number ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>ODC</th>
                            <td>    {{ $pelanggan->odp->odc->nama ?? 'ODC tidak ada' }}
</td>
                        </tr>
                        <tr>
                            <th>ODP</th>
                            <td>{{ $pelanggan->odp->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Port</th>
                            <td>{{ $pelanggan->port }}</td>
                        </tr>
                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<x-end />

</body>
</html>