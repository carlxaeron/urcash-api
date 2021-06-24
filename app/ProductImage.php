<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    protected $guarded = [];

    protected $hidden = ['product_id', 'deleted_at', 'created_at'];

    public function getFilenameAttribute() {
        return asset($this->attributes['filename']);
    }
}
