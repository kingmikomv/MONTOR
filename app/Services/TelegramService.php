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

    // Kirim ke chat_id tertentu (private)
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
                \Log::error('Telegram response failed', [
                    'chat_id' => $chat_id,
                    'body' => $response->body(),
                ]);
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

    // Handle pesan dari group → log & auto keluar
    public function handleGroupMessage(array $message)
    {
        $chat = $message['chat'] ?? [];
        $chat_id = $message['from']['id'] ?? null;
        $chat_type = $chat['type'] ?? '';
        $chat_title = $chat['title'] ?? null;
        $text = $message['text'] ?? '';

        if ($chat_type === 'group' || $chat_type === 'supergroup') {

            // Log semua info grup
            \Log::warning('BOT DETECTED IN GROUP', [
                'group_chat_id' => $chat['id'] ?? null,
                'type' => $chat_type,
                'title' => $chat_title,
                'added_by_user_id' => $chat_id,
                'message_text' => $text,
            ]);

            // Auto keluar dari grup
            $groupChatId = $chat['id'] ?? null;
            if ($groupChatId) {
                Http::get("https://api.telegram.org/bot{$this->botToken}/leaveChat?chat_id={$groupChatId}");
            }

            return true;
        }

        return false;
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
        $msg = $status === 'down'
            ? "⚠️ Gangguan Internet\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda *tidak aktif*. Mohon menunggu perbaikan."
            : "✅ Internet Normal Kembali\n\nHalo {$pelanggan->nama} ({$pelanggan->username_pppoe})\nKoneksi Anda sekarang *aktif*.";

        $this->sendToChat($pelanggan->chat_id, $msg);
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