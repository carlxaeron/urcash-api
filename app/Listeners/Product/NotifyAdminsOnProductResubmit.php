<?php

namespace App\Listeners\Product;

use App\Events\Product\Resubmit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminsOnProductResubmit
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
     * @param  Resubmit  $event
     * @return void
     */
    public function handle(Resubmit $event)
    {
        $product = $event->product;
    }
}
