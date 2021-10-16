<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'shop_id', 'sku', 'ean', 'name', 'manufacturer_name', 'variant', 'is_verified','user_id','description', 'company_price'
    ];

    protected $hidden = [
        // 'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $with = [
        'prices','images','categories.category','owner','remarks'
    ];

    protected $appends = [
        'price', 'likes', 'status'
    ];

    public function prices() {
        return $this->hasOne(Price::class,'id');
    }
    public function images() {
        return $this->hasMany(ProductImage::class,'product_id','id');
    }
    public function categories() {
        return $this->hasMany(ProductCategory::class,'product_id','id');
    }
    public function owner() {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function likes() {
        return $this->hasMany(ProductLike::class, 'product_id', 'id');
    }
    public function remarks()
    {
        return $this->morphMany('App\Notify', 'notifiable')->where('notifies.type','product_remarks');
    }

    public function scopeVerified($query) {
        return $query->where('is_verified',1);
    }
    public function scopeUnverified($query) {
        return $query->where('is_verified',0);
    }
    public function scopeRejected($q) {
        return $q->where('is_verified',2);
    }
    public function scopeResubmitted($q) {
        return $q->where('is_verified',3);
    }
    public function scopeRelated($query, $limit) {
        return $query->inRandomOrder()->limit($limit);
    }
    public function scopeSearch($query, $keyword) {
        return $query->where(function($q) use($keyword) {
            return $q
            ->where('name','like','%'.$keyword.'%')
            ->orWhere('description','like','%'.$keyword.'%')
            // ->orWhere('name','like','%'.$keyword.'%')
            ;
        });
    }
    public function scopeFilters($query) {
        if($cat = request()->category) {
            // $query = $query->categories->where('category_id',$cat);
            $ids = ProductCategory::where('category_id',$cat)->get('product_id')->map(function($v) { return $v['product_id']; })->toArray();
            if(count($ids)) $query = $query->whereIn('id',$ids);
            else $query = $query->where('id',[0]);
        }
        if($ob = request()->order_by) {
            $sort = request()->sort_by ? request()->sort_by : 'asc';
            if($ob == 'id') {
                $query->orderBy('id', $sort);
            }
            elseif($ob == 'name') {
                $query->orderBy('name', $sort);
            }
            elseif($ob == 'date') {
                $query->orderBy('created_at', $sort);
            }
        }
        if($red = request()->red_category) {
            $query->select('products.*');
            if($red == 'premium') {
                $query->leftJoin('users','users.id','=','products.user_id')
                ->where(function($q){
                    $q
                    ->where('users.vip_level','5')
                    ->orWhere('users.vip_level','6')
                    ;
                })
                ;
            }
            elseif($red == 'preffered') {
                $query->leftJoin('users','users.id','=','products.user_id')
                ->where(function($q){
                    $q
                    ->where('users.vip_level','3')
                    ->orWhere('users.vip_level','4')
                    ;
                })
                ;
            }
            elseif($red == 'privilege') {
                $query->leftJoin('users','users.id','=','products.user_id')
                ->where(function($q){
                    $q
                    ->where('users.vip_level','1')
                    ->orWhere('users.vip_level','2')
                    ;
                })
                ;
            }
            else {
                $query->leftJoin('users','users.id','=','products.user_id')
                ->where(function($q){
                    $q
                    ->where('users.vip_level','0')
                    ;
                })
                ;
            }
        }
        return $query;
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

    public function getPriceAttribute() {
        return $this->prices ? $this->prices->price : 'ERROR';
    }

    public function getLikesAttribute() {
        $mdl = $this->hasMany('App\ProductLike','product_id','id')->count();
        
        return $mdl;
    }

    public function getStatusAttribute() {
        if($this->attributes['is_verified'] == 3) {
            $status = 'resubmit';
        }
        if($this->attributes['is_verified'] == 2) {
            $status = 'rejected';
        }
        if($this->attributes['is_verified'] == 1) {
            $status = 'verified';
        }
        if($this->attributes['is_verified'] == 0) {
            $status = 'pending';
        }

        return $status;
    }
}
