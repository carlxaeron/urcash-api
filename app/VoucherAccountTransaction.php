<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoucherAccountTransaction extends Model
{
    protected $fillable = [
        'id','ref_number', 'voucher_accounts_id', 'transaction_type_id', 'status'
    ];
}
