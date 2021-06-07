<?php

namespace App\Repositories;

use App\Pay;
use App\VoucherAccount;
use App\Interfaces\VoucherAccountInterface;
use App\Traits\ResponseAPI;
use Illuminate\Support\Facades\DB;

class VoucherAccountRepository implements VoucherAccountInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getVoucherAccountByShopId($shop_id)
    {
        try {
            $voucherBalance = DB::table('voucher_accounts')
                ->where('shop_id', '=',  $shop_id)->first();

            if ($voucherBalance) {
                return $this->success("Voucher balance", array(
                    'voucher_balance' => $voucherBalance->voucher_balance
                ));
            }

            return $this->error("Voucher balance not found", 404);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function pay($payor_shop_id, $payee_shop_id, $amount)
    {
        try {
            $payor = VoucherAccount::where('shop_id', $payor_shop_id)->first();
            $payee = VoucherAccount::where('shop_id', $payee_shop_id)->first();

            if (!$payor) {
                return $this->error("Payor not found", 404);
            }
            if (!$payee) {
                return $this->error("Payee not found", 404);
            }

            if ($payor->voucher_balance > $amount) {
                // Deduct payor balance
                $update_payor_balance = $payor->voucher_balance - $amount;
                $payor->update(['voucher_balance' => $update_payor_balance]);

                // Add payee balance
                $update_payee_balance = $payee->voucher_balance + $amount;
                $payee->update(['voucher_balance' => $update_payee_balance]);

                // Create Transaction
                $voucherAccount_repository = new VoucherAccountTransactionRepository();
                $payment_sent = 3; // Pay
                $payment_received = 4; // Pay
                $payor_trans = $voucherAccount_repository->createVoucherAccountTransaction($payor->id, $payment_sent);
                $payee_trans = $voucherAccount_repository->createVoucherAccountTransaction($payee->id, $payment_received);

                Pay::create([
                    'voucher_account_transaction_id' => $payor_trans['id'],
                    'payor_shop_id' => $payor_shop_id,
                    'payee_shop_id' => $payee_shop_id,
                    'amount' => $amount
                ]);

                Pay::create([
                    'voucher_account_transaction_id' => $payee_trans['id'],
                    'payor_shop_id' => $payor_shop_id,
                    'payee_shop_id' => $payee_shop_id,
                    'amount' => $amount
                ]);

                return $this->success("Payment successful", $amount);
            } else {
                return $this->error("Your Voucher Balance is not enough to complete this transaction!");
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
