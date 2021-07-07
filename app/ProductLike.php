<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductLike extends Model
{
    protected $guarded = [];

    public function setUsersDataAttribute($value)
    {
        $this->attributes['users_data'] = serialize($value);
    }

    public function getUsersDataAttribute()
    {
        return unserialize($this->attributes['users_data']);
    }
}
