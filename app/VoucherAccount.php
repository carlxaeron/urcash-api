<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherAccount extends Model
{
    protected $fillable = [
        'id', 'shop_id', 'voucher_balance'
    ];
}
