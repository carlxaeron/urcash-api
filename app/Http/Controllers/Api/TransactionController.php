<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\VoucherAccountTransactionInterface;
use App\Traits\ResponseAPI;

class TransactionController extends Controller
{
    use ResponseAPI;

    protected $voucherAccountTransactionInterface;

    public function __construct(VoucherAccountTransactionInterface $voucherAccountTransactionInterface)
    {
        $this->voucherAccountTransactionInterface = $voucherAccountTransactionInterface;
    }

    public function vouncherTransactions($id)
    {
        return $this->voucherAccountTransactionInterface->getVoucherAccountTransactionsByAccountId($id);
    }

    public function payTransaction($id){
        return $this->voucherAccountTransactionInterface->getPayTransactionsByAccountId($id);
    }
}
