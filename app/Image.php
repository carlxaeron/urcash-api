<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['path'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getPathAttribute() {
        return asset($this->attributes['path']);
    }
}
