<?php

namespace App\Listeners\Product;

use App\Events\Product\Created;
use App\Notifications\Product\Created as ProductCreated;
use App\Role;
use App\User;
use App\UsersRole;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsOnProductCreated
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
     * @param  Created  $event
     * @return void
     */
    public function handle(Created $event)
    {
        $product = $event->product;

        $role = Role::where('slug','administrator')->first();
        $users = UsersRole::where('role_id', $role->id)->with(['user'])->get();
        foreach($users as $user) {
            $_user = User::find($user->user->id);
            if($_user) $_user->notify(new ProductCreated());
        }
        
        $product = $event->product;
    }
}
