<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use App\Models\Odp;
class MapController extends Controller
{
   

public function index(Request $request)
{
    $odp_id = $request->odp_id;

    $odps = Odp::all(); // semua titik
    $selectedOdp = null;

    if ($odp_id) {
        $selectedOdp = Odp::find($odp_id);
    }

    return view('map.index', compact('odps', 'selectedOdp'));
}
}
