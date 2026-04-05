<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Odp;

class HirarkiController extends Controller
{

    public function index()
    {
        return view('hirarki.index');
    }

    public function data()
    {

        $servers = Server::with('olts.odcs.odps')->get();

        $tree = [];

        foreach ($servers as $server) {

            $serverNode = [
                "id" => "server_" . $server->id,
                "text" => "🖥️ " . $server->nama,
                "children" => []
            ];

            foreach ($server->olts as $olt) {

                $oltNode = [
                    "id" => "olt_" . $olt->id,
                    "text" => "📡 " . $olt->nama,
                    "children" => []
                ];

                foreach ($olt->odcs as $odc) {

                    $odcNode = [
                        "id" => "odc_" . $odc->id,
                        "text" => "📦 " . $odc->nama,
                        "children" => []
                    ];

                    foreach ($odc->odps->whereNull('parent_odp_id') as $odp) {

                        $odcNode['children'][] = $this->buildOdpTree($odp);

                    }

                    $oltNode['children'][] = $odcNode;
                }

                $serverNode['children'][] = $oltNode;
            }

            $tree[] = $serverNode;
        }

        return response()->json($tree);
    }


    private function buildOdpTree($odp)
    {

        $node = [
            "id" => "odp_" . $odp->id,
            "text" => "📍 " . $odp->nama,
            "children" => []
        ];

        // CHILD ODP
        foreach ($odp->children as $child) {

            $node['children'][] = $this->buildOdpTree($child);

        }

        // PELANGGAN
        foreach ($odp->pelanggans as $pelanggan) {

            $node['children'][] = [
                "id" => "pelanggan_" . $pelanggan->id,
                "text" => "👤 " . $pelanggan->nama,
                "icon" => "jstree-file"
            ];

        }

        return $node;

    }

}