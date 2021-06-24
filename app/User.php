<?php

namespace App;

use App\Traits\HasPermissionsTrait;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, Notifiable, HasPermissionsTrait, CanResetPassword;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','address_id', 'first_name', 'middle_name', 'last_name', 'mobile_number', 'birthdate', 'email', 'password',
        'token', 'authToken', 'profile_picture'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'otp', 'otp_expiration', 'otp_created_at', 'email_verified_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'birthdate' => 'date',
        'email_verified_at' => 'datetime',
    ];

    protected $with = [
        'roles'
    ];

    /**
     * Serialize timestamps as datetime strings without the timezone.
     */
    public function getBirthdateAttribute($date) {
        return Carbon::parse($date)->format('m/d/Y');
    }
    public function getCreatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTimeString();
    }

    public function getUpdatedAtAttribute($date) {
        return Carbon::parse($date)->toDateTimeString();
    }

     /**
     * Set the user's first_name, middle_name, and last_name values to uppercase. - STANDARD.
     * Set the user's email value to lowercase - STANDARD.
     */
    public function setFirstNameAttribute ($value) {
        $this->attributes['first_name'] = strtoupper($value);
    }

    public function setMiddleNameAttribute ($value) {
        $this->attributes['middle_name'] = strtoupper($value);
    }

    public function setLastNameAttribute ($value) {
        $this->attributes['last_name'] = strtoupper($value);
    }

    public function setEmailAttribute ($value) {
        $this->attributes['email'] = strtolower($value);
    }

    public function setPasswordAttribute ($value) {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Foreign key relationships
     */

    public function announcement () {
        return $this->hasMany('App\Announcement');
    }

    public function ticketPurchase () {
        return $this->hasMany('App\TicketPurchase');
    }

    public function roles () {
        return $this->hasMany('App\UsersRole','user_id','id');
    }
}
