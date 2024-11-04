<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use App\Mail\NewSession;
use DateTime;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $event->user->last_login = new DateTime;
        $event->user->save();
        Mail::to($event->user->email)->queue(new NewSession($event->user->name));
        Log::info('Correo enviado a '. $event->user->email);

    }
}
