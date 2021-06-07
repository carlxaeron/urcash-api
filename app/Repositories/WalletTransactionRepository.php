<?php

namespace App\Repositories;

use App\WalletTransaction;
use App\Interfaces\WalletTransactionInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletTransactionRepository implements WalletTransactionInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function insertWalletTransaction(
        $mobile_number,
        $transaction_type,
        $txn_id,
        $transaction_description,
        $payment_method,
        $total_amount,
        $handling_fee
        )
    {
        try {
            $getUserId = DB::table('users')
                ->where('mobile_number', '=',  $mobile_number)
                ->first()->id;

            $getWalletId = DB::table('wallets')
                ->where('user_id', '=',  $getUserId)
                ->first()->id;

            $status = '';
            if ($transaction_type == 'B2P' || $transaction_type == 'Ticket' || $transaction_type == 'Request-Fund' || $transaction_type == 'Send-Fund') {
                $status = 'S';
            } else {
                $status = 'P';
            }

            $wallet_trans = [
                    'transaction_type' => $transaction_type,
                    'txn_id' => $txn_id,
                    'wallet_id' => $getWalletId,
                    'transaction_description' => $transaction_description,
                    'payment_method' => $payment_method,
                    'amount' => $total_amount,
                    'charge_amount' => $handling_fee,
                    'created_at' => now(),
                    'status' =>  $status
                ];

            $wallet_transactions = WalletTransaction::create($wallet_trans);

            //$wallet_transactionsResult = json_decode($wallet_transactions->getContent(), true);

            return $wallet_transactions;
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getWalletTransactions(Request $request)
    {
    }

    public function getWalletTransactionsById(Request $request)
    {
    }

    public function updateWalletTransactionStatus($status, $currentTime, $wallettransaction_id)
    {
        try {
            DB::update('UPDATE wallet_transactions SET status = ?, updated_at = ? WHERE id = ?', [$status, $currentTime, $wallettransaction_id]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getWalletTransactionsByTxnId($txn_id)
    {
        try {
            $wallet_transaction = DB::table('wallet_transactions')
                ->where('txn_id', '=',  $txn_id)
                ->first();

            return $this->success("User wallet transaction", $wallet_transaction);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
    public function getWalletTransactionsByWalletId($wallet_id, $status)
    {
        try {
            $wallet_transaction = DB::table('wallet_transactions')
                ->where('wallet_id', '=',  $wallet_id)
                ->where('transaction_type', '=' ,'Payout')
                ->where('status', '=',  $status)
                ->first();

            return $this->success("User wallet transaction", $wallet_transaction);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
