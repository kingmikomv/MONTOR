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
        $chat_id  = (string) ($data['message']['chat']['id'] ?? '');
        $from_id  = (string) ($data['message']['from']['id'] ?? '');
        $text     = trim($data['message']['text'] ?? '');

        // hanya respon private chat
        if ($chatType !== 'private') {
            return response()->json(['status' => 'ignored']);
        }

        // ===== START =====
        if ($text === '/start') {

            $msg  = "👋 Selamat datang\n\n";
            $msg .= "🆔 User ID Telegram Anda:\n";
            $msg .= $from_id . "\n\n";
            $msg .= "Untuk menghubungkan akun kirim:\n";
            $msg .= "/kirim tarsiwen@pamayahan";

            $this->telegram->sendToChat($chat_id, $msg);

            return response()->json(['status' => 'ok']);
        }

        // ===== /kirim username =====
        if (strpos($text, '/kirim') === 0) {

            $parts = explode(' ', $text);

            if (count($parts) < 2) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "❌ Format salah.\n\nContoh:\n/kirim tarsiwen@pamayahan"
                );

                return response()->json(['status' => 'ok']);
            }

            $username = trim($parts[1]);

            // cek username awal di database
            $pelanggan = Pelanggan::where('username_pppoe', $username)->first();

            if (!$pelanggan) {

                $this->telegram->sendToChat(
                    $chat_id,
                    "❌ Username tidak ditemukan di sistem."
                );

                return response()->json(['status' => 'ok']);
            }

            // format username baru
            $usernameBaru = $username . '@' . $from_id;

            // update database
            $pelanggan->update([
                'chat_id' => $from_id
            ]);

            $this->telegram->sendToChat(
                $chat_id,
                "✅ Akun berhasil dihubungkan\n\nUsername baru:\n" . $usernameBaru
            );

            return response()->json(['status' => 'ok']);
        }

        // ===== default =====
        $this->telegram->sendToChat(
            $chat_id,
            "Perintah tidak dikenal.\n\nGunakan:\n/start"
        );

        return response()->json(['status' => 'ok']);
    }
}