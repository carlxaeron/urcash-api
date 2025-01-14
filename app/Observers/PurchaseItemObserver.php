<?php

namespace App\Observers;

use App\Events\PaidEvent;
use App\PurchaseItem;

class PurchaseItemObserver
{
    /**
     * Handle the purchase item "created" event.
     *
     * @param  \App\PurchaseItem  $purchaseItem
     * @return void
     */
    public function created(PurchaseItem $purchaseItem)
    {
        // dd($purchaseItem->);
        if($purchaseItem->purchase_status == 'paid') {
            event(new PaidEvent($purchaseItem));
        }
    }

    /**
     * Handle the purchase item "updated" event.
     *
     * @param  \App\PurchaseItem  $purchaseItem
     * @return void
     */
    public function updated(PurchaseItem $purchaseItem)
    {
        if($purchaseItem->purchase_status == 'paid') {
            event(new PaidEvent($purchaseItem));
        }
    }

    /**
     * Handle the purchase item "deleted" event.
     *
     * @param  \App\PurchaseItem  $purchaseItem
     * @return void
     */
    public function deleted(PurchaseItem $purchaseItem)
    {
        //
    }

    /**
     * Handle the purchase item "restored" event.
     *
     * @param  \App\PurchaseItem  $purchaseItem
     * @return void
     */
    public function restored(PurchaseItem $purchaseItem)
    {
        //
    }

    /**
     * Handle the purchase item "force deleted" event.
     *
     * @param  \App\PurchaseItem  $purchaseItem
     * @return void
     */
    public function forceDeleted(PurchaseItem $purchaseItem)
    {
        //
    }
}
