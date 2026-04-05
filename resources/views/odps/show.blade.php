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
                <h4>Detail ODP</h4>
                <a href="{{ route('odps.index') }}" class="btn btn-secondary">Kembali</a>
            </div>

            <!-- INFO ODP -->
            <div class="card mb-3">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-4">
                            @if($odp->foto)
                                <img src="{{ asset('storage/' . $odp->foto) }}" class="img-fluid">
                            @else
                                <p>Tidak ada foto</p>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <table class="table">
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $odp->nama }}</td>
                                </tr>
                                <tr>
                                    <th>ODC</th>
                                    <td>    {{ $pelanggan->odp->odc->nama ?? 'ODC tidak ada' }}
</td>
                                </tr>
                                <tr>
                                    <th>Splitter</th>
                                    <td>{{ $odp->splitter }}</td>
                                </tr>
                                <tr>
                                    <th>Kapasitas</th>
                                    <td>{{ $odp->kapasitas }}</td>
                                </tr>
                                <tr>
                                    <th>Terpakai</th>
                                    <td>
                                        {{ $odp->port_terpakai }} / {{ $odp->kapasitas }}

                                        @if($odp->port_terpakai >= $odp->kapasitas)
                                            <span class="badge bg-danger">FULL</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $odp->alamat }}</td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td>
                                        {{ $odp->lat }}, {{ $odp->lng }} <br>

                                        @if($odp->lat && $odp->lng)
                                            <a href="https://www.google.com/maps?q={{ $odp->lat }},{{ $odp->lng }}" 
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

            <!-- VISUAL PORT -->
            <div class="card mb-3">
                <div class="card-body">

                    <h5>Port ODP</h5>

                    <div class="d-flex flex-wrap">
                        @for($i = 1; $i <= $odp->kapasitas; $i++)
                            @php
                                $terpakai = $i <= $odp->port_terpakai;
                            @endphp

                            <div class="m-2 p-3 text-center border rounded 
                                {{ $terpakai ? 'bg-success text-white' : 'bg-danger text-white' }}"
                                style="width:70px;">
                                {{ $i }}
                            </div>
                        @endfor
                    </div>

                    <small class="text-muted">
                        Hijau = Terpakai, Merah = Kosong
                    </small>

                </div>
            </div>

            <!-- LIST PELANGGAN -->
            <div class="card">
                <div class="card-body">

                    <h5>Daftar Pelanggan</h5>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Port</th>
                                <th>Alamat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($odp->pelanggans as $p)
                            <tr>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->port }}</td>
                                <td>{{ $p->alamat }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">Belum ada pelanggan</td>
                            </tr>
                            @endforelse
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