<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'user_id', 'price', 'batch_code',
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

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
