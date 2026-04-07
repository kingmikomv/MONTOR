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

    // Kirim ke chat_id tertentu (sudah diperbaiki)
    public function sendToChat($chat_id, $text)
    {
        if (!$chat_id) return false;

        try {
            $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $chat_id,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->successful()) {
                return true;
            }

            // Tangani error dari Telegram
            $errorBody = $response->json();
            $errorDesc = $errorBody['description'] ?? '';

            \Log::error('Telegram response failed', [
                'chat_id' => $chat_id,
                'body' => $response->body(),
            ]);

            // Jika chat tidak ditemukan atau bot tidak punya akses, hapus chat_id dari database
            if (str_contains($errorDesc, 'chat not found') || 
                str_contains($errorDesc, 'bot was blocked') ||
                str_contains($errorDesc, 'user deactivated')) {
                $this->removeInvalidChatId($chat_id);
            }

            return false;

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
        Pelanggan::where('chat_id', $chat_id)->update(['chat_id' => null]);
        \Log::info('Chat_id tidak valid telah dihapus', ['chat_id' => $chat_id]);
    }

    // Broadcast ke semua pelanggan (tetap sama, tidak perlu diubah)
    public function broadcastToPelanggan($text)
    {
        $pelanggans = Pelanggan::whereNotNull('chat_id')->get();

        foreach ($pelanggans as $p) {
            $this->sendToChat($p->chat_id, $text);
            usleep(300000);
        }
    }

    // Notifikasi PPPoE pelanggan (tetap sama)
    public function notifyPppoe($pelanggan, $status)
    {
        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda *tidak aktif*. Mohon menunggu perbaikan."
            : "✅ Internet Normal Kembali\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda sekarang *aktif*.";

        $this->sendToChat($pelanggan->chat_id, $msg);
    }

    // Notifikasi ISP pusat ke semua pelanggan (tetap sama)
    public function notifyIsp($status)
    {
        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet Pusat\n\nKoneksi ISP sedang *tidak aktif*.\nMohon bersabar, teknisi sedang memeriksa."
            : "✅ Internet Pusat Normal Kembali\n\nKoneksi ISP sekarang *aktif*.";

        $this->broadcastToPelanggan($msg);
    }
}