<?php

namespace App\Http\Controllers;

use App\Models\Odc;
use App\Models\Odp;
use App\Models\Olt;
use App\Models\Pon;
use Illuminate\Http\Request;

class OdcController extends Controller
{
    public function index()
    {
        $odcs = Odc::with('olt', 'pon')->get();

        return view('odcs.index', compact('odcs'));
    }

    public function create()
    {
        $olts = Olt::all();
        $pons = Pon::all();

        return view('odcs.create', compact('olts', 'pons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'olt_id' => 'required',
            'pon_id' => 'required',
            'nama' => 'required',
            'splitter' => 'required',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // ambil kapasitas dari splitter
        $kapasitas = explode(':', $request->splitter)[1];

        // 🔥 upload foto
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('odc', 'public');
        }

        Odc::create([
            'olt_id' => $request->olt_id,
            'pon_id' => $request->pon_id,
            'nama' => $request->nama,
            'splitter' => $request->splitter,
            'kapasitas' => $kapasitas,
            'alamat' => $request->alamat,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'foto' => $fotoPath, // 🔥 simpan foto
        ]);

        return redirect()->route('odcs.index')
            ->with('success', 'ODC berhasil ditambahkan');
    }

   public function show($id)
{
    $odc = Odc::with([
        'olt',
        'pon',
        'odps' => function ($q) {
            $q->whereNull('parent_odp_id'); // ⬅️ ini kunci utama
        },
        'odps.childrenRecursive' // ⬅️ biar child tetap ke-load
    ])->findOrFail($id);

    return view('odcs.show', compact('odc'));
}
// private function loadTree($odps)
// {
//     foreach ($odps as $odp) {
//         $odp->childrenRecursive = $odp->children()->get();
//         $this->loadTree($odp->childrenRecursive);
//     }
// }



    public function update(Request $request, $id)
    {
        $odc = Odc::findOrFail($id);
        $odc->update($request->all());

        return redirect()->back()->with('success', 'Berhasil update');
    }

    public function destroy($id)
    {
        Odc::destroy($id);

        return redirect()->back()->with('success', 'Berhasil hapus');
    }
}
