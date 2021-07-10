<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLike extends Model
{
    protected $guarded = [];

    protected $with = ['product'];

    public function product()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }

    public function setUsersDataAttribute($value)
    {
        $this->attributes['users_data'] = serialize($value);
    }

    public function getUsersDataAttribute()
    {
        return unserialize($this->attributes['users_data']);
    }
}
