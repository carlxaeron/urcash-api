<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherOrder extends Model
{
   protected $fillable = [
        'id','voucher_account_transaction_id', 'payment_method_id', 'voucher_id','transaction_description', 'number_of_vouchers', 'amount', 'fee', 'proof_of_payment', 'status'
    ];
}
