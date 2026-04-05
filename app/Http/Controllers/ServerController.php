<?php

namespace App\Http\Controllers;

use App\Models\Server;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::withCount('olts')->get();
        return view('servers.server', compact('servers'));
    }

    public function create()
    {
        return view('servers.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'lokasi' => 'nullable|string|max:255',
    ]);

    // 🔥 ambil angka terakhir
    $last = Server::orderBy('id', 'desc')->first();
    $number = $last ? $last->id + 1 : 1;

    // 🔥 format jadi SRV-01
    $kode = 'SRV-' . str_pad($number, 2, '0', STR_PAD_LEFT);

    Server::create([
        'nama' => $request->nama,
        'kode' => $kode,
        'lokasi' => $request->lokasi,
    ]);

    return redirect()->route('servers.index')
        ->with('success', 'Server berhasil ditambahkan');
}

    public function show($id)
    {
        $server = Server::with('olts')->findOrFail($id);
        return view('servers.show', compact('server'));
    }

    public function edit($id)
    {
        $server = Server::findOrFail($id);
        return view('servers.edit', compact('server'));
    }

    public function update(Request $request, $id)
    {
        $server = Server::findOrFail($id);
        $server->update($request->all());
        return redirect()->route('servers.index');
    }

    public function destroy($id)
    {
        Server::findOrFail($id)->delete();
        return back();
    }
}
