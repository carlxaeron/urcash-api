<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\WalletInterface;
use Illuminate\Http\Request;
use App\Interfaces\WalletTransactionInterface;
use App\Interfaces\UserInterface;
use App\Traits\ResponseAPI;
use App\Http\Services\DragonpayService;

class WalletController extends Controller
{
    use ResponseAPI;

    protected $walletInterface;
    protected $walletTransactionInterface;
    protected $userInterface;
    protected $dragonpayService;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(
        WalletInterface $walletInterface,
        WalletTransactionInterface $walletTransactionInterface,
        UserInterface $userInterface,
        DragonpayService $dragonpayService
    ) {
        $this->walletInterface = $walletInterface;
        $this->walletTransactionInterface = $walletTransactionInterface;
        $this->userInterface = $userInterface;
        $this->dragonpayService = $dragonpayService;
    }

    /**
     * Get user wallet.
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getWallet(Request $request)
    {
        return $this->walletInterface->getWallet($request);
    }

    /**
     * fund user wallet via dragonpay.
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function dragonpayFundWallet(Request $request)
    {
        $fee = $request->amount * 0.01;
        $totalAmount =  number_format((float)$request->amount + $fee, 2, '.', '');

        $payService = $this->dragonpayService;
        $result = $payService->pay($totalAmount, $request->email);

        $fundingInfo = array(
            'transactionId' => $result['txn_id'],
            'fundAmount' => $request->amount,
            'handlingFee' => $fee,
            'totalPayment' => $totalAmount,
            'CreatedTime' => now()
        );

        $this->walletTransactionInterface->insertWalletTransaction(
            $request->mobile_number,
            "Fund",
            $result['txn_id'],
            "AEX fund wallet",
            "Dragonpay",
            $request->amount,
            $fee
        );

        return array('url' => $result['url'], 'fundingInfo' => $fundingInfo);
    }

    /**
     * User payout via dragonpay.
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function dragonpayWithdrawFund(Request $request)
    {
        //Get user
        $fee = $request->amount * 0.01;
        $totalAmount = number_format((float)$request->amount + $fee, 2, '.', '');

        $user = $this->userInterface->getUserByMobileNumber($request->mobile_number);
        $userResult = json_decode($user->getContent(), true);
        //Get user wallet
        $wallet = $this->walletInterface->getWalletByUserId($userResult['results']['id']);
        $walletResult = json_decode($wallet->getContent(), true);
        //get user wallet transaction
        $walletTransaction = $this->walletTransactionInterface->getWalletTransactionsByWalletId($walletResult['results']['id'], "P");
        $walletTransactionResult = json_decode($walletTransaction->getContent(), true);

        if (!empty($walletTransactionResult['results'])) {
            return $this->error("Please wait for the result of your pending transaction to proceed with this process.");
        }

        if ((float)$walletResult['results']['available_balance'] <= (float)$totalAmount) {
            return $this->error("Insufficient funds");
        }

        $payService = $this->dragonpayService;

        $result = $payService->payout($userResult['results'], $request->amount, $request->proc_id);

        return $result;
    }

    /**
     * Postback dragonpay.
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function postBack(Request $request)
    {
        $payService = $this->dragonpayService;
        $result = $payService->postBack($request);
        return $result;
    }

    /**
     * Get wallet by QR code
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getWalletByQRCode(Request $request)
    {
        return $this->walletInterface->getWalletByqrcode($request->qr_code);
    }

    /**
     * Get wallet by QR code
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getQRCodeByMobileNumber(Request $request)
    {
        return $this->walletInterface->getQrCodeByMobileNumber($request);
    }
}
