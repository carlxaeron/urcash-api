<?php

namespace App\Repositories;

use App\PaymentMethod;
use App\Shop;
use App\Voucher;
use App\VoucherAccount;
use App\VoucherAccountTransaction;
use App\VoucherOrder;
use App\Http\Helper\Utils\UploadImage;
use App\Interfaces\VoucherOrderInterface;
use App\Traits\ResponseAPI;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VoucherOrderRepository implements VoucherOrderInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getVoucherOrders()
    {
        try {
            $voucherOrders = VoucherOrder::orderBy('created_at', 'DESC')->get();

            return $this->success("Voucher orders", $voucherOrders);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVoucherOrderById($id)
    {
        try {
            $voucherOrder = VoucherOrder::find($id);
            $$voucherOrder['receipt'] = url('/'.$voucherOrder['receipt']);
            return $this->success("Voucher order", $voucherOrder);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createVoucherOrder($request)
    {
        try {
            $inputs = [
                'shop_id' => $request->shop_id,
                'payment_method_id' => $request->payment_method_id,
                'voucher_id' => $request->voucher_id,
                'number_of_vouchers' => $request->number_of_vouchers,
            ];
            $rules = [
                'shop_id' => 'required',
                'payment_method_id' => 'required',
                'voucher_id' => 'required',
                'number_of_vouchers' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            // Get Voucher Account
            $voucherAccount = VoucherAccount::where('shop_id', $inputs['shop_id'])->first();

            // Create Transaction
            $voucherAccount_repository = new VoucherAccountTransactionRepository();
            $trans_id = 1; // Purchase Voucher
            $trans = $voucherAccount_repository->createVoucherAccountTransaction($voucherAccount->id, $trans_id);

            // Get voucher
            $voucher = Voucher::find($request->voucher_id);

            $fee = ($voucher->amount * $request->number_of_vouchers) * .03;
            $inputs['voucher_account_transaction_id'] = (int)$trans['id'];
            $inputs['transaction_description'] = 'Purchase Voucher';
            $inputs['amount'] = (float)$voucher->amount * $request->number_of_vouchers;
            $inputs['fee'] =  (float)$fee;
            $inputs['status'] = "Unpaid";

            // Create Order
            $voucherOrders = VoucherOrder::create($inputs);
            $paymentMethod = PaymentMethod::find($voucherOrders->payment_method_id);

            return $this->success("Voucher orders", array(
                "voucher_order" => $voucherOrders,
                "payment_method" => $paymentMethod
            ));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function uploadProofOfPayment($request)
    {
        try {
            $voucherOrder = VoucherOrder::find($request->get('voucher_orders_id'));

            $file_name = 'proof_of_payment' . $request->voucher_orders_id . '_' . time();
            $directory = '/images/proof-of-payments/';

            $upload_image = new UploadImage($request, 'select_file', $file_name, $directory);
            $file_path = $upload_image->upload();

            $voucherOrder['proof_of_payment'] = $file_path ;
            $voucherOrder['status'] = "To Verify";
            $voucherOrder->save();

            return $this->success("Proof of payment upload successfully", ["file_path" => $file_path]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function verifyVoucherOrder($id)
    {
        try {
            $voucherOrder = VoucherOrder::find($id);

            if ($voucherOrder->status == 'Verified') return $this->error("Voucher order was previously verified");

            $voucherOrder['status'] = "Verified";
            $amount = $voucherOrder['amount'];

            $voucherOrder->save();
            $trans = VoucherAccountTransaction::find($voucherOrder['voucher_account_transaction_id']);
            $account = VoucherAccount::find($trans['voucher_accounts_id']);
            $account['voucher_balance'] = $account['voucher_balance'] + $amount;
            $account->save();
            $shop = Shop::find($account->shop_id);

            $action = 'Voucher order #' .$voucherOrder->id. ' was verified';
            $notes = $amount. ' worth of vouchers was added to ' .$shop->reg_bus_name;
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action, $notes);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Verified", $voucherOrder);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function rejectVoucherOrder($id)
    {
        try {
            $voucherOrder = VoucherOrder::find($id);

            if ($voucherOrder->status == 'Rejected') return $this->error("Voucher order was previously rejected");

            $voucherOrder['status'] = "Rejected";
            $voucherOrder->save();

            $action = 'Voucher order #' .$voucherOrder->id. ' was rejected';
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Rejected",  $voucherOrder);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }


    public function toVerify()
    {
        try {
            $voucherOrders = VoucherOrder::where('status', '=', 'To Verify')->orderBy('created_at', 'DESC')->get();

            $orders = $this->voucherOrders($voucherOrders);

            return $this->success("Voucher orders", array("orders" => $orders));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function history()
    {
        try {
            $voucherOrders = VoucherOrder::where('status', '!=', 'To Verify')->orderBy('created_at', 'DESC')->get();

            $orders = $this->voucherOrders($voucherOrders);

            return $this->success("Voucher orders", $orders);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    private function voucherOrders($voucherOrders){
        $orders = [];

        foreach ($voucherOrders as $order) {
            $voucherAccountTransaction_repository = new VoucherAccountTransactionRepository();
            $trans = $voucherAccountTransaction_repository->getVoucherAccountTransactionById($order->voucher_account_transaction_id);

            $voucher_account = DB::table('voucher_accounts')
            ->where('id', '=',  $trans->voucher_accounts_id)
            ->first();

            $shop = DB::table('shops')
            ->where('id', '=',  $voucher_account->shop_id)
            ->first();

            $user = DB::table('users')
            ->where('id', '=',  $shop->user_id)
            ->first();

            $payment_method =  DB::table('payment_methods')
            ->where('id', '=',  $order->payment_method_id)
            ->first();

            if ($order->proof_of_payment != null) {
                $orders[] = [
                    'id' => $order->id,
                    'date' => $order->created_at->format('m/d/Y'),
                    'merchant_name' =>  $shop->reg_bus_name,
                    'cost'=>  $order->amount,
                    'proof_of_payment' => $order->proof_of_payment,
                    'payment_method' => $payment_method->title,
                    'ref_number' => $trans->ref_number,
                    'mobile_number' => $user->mobile_number,
                    'status' => $order->status
                ];
            }
        }
        return $orders;
    }

    public function countPendingRequest()
    {
        try {
            $voucherOrders = VoucherOrder::where('status', '=', 'To Verify')->orderBy('created_at', 'DESC')->get();

            return $this->success("Voucher Purchases", array("voucher_purchases" => $voucherOrders->count()));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }


    public function unpaid()
    {
        try {
            $unpaidVouchers = VoucherOrder::where('status', '=', 'Unpaid')->get()->sum('amount');

            return $this->success("Unpaid Vouchers", array("unpaid_vouchers" => $unpaidVouchers));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function feesCollected()
    {
        try {
            $voucherOrders = VoucherOrder::where('status', '=', 'Verified')->get()->sum('fee');

            return $this->success("Fees Collected", array("fees_collected" => $voucherOrders));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function voucherSold()
    {
        try {
            $voucherSold = VoucherOrder::where('status', '=', 'Verified')->get()->sum('amount');

            return $this->success("Vouchers sold", array("voucher_sold" => $voucherSold));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function noProofOfPayment($id)
    {
        try {
            $voucherAccountTransactions = VoucherAccountTransaction::where('voucher_accounts_id', '=', $id)
                ->where('transaction_type_id','1')->get();
            $transactions = [];

            foreach ($voucherAccountTransactions as $trans) {
                $noProofOfPayment = VoucherOrder::where('voucher_account_transaction_id','=', $trans->id)->get();

                if (isset($noProofOfPayment)) {
                    foreach ($noProofOfPayment as $order) {
                        if ($order->proof_of_payment == null) {
                            $paymentMethod = PaymentMethod::find($order->payment_method_id);
                            $transactions[] = [
                                'id' => $order->id,
                                'payment_method' => $paymentMethod,
                                'voucher_id' => $order->voucher_id,
                                'trasaction_description' => $order->transaction_description,
                                'no_of_vouchers' => $order->number_of_vouchers,
                                'proof_of_payment' => $order->proof_of_payment,
                                'amount' => $order->amount,
                                'fee' => $order->fee
                            ];
                        }
                    }
                }
            }

            return $this->success("Voucher orders with no proof of payment", $transactions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function cancelOrder($id)
    {
        try {
            $voucherOrder = VoucherOrder::find($id);

            if (!$voucherOrder) return $this->error("Voucher order not found", 404);

            $voucherAccountTransaction = VoucherAccountTransaction::find($voucherOrder->voucher_account_transaction_id);

            $voucherAccountTransaction->delete();
            $voucherOrder->delete();

            return $this->success("Voucher order successfully cancelled", true);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
