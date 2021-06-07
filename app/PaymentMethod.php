<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'id', 'title', 'description', 'payment_instruction', 'verification_instruction', 'bank_name', 'account_name',
        'account_number', 'status'
    ];
}
