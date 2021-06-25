<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at'];

    protected $with = [
        // 'category'
    ];

    public function category()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }
}
