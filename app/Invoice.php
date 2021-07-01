<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = serialize($value);
    }

    public function getDataAttribute()
    {
        return unserialize($this->attributes['data']);
    }

    public function scopeCheckoutItemsRef($q, $ref)
    {
        return $q->where('data','like','%"CHECKOUT_ITEMS__reference";s:20:"'.$ref.'";%');
    }
}
