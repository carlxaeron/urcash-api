<?php

namespace App\Mail;

use App\Product;
use App\PurchaseItem;
use App\Shop;
use App\Http\Helper\Utils\CalculateSales;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CheckoutProducts extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;
    public $purchase;
    public $subtotal;
    public $products = array();

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $purchase) {
        $this->user = $user;
        $this->purchase = $purchase;
        $this->subject = "Checkout successful!";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $find_purchase_items = PurchaseItem::where('purchase_id', '=', $this->purchase->id)->get();
        $shop = Shop::where('user_id', '=', $this->user->id)->first();

        for ($i = 0; $i < $find_purchase_items->count(); $i++) {
            $product = Product::find($find_purchase_items[$i]->product_id);
            $calculate_sales = new CalculateSales();
            $this->subtotal = $calculate_sales->calculateSubtotal($find_purchase_items, $shop->id);

            $this->products[$i]['ean'] = $product->ean;
            $this->products[$i]['name'] = $product->name;
            $this->products[$i]['manufacturer'] = $product->manufacturer_name;
            $this->products[$i]['quantity'] = $find_purchase_items[$i]->quantity;
        }

        return $this->view('emails.checkout_products')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
            'customer_mobile_number' => $this->purchase->customer_mobile_number,
            'business_name' => $shop->reg_bus_name,
            'subtotal' => $this->subtotal,
            'products' => $this->products
        ]);
    }
}
