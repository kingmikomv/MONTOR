<?php

namespace App\Listeners;

use App\Events\PppEvent;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue; // Optional jika ingin antrian
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Pelanggan;

class SendPppTelegram
{
    // Jika ingin listener ini queued, uncomment implement ShouldQueue
    // implements ShouldQueue
    // use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PppEvent $event): void
{
    $pelanggan = Pelanggan::where('username_pppoe', $event->user)->first();

    if (!$pelanggan || !$pelanggan->chat_id) {
        return;
    }

    // ===== Cek status terakhir =====
    $lastStatus = cache()->get('user_status_'.$event->user);
    if ($lastStatus === $event->type) {
        // status sama, jangan kirim
        return;
    }

    // simpan status terakhir ke cache (misal 10 menit)
    cache()->put('user_status_'.$event->user, $event->type, 600);

    // Pilih pesan berdasarkan type
    $msg = '';
if ($event->type === 'remove') {
    $msg = "⚠️ Pemberitahuan Gangguan Internet\n\n"
        . "Halo {$pelanggan->nama} ({$pelanggan->username_pppoe}),\n\n"
        . "Kami mendeteksi koneksi internet Anda sedang **tidak aktif**.\n"
        . "Tim teknisi kami sedang memeriksa dan akan segera memperbaikinya.\n\n"
        . "Mohon kesabarannya. Terima kasih atas pengertian Anda.\n\n"
        . "📌 Jika masalah berlangsung lebih dari 30 menit, silakan hubungi layanan pelanggan kami.";
} elseif ($event->type === 'add') {
    $msg = "✅ Internet Normal Kembali\n\n"
        . "Halo {$pelanggan->nama} ({$pelanggan->username_pppoe}),\n\n"
        . "Koneksi internet Anda sekarang sudah **aktif kembali** dan dapat digunakan seperti biasa.\n\n"
        . "Terima kasih telah menggunakan layanan kami.";
}

    // Kirim ke ESP endpoint Telegram
    try {
        Http::get('http://192.168.137.203/send', [
            'chat_id' => $pelanggan->chat_id,
            'msg' => $msg
        ]);
    } catch (\Exception $e) {
        \Log::error('Gagal kirim ke ESP', [
            'error' => $e->getMessage(),
            'user' => $event->user,
            'type' => $event->type,
        ]);
    }
}
}