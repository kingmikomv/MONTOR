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

        if (isset($data['message'])) {
            $chat_id = $data['message']['from']['id'] ?? null;
            $text = $data['message']['text'] ?? '';

            if ($text == '/start') {
                $this->telegram->sendToChat($chat_id, 'Silakan kirim username PPPoE anda');
            } else {
                $pelanggan = Pelanggan::where('username_pppoe', $text)->first();

                if ($pelanggan) {
                    $pelanggan->update(['chat_id' => $chat_id]);
                    $this->telegram->sendToChat($chat_id, '✅ Telegram berhasil terhubung');
                } else {
                    $this->telegram->sendToChat($chat_id, '❌ Username tidak ditemukan');
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }
}