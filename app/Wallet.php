<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Wallet extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'id', 'qr_code', 'user_id', 'available_balance',
    ];

    public function walletTransaction () {
        return $this->hasMany('App\WalletTransaction');
    }
}
