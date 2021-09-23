<?php

namespace App\Listeners;

use App\Events\PaidEvent;
use App\Http\Services\RedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessPaidPurchase
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
     * @param  PaidEvent  $event
     * @return void
     */
    public function handle(PaidEvent $event)
    {
        if(config('UCC.type') == 'RED') {
            app(RedService::class)->purchase($event->purchase);
        }
    }
}
