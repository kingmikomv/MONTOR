<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Log;

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

        // Log semua data masuk untuk debugging
        Log::info('Webhook received', $data);

        if (!isset($data['message'])) {
            return response()->json(['status' => 'ok']);
        }

        $chat = $data['message']['chat'];
        $chat_id = $chat['id'];
        $chat_type = $chat['type'] ?? 'unknown';
        $text = $data['message']['text'] ?? '';

        // 1. Hanya proses private chat (bukan grup/supergroup/channel)
        if ($chat_type !== 'private') {
            Log::warning('Non-private chat ignored', [
                'chat_id' => $chat_id,
                'type' => $chat_type,
            ]);
            return response()->json(['status' => 'ignored']);
        }

        // 2. Pastikan chat_id positif (private chat seharusnya positif, tapi jaga-jaga)
        if ($chat_id < 0) {
            Log::error('Negative chat_id in private chat', ['chat_id' => $chat_id]);
            return response()->json(['status' => 'error']);
        }

        // Proses perintah
        if ($text === '/start') {
            $this->telegram->sendToChat($chat_id, 'Silakan kirim username PPPoE anda');
        } else {
            $pelanggan = Pelanggan::where('username_pppoe', $text)->first();

            if ($pelanggan) {
                // Simpan chat_id yang valid (positif)
                $pelanggan->update(['chat_id' => $chat_id]);
                $this->telegram->sendToChat($chat_id, '✅ Telegram berhasil terhubung');
                Log::info('Chat_id saved for pelanggan', [
                    'username' => $pelanggan->username_pppoe,
                    'chat_id' => $chat_id,
                ]);
            } else {
                $this->telegram->sendToChat($chat_id, '❌ Username tidak ditemukan');
            }
        }

        return response()->json(['status' => 'ok']);
    }
}