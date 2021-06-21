<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'shop_id', 'sku', 'ean', 'name', 'manufacturer_name', 'variant', 'is_verified','user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $with = [
        'prices'
    ];

    public function prices() {
        return $this->hasOne(Price::class,'id');
    }

    public function scopeVerified($query) {
        return $query->where('is_verified',1);
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
}
