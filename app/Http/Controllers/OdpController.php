<?php

namespace App\Http\Controllers;

use App\Models\Odc;
use App\Models\Odp;
use Illuminate\Http\Request;

class OdpController extends Controller
{
    public function index()
    {
        $odps = Odp::with('odc')
            ->withCount('children')
            ->get();

        $odcs = Odc::all();

        return view('odps.index', compact('odps', 'odcs'));
    }

    public function create()
    {
        $odcs = Odc::all();
        $odps = Odp::all();

        return view('odps.create', compact('odcs', 'odps'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'splitter' => 'required',
        ]);

        $kapasitas = explode(':', $request->splitter)[1];

        $odc_id = null;
        $parent_odp_id = null;

        // //////////////////////////////////////////////////
        // SUMBER DARI ODC
        // //////////////////////////////////////////////////

        if ($request->filled('odc_id')) {

            $odc = Odc::findOrFail($request->odc_id);

            if ($odc->port_terpakai >= $odc->kapasitas) {
                return back()->with('error', 'Port ODC sudah penuh!');
            }

            $odc_id = $odc->id;

            $odc->increment('port_terpakai');
        }

        // //////////////////////////////////////////////////
        // SUMBER DARI ODP
        // //////////////////////////////////////////////////

        if ($request->filled('parent_odp_id')) {

            $parent = Odp::findOrFail($request->parent_odp_id);

            if ($parent->port_terpakai >= $parent->kapasitas) {
                return back()->with('error', 'Port ODP sumber sudah penuh!');
            }

            $parent_odp_id = $parent->id;

            $parent->increment('port_terpakai');
        }

        // //////////////////////////////////////////////////
        // SIMPAN
        // //////////////////////////////////////////////////

        Odp::create([
            'odc_id' => $odc_id,
            'parent_odp_id' => $parent_odp_id,
            'nama' => $request->nama,
            'splitter' => $request->splitter,
            'kapasitas' => $kapasitas,
            'alamat' => $request->alamat,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'port_terpakai' => 0,
        ]);

        return redirect()->route('odps.index')
            ->with('success', 'ODP berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $odp = Odp::findOrFail($id);

        if ($odp->parent_odp_id) {
            Odp::where('id', $odp->parent_odp_id)->decrement('port_terpakai');
        }

        if ($odp->odc_id) {
            Odc::where('id', $odp->odc_id)->decrement('port_terpakai');
        }

        $odp->delete();

        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }

    public function show($id)
    {
        $odp = Odp::with('odc', 'pelanggans')->findOrFail($id);

        return view('odps.show', compact('odp'));
    }

    public function update(Request $request, $id)
    {
        $odp = Odp::findOrFail($id);

        $old_parent = $odp->parent_odp_id;
        $old_odc = $odp->odc_id;

        $new_parent = $request->parent_odp_id;
        $new_odc = $request->odc_id;

        // //////////////////////////////////////////////////
        // KURANGI PORT SUMBER LAMA
        // //////////////////////////////////////////////////

        if ($old_parent) {
            Odp::where('id', $old_parent)->decrement('port_terpakai');
        }

        if ($old_odc) {
            Odc::where('id', $old_odc)->decrement('port_terpakai');
        }

        // //////////////////////////////////////////////////
        // TAMBAH PORT SUMBER BARU
        // //////////////////////////////////////////////////

        if ($new_parent) {

            $parent = Odp::findOrFail($new_parent);

            if ($parent->port_terpakai >= $parent->kapasitas) {
                return back()->with('error', 'Port ODP tujuan penuh!');
            }

            $parent->increment('port_terpakai');
        }

        if ($new_odc) {

            $odc = Odc::findOrFail($new_odc);

            if ($odc->port_terpakai >= $odc->kapasitas) {
                return back()->with('error', 'Port ODC tujuan penuh!');
            }

            $odc->increment('port_terpakai');
        }

        // //////////////////////////////////////////////////
        // UPDATE DATA
        // //////////////////////////////////////////////////

        $odp->update([
            'nama' => $request->nama,
            'odc_id' => $new_odc,
            'parent_odp_id' => $new_parent,
            'splitter' => $request->splitter,
            'kapasitas' => $request->kapasitas,
        ]);

        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function children($id)
    {
        $children = Odp::where('parent_odp_id', $id)
            ->withCount('children')
            ->get();

        return response()->json($children);
    }

    public function getOdc($id)
    {
        $odp = Odp::find($id);

        return response()->json([
            'odc_id' => $odp->odc_id,
        ]);
    }
}
