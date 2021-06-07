<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $fillable = [
        'admin_user_id', 'full_name', 'action', 'notes'
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
     * Set the full name value of user to uppercase. - STANDARD.
     */
    public function setFullNameAttribute ($value) {
        $this->attributes['full_name'] = strtoupper($value);
    }
}
