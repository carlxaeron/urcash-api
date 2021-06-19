<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class EmailVerifiedSuccessfully extends Mailable
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
        $this->subject = "B2B - You have successfully verified your email!";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.email_verified_successfully')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
        ]);
    }
}
