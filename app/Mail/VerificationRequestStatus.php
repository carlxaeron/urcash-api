<?php

namespace App\Mail;

use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class VerificationRequestStatus extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;
    public $verification_request;
    public $product;
    public $status_text;
    public $ean = null;
    public $product_name = null;
    public $product_manufacturer = null;
    public $variant = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $verification_request, $product = null) {
        $this->user = $user;
        $this->verification_request = $verification_request;

        if ($product != null) {
            $find_product = Product::find($product->id);
            $this->ean = $find_product->ean;
            $this->product_name = $find_product->name;
            $this->product_manufacturer = $find_product->manufacturer_name;
            $this->variant = $find_product->variant;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        if ($this->verification_request->is_accepted == True) {
            $this->status_text = 'accepted';
        } elseif ($this->verification_request->is_accepted == False) {
            $this->status_text = 'rejected';
        }

        return $this->view('emails.verification_request_status')
            ->subject("Verification request #" .$this->verification_request->id. " was " .$this->status_text)
            ->with([
                'first_name' => Str::title($this->user->first_name),
                'last_name' => Str::title($this->user->last_name),
                'status' => $this->status_text,
                'ean' => $this->ean,
                'product_name' => $this->product_name,
                'product_manufacturer' => $this->product_manufacturer,
                'variant' => $this->variant
        ]);
    }
}
