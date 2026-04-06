<?php

namespace App\Listeners;

use App\Events\PppEvent;
use App\Models\Pelanggan;
use App\Services\TelegramService;

class SendPppTelegram
{
    protected $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle(PppEvent $event): void
    {
        $pelanggan = Pelanggan::where('username_pppoe', $event->user)->first();
        if (!$pelanggan || !$pelanggan->chat_id) return;

        $lastStatus = cache()->get('user_status_'.$event->user);
        if ($lastStatus === $event->type) return;
        cache()->put('user_status_'.$event->user, $event->type, 600);

        $status = $event->type === 'remove' ? 'down' : 'up';
        $this->telegram->notifyPppoe($pelanggan, $status);
    }
}