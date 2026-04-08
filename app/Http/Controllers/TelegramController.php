<?php

namespace App\Http\Controllers;

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

        // simpan raw data telegram ke log
        Log::info('Telegram Raw Update', $data);

        if (!isset($data['message'])) {
            return response()->json(['status' => 'no message']);
        }

        $chatType = $data['message']['chat']['type'] ?? '';
        $chat_id  = $data['message']['chat']['id'] ?? null;
        $from_id  = $data['message']['from']['id'] ?? null;
        $text     = trim($data['message']['text'] ?? '');

        // log info user telegram
        Log::info('Telegram User Info', [
            'from_id' => $from_id,
            'chat_id' => $chat_id,
            'text' => $text
        ]);

        // hanya respon chat private
        if ($chatType !== 'private') {
            return response()->json(['status' => 'ignored']);
        }

        // command /start
        if ($text === '/start') {

            $msg  = "👋 Selamat datang\n\n";
            $msg .= "Untuk menghubungkan Telegram dengan sistem ISP.\n\n";
            $msg .= "📌 ID Telegram Anda:\n";
            $msg .= $from_id."\n\n";
            $msg .= "Silakan kirim ID ini ke admin untuk diaktifkan.";

            $this->telegram->sendToChat($chat_id, $msg);

            return response()->json(['status' => 'ok']);
        }

        // command /id
        if ($text === '/id') {

            $msg  = "🆔 ID Telegram Anda:\n\n";
            $msg .= $from_id."\n\n";
            $msg .= "Kirim ID ini ke admin.";

            $this->telegram->sendToChat($chat_id, $msg);

            return response()->json(['status' => 'ok']);
        }

        // jika command tidak dikenal
        $this->telegram->sendToChat(
            $chat_id,
            "Perintah tidak dikenali.\n\nGunakan:\n/start\n/id"
        );

        return response()->json(['status' => 'ok']);
    }
}