<?php
namespace App\Repositories;

use App\Http\Resources\PurchaseItems;
use App\Interfaces\OrderInterface;
use App\Product;
use App\PurchaseItem;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderRepository implements OrderInterface
{
    use ResponseAPI;

    public function updateOrderStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $inputs = [
                'order_id'=>$request->order_id,
                'status'=>$request->status,
            ];
            $rules = [
                'order_id'=>['required','exists:purchase_items,id',function($attr,$value,$fail) use($user){
                    // admin can update any status
                    if(!$user->hasRole('administrator')) {
                        if(!PurchaseItem::where('id',$value)->where('user_id',$user->id)->count()) {
                            $fail('You dont have permission to update this order.');
                        }
                    }
                }],
                'status'=>['sometimes',function($attr,$value,$fail) {
                    if(!in_array($value,config('purchase_statuses.status.v1'))) $fail('Status not found!');
                }]
            ];
            $others = [];
            if($request->status == 'shipped') {
                $inputs['tracking_number'] = $request->tracking_number;
                $rules['tracking_number'] = 'required';
                $inputs['remarks'] = $request->remarks;
                $rules['remarks'] = function($attr, $value, $fail) {
                    if(!$value && $value != 'N/A') $fail('The remarks field is required. Type N/A if not available.');
                };
            }
            elseif($request->status == 'cancelled') {
                $inputs['remarks'] = $request->remarks;
                $rules['remarks'] = 'required';
                $rules['remarks'] = function($attr, $value, $fail) {
                    if(!$value && $value != 'N/A') $fail('The remarks field is required. Type N/A if not available.');
                };
            }

            $validation = Validator::make($inputs, $rules, $others);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $order = PurchaseItem::find($request->order_id);

            $STEP = $order->purchase_step;

            if($request->status) {
                $order->status = $request->status;
                if($request->status == 'shipped') {
                    if($user->merchant_level === 0) return $this->error('You dont have permission to make the status shipped.',403);
                    //$data = $order->data ? $order->data : [];
                    $data['STATUS_SHIPPED__tracking_number'] = $request->tracking_number;
                    $data['STATUS_SHIPPED__remarks'] = $request->remarks;
                    $data['STATUS_SHIPPED__date'] = date('Y-m-d H:i:s', strtotime(now()));
                    $order->data = $data;
                }
                elseif($request->status == 'cancelled') {
                    //$data = $order->data ? $order->data : [];
                    $data['STATUS_CANCELLED__remarks'] = $request->remarks;
                    $data['STATUS_CANCELLED__date'] = date('Y-m-d H:i:s', strtotime(now()));
                    $order->data = $data;
                }
                elseif($request->status == 'delivered') {
                    //$data = $order->data ? $order->data : [];
                    $data['STATUS_DELIVERED__date'] = date('Y-m-d H:i:s', strtotime(now()));
                    $order->data = $data;
                }
                elseif($request->status == 'completed') {
                    if($order->user_id == $user->id || $order->product->user_id == $user->id) {
                        // do nothing
                    } else return $this->error('You dont have permission to make the status completed.',403);
                    //$data = $order->data ? $order->data : [];
                    $data['STATUS_COMPLETED__date'] = date('Y-m-d H:i:s', strtotime(now()));
                    $order->data = $data;
                }
            }

            $order->save();

            // Check Proper Process
            if($STEP === 4) return $this->error('Actions are not valid.',403);
            elseif($order->purchase_step !== 0) {
                if(($order->purchase_step - $STEP) !== 1) return $this->error('Actions are not valid.',403);
            }

            DB::commit();

            $order = $order->find($order->id);

            return $this->success('Successfully updated the order.', $order);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function getAllMerchantOrders(Request $request)
    {
        try {
            // $orders = PurchaseItem::where('user_id',Auth::user()->id);
            // $orders = PurchaseItem::with(['product'=>function($query){
                //     $query->where('user_id',Auth::user()->id);
                // }])->where('product_id','!=',null);
            $orders = PurchaseItem::
            select('purchase_items.*')
            ->
            leftJoin('products as products_table','purchase_items.product_id', 'products_table.id')
            ->where('products_table.user_id',Auth::user()->id)
            ;
            $orders = $request->page ? $orders->paginate($request->per_page ? $request->per_page : 10) : $orders->get();

            return $this->success('Successfully retrieved the orders.', new PurchaseItems($orders));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function getAllOrders(Request $request)
    {
        try {
            $orders = $request->page ? PurchaseItem::paginate(10) : PurchaseItem::all();

            return $this->success('Successfully retrieved all the orders.', new PurchaseItems($orders));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }
}