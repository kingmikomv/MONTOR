<!DOCTYPE html>
<html lang="en">

<x-head />

<body>

<x-navbar />

<div class="az-content az-content-dashboard">
    <div class="container">
        <div class="az-content-body">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Tambah Server</h4>
                <a href="{{ route('servers.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
            </div>

            <!-- Card Form -->
            <div class="card">
                <div class="card-body">

                    <!-- Alert Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('servers.store') }}" method="POST">
                        @csrf

                        <!-- Nama Server -->
                        <div class="form-group mb-3">
                            <label>Nama Server</label>
                            <input type="text" name="nama" class="form-control"
                                   placeholder="Contoh: Server Jakarta"
                                   value="{{ old('nama') }}" required>
                        </div>

                        <!-- Lokasi -->
                        <div class="form-group mb-3">
                            <label>Lokasi</label>
                            <textarea name="lokasi" class="form-control"
                                      placeholder="Alamat / lokasi server">{{ old('lokasi') }}</textarea>
                        </div>

                        <!-- Button -->
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                Simpan
                            </button>
                            <a href="{{ route('servers.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<x-end />

</body>
</html>