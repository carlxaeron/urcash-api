<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface WalletTransactionInterface
{
    public function insertWalletTransaction(
        $mobile_number,
        $transaction_type,
        $txn_id,
        $transaction_description,
        $payment_method,
        $total_amount,
        $handling_fee
    );

    public function getWalletTransactions(Request $request);

    public function getWalletTransactionsById(Request $request);

    public function updateWalletTransactionStatus($status, $currentTime, $wallettransaction_id);

    public function getWalletTransactionsByTxnId($txn_id);

    public function getWalletTransactionsByWalletId($wallet_id, $status);

}
