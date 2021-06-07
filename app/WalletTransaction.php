<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
    'id','transaction_type', 'txn_id', 'refNo','wallet_id','transaction_description','payment_method','amount','charge_amount','status'
    ];

    public function wallet () {
        return $this->belongsTo('App\Wallet');
    }
}
