<?php

namespace App\Http\Controllers;

use App\Models\Olt;
use App\Models\Server;
use Illuminate\Http\Request;

class OltController extends Controller
{
    public function index()
    {
        $olts = Olt::with('server')->get();
        $servers = Server::all();
        return view('olts.index', compact('olts', 'servers'));
    }

    public function create()
    {
        $servers = Server::all();
        return view('olts.create', compact('servers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'server_id' => 'required',
            'nama' => 'required',
            'tipe' => 'required',
            'jumlah_pon' => 'required|numeric'
        ]);

        $olt = Olt::create($request->all());

        // auto generate PON
        for ($i = 1; $i <= $request->jumlah_pon; $i++) {
            \App\Models\Pon::create([
                'olt_id' => $olt->id,
                'pon_number' => $i
            ]);
        }

        return redirect()->route('olts.index');
    }
    public function update(Request $request, $id)
{
    $olt = Olt::findOrFail($id);

    $olt->update($request->all());

    return back()->with('success', 'OLT berhasil diupdate');
}

public function destroy($id)
{
    Olt::findOrFail($id)->delete();

    return back()->with('success', 'OLT berhasil dihapus');
}
}