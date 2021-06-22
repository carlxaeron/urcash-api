<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    
    protected $guarded = ['created_at', 'deleted_at', 'updated_at'];

    public function setNameAttribute($value) {
        $this->attributes['name'] = strtoupper($value);
    }
}
