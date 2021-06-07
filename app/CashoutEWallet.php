<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashoutEWallet extends Model
{
    protected $fillable = [
        'id', 'e_wallet_id', 'cashout_id', 'account_name', 'account_number'
    ];
}
