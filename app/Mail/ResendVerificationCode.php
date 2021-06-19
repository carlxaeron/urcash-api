<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ResendVerificationCode extends Mailable
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
        $this->subject = "B2B - Resend Verification Code";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.resend_verification_code')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
        ]);
    }
}
