<?php

namespace App\Http\Controllers;

use App\Models\Odc;
use App\Models\Odp;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Imports\PelangganImport;
use Maatwebsite\Excel\Facades\Excel;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with('odp')->get();
        $odps = Odp::all(); // 🔥 WAJIB buat dropdown edit
        $odcs = Odc::all(); // 🔥 INI YANG KURANG

        return view('pelanggans.index', compact('pelanggans', 'odps', 'odcs'));
    }

    public function create()
    {
        $odps = Odp::with('odc')->get();

        return view('pelanggans.create', compact('odps'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'odp_id' => 'required',
        'username_pppoe' => 'required|unique:pelanggans,username_pppoe', // 🔥 TAMBAHAN
    ]);

    $odp = Odp::findOrFail($request->odp_id);

    // 🔥 CEK PORT ODP
    if ($odp->port_terpakai >= $odp->kapasitas) {
        return back()->with('error', 'Port ODP sudah penuh!');
    }

    // upload foto
    $foto = null;
    if ($request->hasFile('foto')) {
        $foto = $request->file('foto')->store('pelanggan', 'public');
    }

    Pelanggan::create([
        'nama' => $request->nama,
        'username_pppoe' => $request->username_pppoe, // 🔥 TAMBAHAN
        'odp_id' => $request->odp_id,
        'alamat' => $request->alamat,
        'lat' => $request->lat,
        'lng' => $request->lng,
        'foto' => $foto,
        'port' => $odp->port_terpakai + 1,
    ]);

    // 🔥 TAMBAH PORT TERPAKAI ODP
    $odp->increment('port_terpakai');

    return redirect()->route('pelanggans.index')
        ->with('success', 'Pelanggan berhasil ditambahkan');
}
    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            // 🔍 ambil data pelanggan
            $pelanggan = Pelanggan::findOrFail($id);

            $odpId = $pelanggan->odp_id;

            // 🗑️ hapus pelanggan
            $pelanggan->delete();

            // 🔥 kurangi port terpakai
            if ($odpId) {
                Odp::where('id', $odpId)->decrement('port_terpakai');
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data pelanggan berhasil dihapus');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->withErrors([
                'error' => 'Gagal hapus: '.$e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::with(
            'odp.odc.olt',
            'odp.odc.pon'
        )->findOrFail($id);

        return view('pelanggans.show', compact('pelanggan'));
    }



public function update(Request $request, $id)
{
    $pelanggan = Pelanggan::findOrFail($id);

    $request->validate([
        'nama' => 'required',
        'odp_id' => 'required|exists:odps,id',
        'port' => 'required|numeric|min:1',
        'lat' => 'required',
        'lng' => 'required',
        'alamat' => 'required',
    ]);

    DB::beginTransaction();

    try {

        $oldOdpId = $pelanggan->odp_id;
        $odpBaru = Odp::findOrFail($request->odp_id);

        // ❗ CEK PORT MELEBIHI KAPASITAS
        if ($request->port > $odpBaru->kapasitas) {
            return back()->withErrors([
                'port' => 'Port melebihi kapasitas ODP'
            ]);
        }

        // ❗ CEK PORT SUDAH DIGUNAKAN
        $cekPort = Pelanggan::where('odp_id', $request->odp_id)
            ->where('port', $request->port)
            ->where('id', '!=', $id)
            ->exists();

        if ($cekPort) {
            return back()->withErrors([
                'port' => 'Port sudah digunakan di ODP ini'
            ]);
        }

        // ❗ CEK ODP PENUH
        if ($oldOdpId != $request->odp_id && $odpBaru->port_terpakai >= $odpBaru->kapasitas) {
            return back()->withErrors([
                'odp_id' => 'ODP tujuan sudah penuh'
            ]);
        }

        // 💾 UPDATE DATA
        $pelanggan->update([
            'nama' => $request->nama,
            'odp_id' => $request->odp_id,
            'port' => $request->port,
            'alamat' => $request->alamat,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ]);

        // 🔥 UPDATE PORT TERPAKAI
        if ($oldOdpId != $request->odp_id) {

            Odp::where('id', $oldOdpId)
                ->where('port_terpakai', '>', 0)
                ->decrement('port_terpakai');

            Odp::where('id', $request->odp_id)
                ->increment('port_terpakai');
        }

        DB::commit();

        return redirect()->route('pelanggans.index')
            ->with('success', 'Data pelanggan berhasil diupdate');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()->withErrors([
            'error' => 'Terjadi kesalahan: '.$e->getMessage()
        ]);
    }
}



public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(new PelangganImport, $request->file('file'));

    return back()->with('success','Data pelanggan berhasil diimport');
}
}
