<?php

namespace App\Observers;

use App\Events\Product\Created;
use App\Events\Product\Rejected;
use App\Events\Product\Resubmit;
use App\Events\Product\Verified;
use App\Product;

class ProductObserver
{
    /**
     * Handle the product "created" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        if($product->is_verified == 0) {
            event(new Created($product));
            logger('created product');
        }
    }

    /**
     * Handle the product "updated" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        if($product->is_verified == 1) {
            event(new Verified($product));
            logger('Verified product');
        }
        if($product->is_verified == 2) {
            event(new Rejected($product));
            logger('Rejected product');
        }
        if($product->is_verified == 3) {
            event(new Resubmit($product));
            logger('Resubmit product');
        }
    }

    /**
     * Handle the product "deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the product "restored" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the product "force deleted" event.
     *
     * @param  \App\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
