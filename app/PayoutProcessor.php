<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutProcessor extends Model
{
    protected $fillable = [
        'proc_id', 'description',
    ];
}
