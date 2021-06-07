<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name', 'slug',
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
     * Foreign key relationships
     */
    public function roles() {
        return $this->belongsToMany(Role::class,'roles_permissions');
    }

    public function users() {
        return $this->belongsToMany(User::class,'users_permissions');
    }
}
