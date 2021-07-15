<?php

namespace App\Providers;

use App\Events\PaidEvent;
use App\Events\PaidInvoiceEvent;
use App\Events\PioCallback;
use App\Events\UserRegistered;
use App\Listeners\ProcessCallback;
use App\Listeners\ProcessPaidInvoice;
use App\Listeners\ProcessPaidPurchase;
use App\Listeners\ProcessRegisteredUser;
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
