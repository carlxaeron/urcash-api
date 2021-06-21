<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'shop_id', 'product_id', 'price',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'shop_id',
        'product_id',
    ];

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
