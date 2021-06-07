<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class MarkSupportTicketAsResolved extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;
    public $support_ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $support_ticket) {
        $this->user = Str::title($user);
        $this->support_ticket = $support_ticket;
        $this->subject = "Support ticket #" . $support_ticket->reference_number . " marked as resolved";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->view('emails.mark_support_ticket_as_resolved');
    }
}
