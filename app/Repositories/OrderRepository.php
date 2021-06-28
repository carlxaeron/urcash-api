<?php
namespace App\Repositories;

use App\Http\Resources\PurchaseItems;
use App\Interfaces\OrderInterface;
use App\PurchaseItem;
use App\Traits\ResponseAPI;
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

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $order = PurchaseItem::find($request->order_id);

            if($request->status) {
                $order->status = $request->status;
            }

            $order->save();

            DB::commit();

            $order = $order->find($order->id);

            return $this->success('Successfully updated the order.', $order);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error($e->getMessage(), (int) $e->getCode());
        }
    }

    public function getAllUserOrders(Request $request)
    {
        try {
            $orders = PurchaseItem::where('user_id',Auth::user()->id);
            $orders = $request->page ? $orders->paginate(10) : $orders->get();

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