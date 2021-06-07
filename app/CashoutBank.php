<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashoutBank extends Model
{
    protected $fillable = [
        'id', 'banks_id', 'cashout_id', 'account_name', 'account_number'
    ];
}
