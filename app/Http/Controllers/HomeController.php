<?php

namespace App\Http\Controllers;

use App\Models\Odc;
use App\Models\Odp;
use App\Models\Olt;
use App\Models\Pelanggan;
use App\Models\Server;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        // ================= SUMMARY =================
        $totalServer = Server::count();
        $totalOlt = Olt::count();
        $totalOdc = Odc::count();
        $totalOdp = Odp::count();
        $totalPelanggan = Pelanggan::count();

        // ================= ODP STATUS =================
        $odpFull = Odp::whereColumn('port_terpakai', '>=', 'kapasitas')->count();

        $odpsWarning = Odp::whereRaw('port_terpakai >= kapasitas * 0.8')
            ->with('odc')
            ->get();

        // ================= CHART =================
        $olts = Olt::with(['odcs.odps.pelanggans'])->get();

        $chartOltLabels = [];
        $chartOltData = [];

        foreach ($olts as $olt) {

            $total = 0;

            foreach ($olt->odcs as $odc) {
                foreach ($odc->odps as $odp) {
                    $total += $odp->pelanggans->count();
                }
            }

            $chartOltLabels[] = $olt->nama;
            $chartOltData[] = $total;
        }

        // ================= MAP DATA =================

        // 🔵 OLT
        $mapOlts = Olt::whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'nama', 'lat', 'lng')
            ->get();

        // 🔵 ODC
        $mapOdcs = Odc::with('olt:id,nama,lat,lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'nama', 'lat', 'lng', 'olt_id')
            ->get();

        // 🟢 ODP (🔥 FIX UTAMA)
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

        // 🏠 Pelanggan
        $mapPelanggan = Pelanggan::with('odp:id,nama,lat,lng')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->select('id', 'nama', 'lat', 'lng', 'odp_id')
            ->get();

        // ================= RETURN =================
        return view('home', compact(
            'totalServer',
            'totalOlt',
            'totalOdc',
            'totalOdp',
            'totalPelanggan',
            'odpFull',
            'odpsWarning',
            'chartOltLabels',
            'chartOltData',
            'mapOlts',
            'mapOdcs',
            'mapOdps',
            'mapPelanggan'
        ));
    }
}
