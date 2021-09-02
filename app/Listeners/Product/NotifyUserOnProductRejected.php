<?php

namespace App\Listeners\Product;

use App\Events\Product\Rejected;
use App\Notifications\Product\Rejected as ProductRejected;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserOnProductRejected
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
     * @param  Rejected  $event
     * @return void
     */
    public function handle(Rejected $event)
    {
        $product = $event->product;

        User::find($event->product->owner->id)->notify(new ProductRejected($product));
    }
}
