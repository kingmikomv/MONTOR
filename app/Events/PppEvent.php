<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PppEvent implements ShouldBroadcastNow
{
    public $type;
    public $user;

    public function __construct($payload)
    {
        $this->type = $payload['type'] ?? null;
        $this->user = $payload['user'] ?? null;
    }

    public function broadcastOn()
{
    \Log::info('🔥 broadcastOn jalan');
    return new \Illuminate\Broadcasting\Channel('ppp-channel');
}

    public function broadcastAs()
    {
        return 'PppEvent';
    }

    // 🔥 INI KUNCI NYA
    public function broadcastWith()
    {
        return [
            'type' => $this->type,
            'user' => $this->user,
        ];
    }
}