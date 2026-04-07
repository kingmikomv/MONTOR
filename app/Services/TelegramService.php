<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Pelanggan;

class TelegramService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    // Kirim ke chat_id tertentu
    public function sendToChat($chat_id, $text)
    {
        if (!$chat_id) return false;

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            if (!$response->successful()) {
                $errorBody = $response->json();
                \Log::error('Telegram response failed', [
                    'chat_id' => $chat_id,
                    'body' => $response->body(),
                ]);

                // Jika error karena chat not found, hapus chat_id dari database
                if (isset($errorBody['description']) && 
                    (str_contains($errorBody['description'], 'chat not found') ||
                     str_contains($errorBody['description'], 'bot was blocked') ||
                     str_contains($errorBody['description'], 'user deactivated'))) {
                    $this->removeInvalidChatId($chat_id);
                }

                return false;
            }

            return true;

        } catch (\Exception $e) {
            \Log::error('Telegram send failed', [
                'chat_id' => $chat_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // Hapus chat_id yang tidak valid dari tabel pelanggan
    private function removeInvalidChatId($chat_id)
    {
        try {
            $updated = Pelanggan::where('chat_id', $chat_id)->update(['chat_id' => null]);
            if ($updated) {
                \Log::info('Chat_id tidak valid telah dihapus', ['chat_id' => $chat_id]);
            }
        } catch (\Exception $e) {
            \Log::error('Gagal menghapus chat_id', ['chat_id' => $chat_id, 'error' => $e->getMessage()]);
        }
    }

    // Broadcast ke semua pelanggan
    public function broadcastToPelanggan($text)
    {
        $pelanggans = Pelanggan::whereNotNull('chat_id')->get();

        foreach ($pelanggans as $p) {
            $this->sendToChat($p->chat_id, $text);
            usleep(300000); // delay 0.3 detik anti flood telegram
        }
    }

    // Notifikasi PPPoE pelanggan
    public function notifyPppoe($pelanggan, $status)
    {
        if (!$pelanggan->chat_id) return false;

        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda *tidak aktif*. Mohon menunggu perbaikan."
            : "✅ Internet Normal Kembali\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda sekarang *aktif*.";

        return $this->sendToChat($pelanggan->chat_id, $msg);
    }

    // Notifikasi ISP pusat ke semua pelanggan
    public function notifyIsp($status)
    {
        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet Pusat\n\nKoneksi ISP sedang *tidak aktif*.\nMohon bersabar, teknisi sedang memeriksa."
            : "✅ Internet Pusat Normal Kembali\n\nKoneksi ISP sekarang *aktif*.";

        $this->broadcastToPelanggan($msg);
    }
}