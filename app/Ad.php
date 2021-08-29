<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['name'];

    protected $with = ['images'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
