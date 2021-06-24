<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersRole extends Model
{
    protected $fillable = [
        'user_id', 'role_id',
    ];

    protected $visible = [
        'role'
    ];

    protected $with = [
        'role'
    ];

    public function role() {
        return $this->hasOne('App\Role','id','role_id');
    }
}
