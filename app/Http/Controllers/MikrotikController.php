<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class MikrotikController extends Controller
{
   public function event(Request $request)
{
    $request->validate([
        'server' => 'required',
        'user' => 'required',
        'status' => 'required'
    ]);

    $pelanggan = Pelanggan::where('username_pppoe', $request->user)->first();

    if (!$pelanggan) {
        \Log::warning('USER TIDAK DITEMUKAN', $request->all());
        return response()->json(['error' => 'User tidak ditemukan'], 404);
    }

    \Log::info('MIKROTIK EVENT', [
        'nama' => $pelanggan->nama,
        'user' => $request->user,
        'status' => $request->status
    ]);

    return response()->json([
        'nama' => $pelanggan->nama,
        'status' => $request->status
    ]);
}
}
