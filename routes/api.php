<?php

use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\TelegramController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/mikrotik/event', function (Request $request) {
//     return response()->json([
//         'status' => 'success',
//         'server' => $request->server,
//         'user' => $request->user,
//         'status_user' => $request->status,
//     ]);
// });


// Route::get('/mikrotik/event', function (Request $request) {
//     return response()->json([
//         'status' => 'success',
//         'user' => $request->user,
//     ]);
// });

// Route::get('/mikrotik/event', function (Request $request) {

//     Log::info('MikroTik Event Masuk', [
//         'server' => $request->server,
//         'user' => $request->user,
//         'status' => $request->status,
//         'ip' => $request->ip(),
//         'all' => $request->all(),
//     ]);

//     return response()->json([
//         'status' => 'success'
//     ]);
// });



Route::get('/mikrotik/event', [MonitoringController::class, 'event']);
Route::post('/telegram/webhook', [TelegramController::class, 'webhook']);