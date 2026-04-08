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
        $chat_id  = $data['message']['chat']['id'] ?? null;
        $text     = trim($data['message']['text'] ?? '');

        if ($chatType !== 'private') {
            return response()->json(['status' => 'ignored']);
        }

        // /start
        if ($text === '/start') {

            $msg  = "👋 Selamat datang\n\n";
            $msg .= "Untuk menghubungkan Telegram dengan sistem.\n\n";
            $msg .= "Gunakan perintah:\n";
            $msg .= "/kirim username_pppoe\n\n";
            $msg .= "Contoh:\n";
            $msg .= "/kirim ariinet01";

            $this->telegram->sendToChat($chat_id, $msg);

            return response()->json(['status' => 'ok']);
        }

        // command /kirim
        if (strpos($text, '/kirim') === 0) {

            $parts = explode(' ', $text);

            if (count($parts) < 2) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "❌ Format salah\n\nContoh:\n/kirim username_pppoe"
                );

                return response()->json(['status' => 'ok']);
            }

            $username = trim($parts[1]);

            $pelanggan = Pelanggan::where('username_pppoe', $username)->first();

            if (!$pelanggan) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "❌ Username PPPoE tidak ditemukan"
                );

                return response()->json(['status' => 'ok']);
            }

            // simpan chat_id ke database
            $pelanggan->update([
                'chat_id' => $chat_id
            ]);

            $this->telegram->sendToChat(
                $chat_id,
                "✅ Telegram berhasil terhubung\n\nUsername: ".$username
            );

            return response()->json(['status' => 'ok']);
        }

        $this->telegram->sendToChat(
            $chat_id,
            "Perintah tidak dikenali.\nGunakan:\n/kirim username_pppoe"
        );

        return response()->json(['status' => 'ok']);
    }
}