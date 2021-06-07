<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\CashoutInterface;
use App\Http\Requests\User\CashoutEWalletRequest;
use App\Http\Requests\User\CashoutBankRequest;


class CashoutController extends Controller
{
    protected $cashoutInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(CashoutInterface $cashoutInterface)
    {
        $this->cashoutInterface = $cashoutInterface;
    }

    public function ewalletCashout(CashoutEWalletRequest $request)
    {

        return $this->cashoutInterface->createEwalletCashout($request);
    }

    public function bankCashout(CashoutBankRequest $request)
    {
        return $this->cashoutInterface->createBankCashout($request);
    }

    public function cashouts()
    {
        return $this->cashoutInterface->getAllCashout();
    }

    public function pendingCashouts()
    {
        return $this->cashoutInterface->pendingCashouts();
    }

    public function countPendingRequest()
    {
        return $this->cashoutInterface->countPendingRequest();
    }
    public function sumOfSuccessfulCashouts()
    {
        return $this->cashoutInterface->sumOfSuccessfulCashouts();
    }
    public function feesTotalCollected()
    {
        return $this->cashoutInterface->feesTotalCollected();
    }
    public function approvedCashouts()
    {
        return $this->cashoutInterface->approvedCashouts();
    }
    public function countSuccessfulPayment()
    {
        return $this->cashoutInterface->countSuccessfulPayment();
    }
    public function countCashoutTransactionOfTheDay()
    {
        return $this->cashoutInterface->countCashoutTransactionOfTheDay();
    }
    public function feesCollectedToday()
    {
        return $this->cashoutInterface->feesCollectedToday();
    }
    public function approve($id)
    {
        return $this->cashoutInterface->approveCashout($id);
    }
    public function reject($id)
    {
        return $this->cashoutInterface->rejectCashout($id);
    }
}
