<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $fillable = [
        'user_id', 'address_id', 'reg_bus_name', 'dti', 'bir_reg_cert', 'mayors_permit',
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

    /**
     * Set the merchant's bir_reg_cert, and mayors_permit values to uppercase. - based on sample documents
     */
    public function setBirRegCertAttribute ($value) {
        $this->attributes['bir_reg_cert'] = strtoupper($value);
    }

    public function setMayorsPermitAttribute ($value) {
        $this->attributes['mayors_permit'] = strtoupper($value);
    }
}
