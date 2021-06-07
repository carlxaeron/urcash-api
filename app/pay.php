<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    protected $fillable = [
        'id', 'voucher_account_transaction_id','payor_shop_id', 'payee_shop_id', 'amount'
    ];
}
