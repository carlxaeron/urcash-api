<?php
namespace App\Repositories;

use App\Interfaces\PaymentInterface;
use App\Invoice;
use App\Price;
use App\Product;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentRepository implements PaymentInterface
{
    use ResponseAPI;

    public function generatePaymentGateway()
    {
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
        CURLOPT_URL => config('piopiayay_payment.endpoint'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '.base64_encode(config('piopiayay_payment.id').':'.config('piopiayay_payment.key')),
        ),
        ));

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($curl));

        curl_close($curl);
        dd($response);

        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_URL,config('piopiayay_payment.endpoint'));
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, "login=mylogin&password=mypassword");
                
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
        // $server_output = curl_exec ($ch);
                
        // echo $server_output;
                
        // if ($errno = curl_errno($ch)) {
        //     echo $errno;
        // }
                
        // curl_close ($ch);
    }

    protected $amount = 0;
    public function paymentRequest(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'items' => $request->items,
            ];
            $rules = [
                'items.*.product' => 'required|exists:products,id|exists:prices,product_id',
                'items.*.qty' => 'required',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $items = collect($request->items);
            $items->each(function($v) {
                $price = Price::where('product_id',$v['product'])->first();
                $this->amount += $price->price * $v['qty'];
            });

            $user = Auth::user();

            Invoice::create($_inv = [
                'user_id'=>$user->id,
                'amount'=>$this->amount,
                'status'=>'draft',
                'type'=>'CHECKOUT_ITEMS',
                'data'=>[
                    'CHECKOUT_ITEMS__items'=>$items->toArray(),
                    'CHECKOUT_ITEMS__reference'=>\Str::random(20),
                ]
            ]);

            DB::commit();

            return $this->success('Success saved items.', [
                'amount'=>$this->amount,
                'txnid'=>$_inv['data']['CHECKOUT_ITEMS__reference']
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }

    public function paymentChecker(Request $request)
    {
        try {
            $inputs = [
                'txnid' => $request->txnid,
            ];
            $rules = [
                'txnid' => 'required',
            ];
            $validation = Validator::make($inputs, $rules);

            $invoice = Invoice::checkoutItemsRef($request->txnid)->first();
            return $this->success('Success checker.', [
                'status'=>$invoice->status ?? false,
            ]);

            if ($validation->fails()) return $this->error($validation->errors()->all());
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function paymentCallback(Request $request)
    {
        DB::beginTransaction();
        try {
            $inputs = [
                'status' => $request->status,
                'txnid' => $request->txnid,
                'refno' => $request->refno,
                'digest' => $request->digest,
            ];
            $rules = [
                'status' => 'required',
                'txnid' => ['required',function($attr,$value,$fail){
                    if(!Invoice::checkoutItemsRef($value)->first()) $fail('Invalid TXN ID');
                }],
                'refno' => 'required',
                'digest' => 'required',
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // @todo: add security here

            $invoice = Invoice::checkoutItemsRef($request->txnid)->first();
            if($invoice->status !== 'draft') return $this->error('Already validated this invoice.');

            if($inputs['status'] == 'S') {
                $invoice->status = 'paid';
                $invoice->save();
            } else {
                $invoice->status = 'unpaid';
                $invoice->save();
            }

            DB::commit();

            // return $this->success('Success validated products.', $invoice);
            return response('<script>window.close();</script>');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage());
        }
    }
}