<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notify extends Model
{
    public static $PRODUCT_REMARKS = 'product_remarks';

    public function notifiable()
    {
        return $this->morphTo();
    }
}
