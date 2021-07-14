<?php

namespace App\Listeners;

use App\Events\PioCallback;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessCallback
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
     * @param  PioCallback  $event
     * @return void
     */
    public function handle(PioCallback $event)
    {
        $inv = $event->invoice;
    }
}
