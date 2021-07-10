<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'user_id', 'price', 'batch_code','data', 'note'
    ];

    protected $appends = [
        'purchase_step'
    ];

    protected $with = [
        'product', 'user'
    ];

    public function product() {
        return $this->belongsTo('App\Product');
    }

    public function scopeFilters($q) {
        if(request()->date_start) {
            $q->where(function($q2) {
                $q2
                ->whereDate('purchase_items.created_at','>',request()->date_start)
                ->whereDate('purchase_items.created_at','<',request()->date_end)
                ;
            });
        }

        return $q;
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

    public function getPurchaseStepAttribute() {
        switch ($this->attributes['status']) {
            case 'processing':
                $num = 1;
                break;

            case 'shipped':
                $num = 2;
                break;
            
            case 'delivered':
                $num = 3;
                break;

            case 'completed':
                $num = 4;
                break;
            
            default:
                $num = 0;
                break;
        }

        return $num;
    }

    public function setDataAttribute($value)
    {
        $this->attributes['data'] = serialize($value ?? []);
    }
    public function getDataAttribute($value)
    {
        return unserialize($this->attributes['data']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
