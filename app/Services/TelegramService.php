<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
    }

    /**
     * Kirim pesan ke chat_id tertentu (hanya personal chat)
     *
     * @param int|string $chat_id
     * @param string $text
     * @return bool
     */
    public function sendToChat($chat_id, $text)
    {
        // Tolak chat_id kosong
        if (empty($chat_id)) {
            Log::warning('sendToChat: chat_id kosong');
            return false;
        }

        // Tolak chat_id negatif (grup/channel)
        if ($chat_id < 0) {
            Log::error('sendToChat: mencoba kirim ke chat_id negatif', ['chat_id' => $chat_id]);
            // Hapus dari database jika terlanjur tersimpan
            Pelanggan::where('chat_id', $chat_id)->update(['chat_id' => null]);
            return false;
        }

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
            $errorDesc = $errorBody['description'] ?? 'Unknown error';

            Log::error('Telegram API error', [
                'chat_id' => $chat_id,
                'error_code' => $errorBody['error_code'] ?? 'unknown',
                'description' => $errorDesc,
            ]);

            // Jika chat tidak ditemukan atau bot diblokir, hapus chat_id dari database
            if (str_contains($errorDesc, 'chat not found') ||
                str_contains($errorDesc, 'bot was blocked') ||
                str_contains($errorDesc, 'user deactivated')) {
                Pelanggan::where('chat_id', $chat_id)->update(['chat_id' => null]);
                Log::info('Chat_id dihapus karena tidak valid', ['chat_id' => $chat_id]);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception saat mengirim pesan Telegram', [
                'chat_id' => $chat_id,
                'exception' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Broadcast pesan ke semua pelanggan yang memiliki chat_id
     *
     * @param string $text
     * @return void
     */
    public function broadcastToPelanggan($text)
    {
        $pelanggans = Pelanggan::whereNotNull('chat_id')->get();

        foreach ($pelanggans as $pelanggan) {
            $this->sendToChat($pelanggan->chat_id, $text);
            usleep(300000); // delay 0.3 detik untuk menghindari flood
        }
    }

    /**
     * Notifikasi status PPPoE untuk satu pelanggan
     *
     * @param \App\Models\Pelanggan $pelanggan
     * @param string $status (down / up)
     * @return bool
     */
    public function notifyPppoe($pelanggan, $status)
    {
        if (!$pelanggan || !$pelanggan->chat_id) {
            Log::warning('notifyPppoe: chat_id tidak ada', [
                'pelanggan_id' => $pelanggan->id ?? 'unknown',
            ]);
            return false;
        }

        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda *tidak aktif*. Mohon menunggu perbaikan."
            : "✅ Internet Normal Kembali\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda sekarang *aktif*.";

        return $this->sendToChat($pelanggan->chat_id, $msg);
    }

    /**
     * Notifikasi status ISP untuk semua pelanggan
     *
     * @param string $status (down / up)
     * @return void
     */
    public function notifyIsp($status)
    {
        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet Pusat\n\nKoneksi ISP sedang *tidak aktif*.\nMohon bersabar, teknisi sedang memeriksa."
            : "✅ Internet Pusat Normal Kembali\n\nKoneksi ISP sekarang *aktif*.";

        $this->broadcastToPelanggan($msg);
    }

    /**
     * Bersihkan semua chat_id negatif dari database
     *
     * @return int Jumlah chat_id yang dihapus
     */
    public function cleanupNegativeChatIds()
    {
        $count = Pelanggan::where('chat_id', '<', 0)->update(['chat_id' => null]);
        Log::info('Cleanup negative chat_ids', ['deleted' => $count]);
        return $count;
    }
}