<?php

use App\Http\Controllers\HirarkiController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MikrotikController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\OdcController;
use App\Http\Controllers\OdpController;
use App\Http\Controllers\OltController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

// Kalau akses root, langsung ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route bawaan auth
Auth::routes(['register' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::resource('servers', ServerController::class);
Route::resource('olts', OltController::class);
Route::resource('odcs', OdcController::class);
Route::resource('odps', OdpController::class);
Route::resource('pelanggans', PelangganController::class);
Route::get('/hirarki', [HirarkiController::class, 'index'])->name('hirarki.index');
Route::get('/hirarki/data', [HirarkiController::class, 'data']);
Route::get('/pelanggan/{id}', [PelangganController::class, 'show']);
Route::get('/map', [MapController::class, 'index']);

Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');


Route::get('/odp/{id}/get-odc',[OdpController::class,'getOdc']);
Route::get('/odps/{id}/children',[OdpController::class,'children']);
Route::get('/get-pons/{olt_id}', function ($olt_id) {
    return \App\Models\Pon::where('olt_id', $olt_id)->get();
});
Route::get('/cek-pusher', function () {
    dd(config('broadcasting.connections.pusher'));
});


Route::post('/pelanggans/import', [PelangganController::class,'import'])
->name('pelanggans.import');
// Route::get('/test-pusher', function () {

//     event(new \App\Events\PppEvent([
//         'type' => 'remove',
//         'user' => 'test123'
//     ]));

//     return 'OK';
// });

// Route::get('/cek-broadcast', function () {
//     return [
//         'driver' => config('broadcasting.default'),
//         'pusher' => config('broadcasting.connections.pusher'),
//     ];
// });