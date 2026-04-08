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
        $from_id  = $data['message']['from']['id'] ?? null;
        $text     = trim($data['message']['text'] ?? '');

        if ($chatType !== 'private') {
            return response()->json(['status' => 'ignored']);
        }

        // START
        if ($text === '/start') {

            $msg  = "👋 Selamat datang\n\n";
            $msg .= "🆔 User ID Telegram Anda:\n";
            $msg .= $from_id."\n\n";
            $msg .= "Kirim username Anda:\n";
            $msg .= "/kirim tarsiwen@pamayahan";

            $this->telegram->sendToChat($chat_id, $msg);

            return response()->json(['status' => 'ok']);
        }

        // /kirim username
        if (strpos($text, '/kirim') === 0) {

            $parts = explode(' ', $text);

            if (count($parts) < 2) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "Format salah.\n\nContoh:\n/kirim tarsiwen@pamayahan"
                );

                return response()->json(['status' => 'ok']);
            }

            $username = trim($parts[1]);

            // cek username awal
            $pelanggan = Pelanggan::where('username_pppoe', $username)->first();

            if (!$pelanggan) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "❌ Username tidak ditemukan"
                );

                return response()->json(['status' => 'ok']);
            }

            // update username dengan user_id
            $usernameBaru = $username . '@' . $from_id;

            $pelanggan->update([
                'username_pppoe' => $usernameBaru
            ]);

            $this->telegram->sendToChat(
                $chat_id,
                "✅ Akun berhasil dihubungkan\n\nUsername baru:\n".$usernameBaru
            );

            return response()->json(['status' => 'ok']);
        }

        $this->telegram->sendToChat(
            $chat_id,
            "Perintah tidak dikenal.\nGunakan:\n/start"
        );

        return response()->json(['status' => 'ok']);
    }
}