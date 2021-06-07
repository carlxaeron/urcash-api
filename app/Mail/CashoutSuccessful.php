<?php

namespace App\Mail;

use App\Shop;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\VoucherAccountTransaction;
use App\CashoutBank;
use App\CashoutEWallet;
use App\Bank;
use App\EWallet;

class CashoutSuccessful extends Mailable
{
    use Queueable, SerializesModels;

    // Global variables
    public $user;
    public $cashout;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $cashout) {
        $this->user = $user;
        $this->cashout = $cashout;
        $this->subject = "Cashout successful!";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $shop = Shop::where('user_id', '=', $this->user->id)->first();

        $vaoucherAccountTransaction = VoucherAccountTransaction::find($this->cashout['voucher_account_transaction_id']);

        $cashoutBank = CashoutBank::where('cashout_id', $this->cashout->id)->first();

        if($cashoutBank) {
            $payment_method = Bank::find($cashoutBank->banks_id);
            $account_num =  $cashoutBank->account_number;
        }
        $cashoutEwallet = CashoutEWallet::where('cashout_id', $this->cashout->id)->first();

        if($cashoutEwallet)
        {
            $payment_method = EWallet::find($cashoutEwallet->e_wallet_id);
            $account_num = $cashoutEwallet->account_number;
        }

        return $this->view('emails.cashout_successful')->with([
            'first_name' => Str::title($this->user->first_name),
            'last_name' => Str::title($this->user->last_name),
            'business_name' => $shop->reg_bus_name,
            'ref_number' => $vaoucherAccountTransaction->ref_number,
            'date_time' =>  $vaoucherAccountTransaction->create_at,
            'payment_method' => $payment_method->name,
            'account_number' => $account_num,
            'amount' => $this->cashout->amount,
            'fee' => $this->cashout->fee
        ]);
    }
}
