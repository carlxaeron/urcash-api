<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;
    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $code) {
        $this->user = $user;
        $this->code = $code;
        $this->subject = "B2B - Confirm your registration";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.verify_email')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
        ]);
    }
}
