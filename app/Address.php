<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'id', 'complete_address', 'street', 'barangay', 'city', 'province', 'country',
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
     * Set the address' street, barangay, city, and province values to uppercase first letter.
     */
    public function setStreetAttribute ($value) {
        $this->attributes['street'] = ucfirst($value);
    }
    public function setBarangayAttribute ($value) {
        $this->attributes['barangay'] = ucfirst($value);
    }
    public function setCityAttribute ($value) {
        $this->attributes['city'] = ucfirst($value);
    }
    public function setProvinceAttribute ($value) {
        $this->attributes['province'] = ucfirst($value);
    }
}
