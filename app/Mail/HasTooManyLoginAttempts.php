<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class HasTooManyLoginAttempts extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user) {
        $this->user = $user;
        $this->subject = "Your account has been temporarily locked";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.has_too_many_login_attempts')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
            'mobile_number' => $this->user->mobile_number,
        ]);
    }
}
