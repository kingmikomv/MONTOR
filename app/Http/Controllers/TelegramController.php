<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        Log::info('Telegram Raw Update', $data);

        if (!isset($data['message'])) {
            return response()->json(['status' => 'no message']);
        }

        $chatType = $data['message']['chat']['type'] ?? '';
        $chat_id = $data['message']['chat']['id'] ?? null;
        $from_id = $data['message']['from']['id'] ?? null;
        $text = trim($data['message']['text'] ?? '');

        // log id user
        Log::info('Telegram User Info', [
            'from_id' => $from_id,
            'chat_id' => $chat_id,
            'text' => $text
        ]);

        if ($chatType !== 'private') {
            return response()->json(['status' => 'ignored']);
        }

        if ($text === '/start') {
            $this->telegram->sendToChat($chat_id, 'Silakan kirim username PPPoE anda');
            return response()->json(['status' => 'ok']);
        }

        $pelanggan = Pelanggan::where('username_pppoe', $text)->first();

        if ($pelanggan) {

            $pelanggan->update([
                'chat_id' => $from_id
            ]);

            $this->telegram->sendToChat($chat_id, '✅ Telegram berhasil terhubung');

        } else {

            $this->telegram->sendToChat($chat_id, '❌ Username tidak ditemukan');

        }

        return response()->json(['status' => 'ok']);
    }
}