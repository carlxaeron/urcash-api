<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPurchasePoint extends Model
{
    protected $guarded = [];

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = serialize($value ?? []);
    }
    public function getDataAttribute($value)
    {
        return unserialize($this->attributes['data']);
    }
}
