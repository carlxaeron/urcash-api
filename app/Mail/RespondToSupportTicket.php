<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class RespondToSupportTicket extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $support_ticket;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($support_ticket, $body) {
        $this->support_ticket = $support_ticket;
        $this->body = $body;
        $this->subject = $this->subject = "Support ticket #" . $support_ticket->reference_number . " - " . $support_ticket->issue;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.respond_to_support_ticket');
    }
}
