<?php
namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {

        $data = $request->all();

        if (isset($data['message'])) {

            $chat_id = $data['message']['chat']['id'];
            $text = $data['message']['text'] ?? '';

            if ($text == '/start') {

                $this->kirimPesan($chat_id, 'Silakan kirim username PPPoE anda');

            } else {

                $pelanggan = Pelanggan::where('username_pppoe', $text)->first();

                if ($pelanggan) {

                    $pelanggan->update([
                        'chat_id' => $chat_id,
                    ]);

                    $this->kirimPesan($chat_id, '✅ Telegram berhasil terhubung');

                } else {

                    $this->kirimPesan($chat_id, '❌ Username tidak ditemukan');

                }

            }

        }

        return response()->json(['status' => 'ok']);

    }

    public function kirimPesan($chat_id, $text)
    {

        $token = '8633697355:AAHfmW8aTbUdg7qGhKZklzb_5HAKmwx5eoI';

        Http::post("https://api.telegram.org/bot$token/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $text,
        ]);

    }
}
