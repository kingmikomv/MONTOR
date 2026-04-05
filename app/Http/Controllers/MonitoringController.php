<?php

namespace App\Http\Controllers;

use App\Events\PppEvent;
use App\Models\Odc;
use App\Models\Odp;
use App\Models\Olt;
use App\Models\Pelanggan;
use App\Models\Server;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    // ================= HALAMAN MAP =================
    public function index()
    {
        // SUMMARY
        $totalServer = Server::count();
        $totalOlt = Olt::count();
        $totalOdc = Odc::count();
        $totalOdp = Odp::count();
        $totalPelanggan = Pelanggan::count();

        // ODP STATUS
        $odpFull = Odp::whereColumn('port_terpakai', '>=', 'kapasitas')->count();

        $odpsWarning = Odp::whereRaw('port_terpakai >= kapasitas * 0.8')
            ->with('odc')
            ->get();

        // ================= MAP DATA =================

        // OLT
        $mapOlts = Olt::whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'nama', 'lat', 'lng')
            ->get();

        // ODC
        $mapOdcs = Odc::with('olt:id,nama,lat,lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'nama', 'lat', 'lng', 'olt_id')
            ->get();

        // ODP
        $mapOdps = Odp::with([
            'odc:id,nama,lat,lng',
            'parent:id,nama,lat,lng',
        ])
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(
                'id',
                'nama',
                'lat',
                'lng',
                'kapasitas',
                'port_terpakai',
                'odc_id',
                'parent_odp_id'
            )
            ->get();

        // 🔥 PELANGGAN (FIX PENTING)
        $mapPelanggan = Pelanggan::with('odp:id,nama,lat,lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select(
                'id',
                'nama',
                'username_pppoe', // 🔥 WAJIB
                'lat',
                'lng',
                'odp_id',
                    'status', // 🔥 WAJIB TAMBAH INI

            )
            ->get();
    $offlinePelanggan = Pelanggan::where('status','nonaktif')
    ->select(
        'nama',
        'username_pppoe',
        'status',
        'lat',
        'lng'
    )
    ->get();
        return view('ppp.index', compact(
            'totalServer',
            'totalOlt',
            'totalOdc',
            'totalOdp',
            'totalPelanggan',
            'odpFull',
            'odpsWarning',
            'mapOlts',
            'mapOdcs',
            'mapOdps',
            'mapPelanggan',
            'offlinePelanggan'
        ));
    }

    // ================= EVENT DARI MIKROTIK =================
    public function event(Request $request)
    {
        $user = $request->query('user');
        $status = $request->query('status');

        \Log::info('PPP EVENT MASUK', [
            'user' => $user,
            'status' => $status,
            'ip' => $request->ip(),
            'params' => $request->all(),
        ]);

        // validasi sederhana
        if (! $user || ! $status) {
            return response()->json([
                'status' => 'error',
                'message' => 'user/status kosong',
            ], 400);
        }

        // ================= ONLINE =================
        if ($status === 'online') {
            Pelanggan::where('username_pppoe', $user)
                ->update(['status' => 'aktif']);
            $payload = [
                'type' => 'add',
                'user' => $user,
            ];

            \Log::info('PPP BROADCAST ONLINE', $payload);

            event(new PppEvent($payload));
        }

        // ================= OFFLINE =================
        elseif ($status === 'offline') {
            Pelanggan::where('username_pppoe', $user)
                ->update(['status' => 'nonaktif']);
            $payload = [
                'type' => 'remove',
                'user' => $user,
            ];

            \Log::info('PPP BROADCAST OFFLINE', $payload);

            event(new PppEvent($payload));
        }

        return response()->json([
            'status' => 'ok',
            'user' => $user,
            'event' => $status,
        ]);
    }
}
