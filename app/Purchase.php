<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'shop_id', 'customer_mobile_number',
    ];

    /**
     * Serialize timestamps as datetime strings without the timezone.
     */
    public function getCreatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTime();
    }

    public function getUpdatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTime();
    }
}
