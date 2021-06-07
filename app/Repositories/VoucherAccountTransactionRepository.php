<?php

namespace App\Repositories;

use App\Cashout;
use App\Pay;
use App\TransactionType;
use App\VoucherAccountTransaction;
use App\VoucherOrder;
use App\Http\Helper\Utils\GenerateRandomIntegers;
use App\Interfaces\VoucherAccountTransactionInterface;
use App\Traits\ResponseAPI;

class VoucherAccountTransactionRepository implements VoucherAccountTransactionInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getVoucherAccountTransactionByVoucherAccountId($voucherAccountId)
    {
        try {
            $voucherAccountTrans = VoucherAccountTransaction::where('voucher_accounts_id', $voucherAccountId)
                ->orderBy('created_at', 'desc');

            if (emptyArray($voucherAccountTrans)) {
                $this->error("Voucher account transaction not found");
            }

            return $this->success("Voucher account transactions", $voucherAccountTrans);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createVoucherAccountTransaction($voucher_account_id, $trans_id)
    {
        try {
            $generate_random_integers = new GenerateRandomIntegers(1, 9, 12); // Instantiate
            $reference_number = $generate_random_integers->generate();

            // Create Transaction
            $trans = VoucherAccountTransaction::create([
                'ref_number' => $reference_number,
                'voucher_accounts_id' => $voucher_account_id,
                'transaction_type_id' => $trans_id
            ]);

            return $trans;
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateVoucherAccountTransactionStatus($id, $amount)
    {
        try {

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }


    public function getVoucherAccountTransactionById($id)
    {
        try {
            $voucherAccountTrans = VoucherAccountTransaction::find($id);

            if (empty($voucherAccountTrans)) {
                $this->error("Voucher account transaction not found");
            }

            return $voucherAccountTrans;
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getVoucherAccountTransactionsByAccountId($id)
    {
        try {
            $transaction = [];
            $voucherAccountTrans = VoucherAccountTransaction::where('voucher_accounts_id', $id)
                ->orderBy('created_at', 'DESC')->get();

            foreach ($voucherAccountTrans as $trans) {
                $transactionType = TransactionType::find($trans->transaction_type_id);
                $amount = 0;
                $fee = 0;

                $voucherOrder = VoucherOrder::where('voucher_account_transaction_id', $trans->id)->first();
                $cashout = Cashout::where('voucher_account_transaction_id', $trans->id)->first();

                if ($cashout) {
                    $amount = '-'.$cashout->amount;
                    $fee = $cashout->fee;
                }

                if ($voucherOrder) {
                    $amount = '+'. $voucherOrder->amount;
                    $fee = $voucherOrder->fee;
                }

                $transaction[] = [
                    'ref_number' => $trans->ref_number,
                    'transaction_type'=>$transactionType->name,
                    'fee' => number_format($fee, 2, '.', ''),
                    'amount' =>  $amount,
                    'created_at' => $trans->created_at->format('Y-m-d h:i:sa')
                ];
            }

            if (empty($voucherAccountTrans)) {
                $this->error("Voucher account transactions not found");
            }

            return $this->success("Voucher account transactions", $transaction);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPayTransactionsByAccountId($id)
    {
        try {
            $transaction = [];
            $voucherAccountTrans = VoucherAccountTransaction::where('voucher_accounts_id', $id)
                ->orderBy('created_at', 'DESC')->get();

            foreach ($voucherAccountTrans as $trans){
                $transactionType = TransactionType::find($trans->transaction_type_id);

                if ($transactionType) {
                    $pay = Pay::where('voucher_account_transaction_id', $trans->id)->first();
                    if ($pay) {
                        $sign = '+';
                        if ($transactionType->id == 3) {
                            $sign= '-';
                        }

                        $transaction[] = [
                            'ref_number' => $trans->ref_number,
                            'transaction_type' => $transactionType->name,
                            'amount' => $sign.$pay->amount,
                            'created_at' => $trans->created_at->format('Y-m-d h:i:sa')
                        ];
                    }
                }
            }

            if (empty($transaction)) {
                $this->error("Voucher account payment transactions not found", 404);
            }

            return  $this->success("Voucher account payment transactions", $transaction);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
