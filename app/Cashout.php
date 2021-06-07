<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cashout extends Model
{
    protected $fillable = [
        'id', 'voucher_account_transaction_id','amount' , 'fee', 'status'
    ];
}
