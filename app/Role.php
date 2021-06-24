<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 'slug',
    ];

    protected $visible = [
        'name'
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
    public function permissions() {
        return $this->belongsToMany(Permission::class,'roles_permissions');
    }

    public function users() {
        return $this->belongsToMany(User::class,'users_roles');
    }
}
