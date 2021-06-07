<?php

namespace App\Http\Controllers\View;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\DragonpayService;
use App\Interfaces\WalletTransactionInterface;

class WalletController extends Controller
{
    protected $walletTransactionInterface;
    protected $dragonpayService;
    /**
     * Create a new constructor for this controller
     */
    public function __construct(WalletTransactionInterface $walletTransactionInterface, DragonpayService $dragonpayService)
    {
        $this->walletTransactionInterface = $walletTransactionInterface;
        $this->dragonpayService = $dragonpayService;
    }

    public function successfulFunding(Request $request){

        $currentTime = $this->dragonpayService->GetCurrentTime();


        $wallettransaction = $this->walletTransactionInterface->getWalletTransactionsByTxnId($request->txnid);
        $result = json_decode($wallettransaction->getContent(), true);

        $this->walletTransactionInterface->updateWalletTransactionStatus($result['results']['status'], $currentTime, $result['results']['id']);

        return View('successfunding', ['amount'=>$result['results']['amount'],'chargeAmount'=>$result['results']['charge_amount'] ]);
    }
}
