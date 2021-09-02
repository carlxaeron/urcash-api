<?php

namespace App\Providers;

use App\Events\PaidEvent;
use App\Events\PaidInvoiceEvent;
use App\Events\PioCallback;
use App\Events\Product\Created;
use App\Events\Product\Rejected;
use App\Events\Product\Resubmit;
use App\Events\Product\Verified;
use App\Events\UserRegistered;
use App\Listeners\ProcessCallback;
use App\Listeners\ProcessPaidInvoice;
use App\Listeners\ProcessPaidPurchase;
use App\Listeners\ProcessRegisteredUser;
use App\Listeners\Product\NotifyAdminsOnProductCreated;
use App\Listeners\Product\NotifyAdminsOnProductResubmit;
use App\Listeners\Product\NotifyUserOnProductRejected;
use App\Listeners\Product\NotifyUserOnProductVerified;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PioCallback::class => [
            ProcessCallback::class
        ],
        UserRegistered::class => [
            ProcessRegisteredUser::class
        ],
        PaidEvent::class => [
            ProcessPaidPurchase::class
        ],
        PaidInvoiceEvent::class => [
            ProcessPaidInvoice::class
        ],
        Created::class => [
            NotifyAdminsOnProductCreated::class
        ],
        Verified::class => [
            NotifyUserOnProductVerified::class
        ],
        Rejected::class => [
            NotifyUserOnProductRejected::class
        ],
        Resubmit::class => [
            NotifyAdminsOnProductResubmit::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
