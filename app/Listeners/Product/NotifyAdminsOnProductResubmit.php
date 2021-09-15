<?php

namespace App\Listeners\Product;

use App\Events\Product\Resubmit;
use App\Notifications\ProductResubmit;
use App\Role;
use App\User;
use App\UsersRole;
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

        $role = Role::where('slug','administrator')->first();
        $users = UsersRole::where('role_id', $role->id)->with(['user'])->get();
        foreach($users as $user) {
            $_user = User::find($user->user->id);
            if($_user) $_user->notify(new ProductResubmit());
        }
        
        $product = $event->product;
    }
}
