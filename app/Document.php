<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function getPathAttribute()
    {
        return asset($this->attributes['path']);
    }
}
