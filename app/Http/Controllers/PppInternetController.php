<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TelegramService;

class PppInternetController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function updateStatus(Request $request)
{
    $status = strtolower(trim($request->input('status')));

    if (!in_array($status, ['down','up'])) {
        return response()->json([
            'error' => 'Invalid',
            'received' => $status
        ],400);
    }

    $lastStatus = cache()->get('isp_status');

    if ($lastStatus === $status) {
        return response()->json(['status'=>'no_change']);
    }

    cache()->put('isp_status', $status, 300);

    $this->telegram->notifyIsp($status);

    return response()->json(['status'=>'ok']);
}
}