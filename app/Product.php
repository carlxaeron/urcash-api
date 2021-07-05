<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_id', 'sku', 'ean', 'name', 'manufacturer_name', 'variant', 'is_verified','user_id','description'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = [
        'prices','images','categories.category'
    ];

    protected $appends = [
        'price'
    ];

    public function prices() {
        return $this->hasOne(Price::class,'id');
    }
    public function images() {
        return $this->hasMany(ProductImage::class,'product_id','id');
    }
    public function categories() {
        return $this->hasMany(ProductCategory::class,'product_id','id');
    }
    public function owner() {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function scopeVerified($query) {
        return $query->where('is_verified',1);
    }
    public function scopeUnverified($query) {
        return $query->where('is_verified',0);
    }
    public function scopeRelated($query, $limit) {
        return $query->inRandomOrder()->limit($limit);
    }
    public function scopeSearch($query, $keyword) {
        return $query->where(function($q) use($keyword) {
            return $q
            ->where('name','like','%'.$keyword.'%')
            ->orWhere('description','like','%'.$keyword.'%')
            // ->orWhere('name','like','%'.$keyword.'%')
            ;
        });
    }
    public function scopeFilters($query) {
        if($cat = request()->category) {
            // $query = $query->categories->where('category_id',$cat);
            $ids = ProductCategory::where('category_id',$cat)->get('product_id')->map(function($v) { return $v['product_id']; })->toArray();
            if(count($ids)) $query = $query->whereIn('id',$ids);
            else $query = $query->where('id',[0]);
        }
        if($ob = request()->order_by) {
            $sort = request()->sort_by ? request()->sort_by : 'asc';
            if($ob == 'id') {
                $query->orderBy('id', $sort);
            }
            elseif($ob == 'name') {
                $query->orderBy('name', $sort);
            }
            elseif($ob == 'date') {
                $query->orderBy('created_at', $sort);
            }
        }
        return $query;
    }

    /**
     * Serialize timestamps as datetime strings without the timezone.
     */
    public function getCreatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTimeString();
    }

    public function getUpdatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTimeString();
    }

    public function getPriceAttribute() {
        return $this->prices->price;
    }
}
