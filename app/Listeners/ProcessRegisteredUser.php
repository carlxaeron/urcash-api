<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Http\Services\RedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessRegisteredUser
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if(config('UCC.type') == 'RED') {
            $reg = app(RedService::class)->register($event->user);
            // logger($reg);
            return $reg;
        }
    }
}
