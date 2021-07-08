<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCart extends Model
{
    protected $guarded = [];

    protected $with = ['product'];

    public function product()
    {
        return $this->hasOne('App\Product','id','product_id');
    }
}
