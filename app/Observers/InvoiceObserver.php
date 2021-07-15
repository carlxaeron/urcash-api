<?php

namespace App\Observers;

use App\Events\PaidInvoiceEvent;
use App\Invoice;

class InvoiceObserver
{
    /**
     * Handle the invoice "created" event.
     *
     * @param  \App\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the invoice "updated" event.
     *
     * @param  \App\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        if($invoice->status == 'paid') {
            event(new PaidInvoiceEvent($invoice));
        }
    }

    /**
     * Handle the invoice "deleted" event.
     *
     * @param  \App\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the invoice "restored" event.
     *
     * @param  \App\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the invoice "force deleted" event.
     *
     * @param  \App\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }
}
