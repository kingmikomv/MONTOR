<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Services\TelegramService;

class TelegramController extends Controller
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function webhook(Request $request)
{
    $data = $request->all();
    \Log::info('Webhook data', $data); // lihat semua data mentah

    if (isset($data['message'])) {
        $chat_id = $data['message']['chat']['id'];

        // Tolak chat_id negatif
        if ($chat_id < 0) {
            \Log::warning('Ditolak: chat_id negatif', ['chat_id' => $chat_id]);
            return response()->json(['status' => 'ignored']);
        }

        // ... proses seperti biasa
    }
    return response()->json(['status' => 'ok']);
}
}