<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PppEvent;
use App\Listeners\SendPppTelegram;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PppEvent::class => [
            SendPppTelegram::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}