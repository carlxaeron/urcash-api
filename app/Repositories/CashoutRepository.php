<?php

namespace App\Repositories;

use App\Interfaces\CashoutInterface;
use App\Traits\ResponseAPI;
use App\Cashout;
use Illuminate\Http\Request;
use App\VoucherAccount;
use App\Http\Requests\User\CashoutEWalletRequest;
use App\VoucherAccountTransaction;
use Illuminate\Support\Facades\DB;
use App\CashoutBank;
use App\CashoutEWallet;
use App\Http\Requests\User\CashoutBankRequest;
use App\Bank;
use App\EWallet;
use App\Notification;
use App\Shop;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\CashoutSuccessful;

class CashoutRepository implements CashoutInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllCashout()
    {
        try {
            $cashouts = Cashout::all();
            if (empty($cashouts)) {
                return $this->error('Cashouts not found', 404);
            }
            $cashout_order = [];
            foreach( $cashouts as $cashout){
                //voucher_account_transactions
                //Voucher_accounts
                //shop
                $voucherAccountTransaction_repository = new VoucherAccountTransactionRepository();
                $trans = $voucherAccountTransaction_repository->getVoucherAccountTransactionById($cashout->voucher_account_transaction_id);

                $voucher_account = DB::table('voucher_accounts')
                ->where('id', '=',  $trans->voucher_accounts_id)
                ->first();

                $shop = DB::table('shops')
                ->where('id', '=',  $voucher_account->shop_id)
                ->first();

                $payment_method = '';

                $cashoutBank = CashoutBank::where('cashout_id', $cashout->id)->first();

                if($cashoutBank) {
                    $payment_method = Bank::find($cashoutBank->banks_id)->name;
                }
                $cashoutEwallet = CashoutEWallet::where('cashout_id', $cashout->id)->first();

                if($cashoutEwallet)
                {
                    $payment_method = EWallet::find($cashoutEwallet->e_wallet_id)->name;
                }

                $user = DB::table('users')
                ->where('id', '=',  $shop->user_id)
                ->first();

                $cashout_order[] = [
                    'id' => $cashout->id,
                    'date' => $cashout->created_at->format('m/d/Y'),
                    'merchant_name' =>  $shop->reg_bus_name,
                    'cost'=>  $cashout->amount,
                    'payment_method' => $payment_method,
                    'ref_number' => $trans->ref_number,
                    'mobile_number' => $user->mobile_number,
                    'status' => $cashout->status
                ];

            }

            return $this->success('Cashout history', $cashout_order);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createEwalletCashout(CashoutEWalletRequest $request)
    {
        try {

            //Get Voucher Account
            $voucherAccount = VoucherAccount::where('shop_id', $request->shop_id)->first();

            //Create Transaction
            $voucherAccount_repository = new VoucherAccountTransactionRepository();
            $trans_type_id = 2; // Cashout
            $trans = $voucherAccount_repository->createVoucherAccountTransaction($voucherAccount->id,  $trans_type_id);

            //check balance if greater that cashout amount
            $total_amount = $request->amount + ($request->amount * 0.03);

            if ((float)$voucherAccount->voucher_balance < (float)$total_amount) {
                return $this->error('Insufficient balance');
            }

            $cashout = Cashout::create([
                'voucher_account_transaction_id' =>  (int)$trans['id'],
                'amount' => $request->amount,
                'fee' => $request->amount * 0.03,
                'status' => 'Pending'
            ]);

            CashoutEWallet::create([
                'e_wallet_id'=>$request->e_wallet_id,
                'cashout_id' => $cashout->id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]);

            $account = VoucherAccount::find($voucherAccount->id);

            $account->voucher_balance =  $account->voucher_balance - $total_amount;
            $account->save();

            return $this->success('Cashout created', $cashout);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createBankCashout(CashoutBankRequest $request)
    {
        try {

            //Get Voucher Account
            $voucherAccount = VoucherAccount::where('shop_id', $request->shop_id)->first();

            //Create Transaction
            $voucherAccount_repository = new VoucherAccountTransactionRepository();
            $trans_type_id = 2; // Cashout
            $trans = $voucherAccount_repository->createVoucherAccountTransaction($voucherAccount->id,  $trans_type_id);

            //check balance if greater that cashout amount
            $total_amount = $request->amount + ($request->amount * 0.03);

            if ((float)$voucherAccount->voucher_balance < (float)$total_amount) {
                return $this->error('Insufficient balance');
            }

            $cashout = Cashout::create([
                'voucher_account_transaction_id' =>  (int)$trans['id'],
                'amount' => $request->amount,
                'fee' => $request->amount * 0.03,
                'status' => 'Pending'
            ]);

            $cashoutBank = CashoutBank::create([
                'banks_id'=>$request->banks_id,
                'cashout_id' => $cashout->id,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ]);

            $account = VoucherAccount::find($voucherAccount->id);

            $account->voucher_balance =  $account->voucher_balance - $total_amount;
            $account->save();

            $shop = Shop::find( $voucherAccount['shop_id']);

            Notification::create([
                'user_id' => 1,
                'notification_type_id' => 1,
                'title' => 'Cashout Request',
                'message' => $shop->reg_bus_name.' is requesting a cashout with the amount of '.$cashout->amount.' pesos.'
            ]);

            return $this->success('Cashout created', $cashout);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function approveCashout($id)
    {
        try {
            $cashout = Cashout::find($id);

            if (!$cashout) $this->error('Cashout not found', 404);

            $cashout['status'] = "Approved";
            $cashout->save();

            $vaoucherAccountTransaction = VoucherAccountTransaction::find($cashout['voucher_account_transaction_id']);
            $voucherAccount = VoucherAccount::find(  $vaoucherAccountTransaction['voucher_accounts_id']);
            $shop = Shop::find( $voucherAccount['shop_id']);
            $user = User::find($shop['user_id']);

            $cashoutBank = CashoutBank::where('cashout_id', $cashout->id)->first();

            if ($cashoutBank) {
                $payment_method = Bank::find($cashoutBank->banks_id);
            }
            $cashoutEwallet = CashoutEWallet::where('cashout_id', $cashout->id)->first();

            if ($cashoutEwallet)  {
                $payment_method = EWallet::find($cashoutEwallet->e_wallet_id);
            }

            Notification::create([
                'user_id' => $user->id,
                'notification_type_id' => 1,
                'title' => 'Cashout Approved',
                'message' => 'Your cashout request with the amount of ' .$cashout->amount. ' pesos was successfully approved and transferred to your ' .$payment_method->name.' account',
            ]);

            Mail::to($user)->send(new CashoutSuccessful($user, $cashout));

            $action = 'Cashout ID ' .$cashout->id. ' was approved';
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Cashout Approved", $cashout);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function rejectCashout($id)
    {
        try {
            $cashout = Cashout::find($id);

            if (!$cashout) return $this->error("Cashout not found", 404);

            if ($cashout['status'] == "Rejected") return $this->error("Status already rejected");

            $cashout['status'] = "Rejected";
            $total_amount = $cashout->amount + $cashout->fee;
            $cashout->save();

            $trans = VoucherAccountTransaction::find($cashout['voucher_account_transaction_id']);
            $account = VoucherAccount::find($trans->voucher_accounts_id);

            $account->voucher_balance =  $account->voucher_balance + $total_amount;
            $account->save();

            $action = 'Cashout ID ' .$cashout->id. ' was rejected';
            $admin_log_repository = new AdminLogRepository();
            $create_admin_log = $admin_log_repository->createAdminLog($action);

            if ($create_admin_log->getData()->statusCode == 500 or $create_admin_log->getData()->statusCode == 401) {
                return $this->error($create_admin_log->getData()->message);
            }

            return $this->success("Cashout Rejected", $cashout);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function pendingCashouts()
    {
        try {
            $cashouts = Cashout::where('status', '=', 'Pending')->orderBy('created_at', 'DESC')->get();

            $orderCashout = [];

            foreach ($cashouts as $cashout) {
                $voucherAccountTransaction_repository = new VoucherAccountTransactionRepository();
                $cashout_trans = $voucherAccountTransaction_repository->getVoucherAccountTransactionById($cashout->voucher_account_transaction_id);

                $voucher_account = DB::table('voucher_accounts')
                ->where('id', '=',  $cashout_trans->voucher_accounts_id)
                ->first();

                $shop = DB::table('shops')
                ->where('id', '=',  $voucher_account->shop_id)
                ->first();

                $user = DB::table('users')
                ->where('id', '=',  $shop->user_id)
                ->first();


                $cashoutBank = CashoutBank::where('cashout_id', $cashout->id)->first();

                if($cashoutBank) {
                    $bank = Bank::find($cashoutBank->banks_id);
                    $payment_method = $bank->name;
                    $account_name = $cashoutBank->account_name;
                    $account_number = $cashoutBank->account_number;
                }

                $cashoutEwallet = CashoutEWallet::where('cashout_id', $cashout->id)->first();

                if($cashoutEwallet)
                {
                    $e_wallet = EWallet::find($cashoutEwallet->e_wallet_id);
                    $payment_method = $e_wallet->name;
                    $account_name = $cashoutEwallet->account_name;
                    $account_number = $cashoutEwallet->account_number;
                }

                $orderCashout[] = [
                    'id' => $cashout->id,
                    'date' => $cashout->created_at->format('m/d/Y'),
                    'merchant_name' =>  $shop->reg_bus_name,
                    'cost'=>  $cashout->amount,
                    'payment_method' => $payment_method,
                    'account_name' => $account_name,
                    'account_number' => $account_number,
                    'ref_number' => $cashout_trans->ref_number,
                    'status' => $cashout->status
                ];

            }

            return $this->success("Pending cashouts",$orderCashout);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function countPendingRequest()
    {
        try {
            $pending_cashouts = Cashout::where('status','=','Pending')->get();

            return $this->success("Pending cashouts",array('pending_cashouts'=> $pending_cashouts->count()));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function countSuccessfulPayment()
    {
        try {
            $successfulPayment = Cashout::where('status','=','Payment Successful')->get();

            return $this->success("Cashout successful payments",array('successfull_payments'=> $successfulPayment->count()));

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function sumOfSuccessfulCashouts()
    {
        try {
            $successfulCashouts = Cashout::where('status','=','Payment Successful')->get()->sum('amount');

            return $this->success("Successful cashouts",array('successful_cashouts'=> $successfulCashouts));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function feesTotalCollected()
    {
        try {
            $cashoutsfeesCollected = Cashout::where('status','=','Payment Successful')->get()->sum('fee');

            return $this->success("Fees Collected",array('fees_collected'=>$cashoutsfeesCollected) );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function approvedCashouts()
    {
        try {
            $successfulCashouts = Cashout::where('status','=','Approved')->get();

            $orderCashout = [];

            foreach ($successfulCashouts as $cashout) {
                $voucherAccountTransaction_repository = new VoucherAccountTransactionRepository();
                $cashout_trans = $voucherAccountTransaction_repository->getVoucherAccountTransactionById($cashout->voucher_account_transaction_id);

                $voucher_account = DB::table('voucher_accounts')
                ->where('id', '=',  $cashout_trans->voucher_accounts_id)
                ->first();

                $shop = DB::table('shops')
                ->where('id', '=',  $voucher_account->shop_id)
                ->first();

                $user = DB::table('users')
                ->where('id', '=',  $shop->user_id)
                ->first();

                $cashoutBank = CashoutBank::where('cashout_id', $cashout->id)->first();

                if($cashoutBank) {
                    $bank = Bank::find($cashoutBank->banks_id);
                    $payment_method = $bank->name;
                    $account_name = $cashoutBank->account_name;
                    $account_number = $cashoutBank->account_number;
                }

                $cashoutEwallet = CashoutEWallet::where('cashout_id', $cashout->id)->first();

                if($cashoutEwallet)
                {
                    $e_wallet = EWallet::find($cashoutEwallet->e_wallet_id);
                    $payment_method = $e_wallet->name;
                    $account_name = $cashoutEwallet->account_name;
                    $account_number = $cashoutEwallet->account_number;
                }

                $orderCashout[] = [
                    'id' => $cashout->id,
                    'date' => $cashout->created_at->format('m/d/Y'),
                    'merchant_name' =>  $shop->reg_bus_name,
                    'cost'=>  $cashout->amount,
                    'payment_method' => $payment_method,
                    'account_name' => $account_name,
                    'account_number' => $account_number,
                    'ref_number' => $cashout_trans->ref_number,
                    'status' => $cashout->status
                ];

            }

            if(empty($orderCashout))
            {
                return $this->error('Approved cashouts not found', 404);
            }

            return $this->success("Approved Cashouts",array('approved_cashouts'=> $orderCashout));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function countCashoutTransactionOfTheDay()
    {
        try {
            $successfulCashouts = Cashout::whereRaw('Date(created_at) = CURDATE()')->get()->sum('amount');

            return $this->success("Total cashouts of the day",array('today_cashout'=>  $successfulCashouts));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function feesCollectedToday()
    {
        try {
            $totalFees = Cashout::where('status','=','Payment Successful')->whereRaw('Date(created_at) = CURDATE()')->get()->sum('fee');

            return $this->success("Total fees collected of the day",array('total_fees'=>  $totalFees));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
