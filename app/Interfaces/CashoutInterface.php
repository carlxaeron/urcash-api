<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\User\CashoutEWalletRequest;
use App\Http\Requests\User\CashoutBankRequest;

interface CashoutInterface
{
    /**
     * Get all cahsout
     *
     * @method  GET api/cashouts
     * @access  public
     */
    public function getAllCashout();


    /**
     * Create Ewallet Cashout
     *
     * @method  post api/cashout
     * @access  public
     */
    public function createEwalletCashout(CashoutEWalletRequest $request);


     /**
     * Create Bank Cashout
     *
     * @method  post api/cashout
     * @access  public
     */
    public function createBankCashout(CashoutBankRequest $request);

     /**
     * Create Bank Cashout
     *
     * @method  post api/cashout
     * @access  public
     */
    public function pendingCashouts();

    public function countPendingRequest();
    public function sumOfSuccessfulCashouts();
    public function feesTotalCollected();
    public function approvedCashouts();
    public function countSuccessfulPayment();
    public function countCashoutTransactionOfTheDay();
    public function feesCollectedToday();
    public function approveCashout($id);
    public function rejectCashout($id);
}
