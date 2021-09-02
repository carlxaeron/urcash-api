<?php

namespace App\Listeners\Product;

use App\Events\Product\Verified;
use App\Notifications\Product\Verified as ProductVerified;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyUserOnProductVerified
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
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $product = $event->product;

        User::find($event->product->owner->id)->notify(new ProductVerified($product));
    }
}
