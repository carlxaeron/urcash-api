<?php

namespace App\Listeners;

use App\Events\PaidInvoiceEvent;
use App\Http\Services\RedService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessPaidInvoice
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
     * @param  PaidInvoiceEvent  $event
     * @return void
     */
    public function handle(PaidInvoiceEvent $event)
    {
        if(config('UCC.type') == 'RED') {
            app(RedService::class)->purchase($event->invoice);
        }
    }
}
