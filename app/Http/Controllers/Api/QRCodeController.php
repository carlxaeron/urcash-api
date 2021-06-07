<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\WalletInterface;
use Illuminate\Http\Request;
use App\Interfaces\WalletTransactionInterface;
use App\Interfaces\UserInterface;
use App\Traits\ResponseAPI;
use App\Http\Services\DragonpayService;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\TicketInterface;

class QRCodeController extends Controller
{
    use ResponseAPI;

    protected $walletInterface;
    protected $walletTransactionInterface;
    protected $userInterface;
    protected $dragonpayService;
    protected $ticketInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(
        WalletInterface $walletInterface,
        WalletTransactionInterface $walletTransactionInterface,
        UserInterface $userInterface,
        DragonpayService $dragonpayService,
        TicketInterface $ticketInterface
    ) {
        $this->walletInterface = $walletInterface;
        $this->walletTransactionInterface = $walletTransactionInterface;
        $this->userInterface = $userInterface;
        $this->dragonpayService = $dragonpayService;
        $this->ticketInterface = $ticketInterface;
    }

    public function ticketQRCode(Request $request)
    {
        //todos

        $inputs = [
            'qr_code' => $request->qr_code,
            'mobile_number' => $request->mobile_number
        ];
        $rules = [
            'qr_code' => 'required',
            'mobile_number' => 'required'
        ];

        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) return $this->error($validation->errors()->all());

        //check if qr exist in ticket
        $getTicket = $this->ticketInterface->getTicketByQRCode($request->qr_code);
        $ticket = json_decode($getTicket->getContent(), true);
        //Get User
        $user = $this->userInterface->getUserByMobileNumber($request->mobile_number);
        $userResult = json_decode($user->getContent(), true);
        //Get user wallet
        $wallet = $this->walletInterface->getWalletByUserId($userResult['results']['id']);
        $walletResult = json_decode($wallet->getContent(), true);

        if (isset($ticket['results'])) {
            //process payment if exist in ticket
            //check wallet if user has suficient wallet balance
            if ((double)$walletResult['results']['available_balance'] >= (double)$ticket['results']['amount']) {
                $trans = $this->walletTransactionInterface->insertWalletTransaction(
                    $request->mobile_number,
                    "Ticket",
                    $this->dragonpayService->GenerateTnxId(),
                    "Purchase " . $ticket['results']['title'],
                    "Wallet",
                    $ticket['results']['amount'],
                    0
                );

                $amount = $walletResult['results']['available_balance'] -  $ticket['results']['amount'];
                //update wallet balance
                $this->walletInterface->updateWalletBalance($amount, $walletResult['results']['id']);

                return $this->success('Succesfuly purchased ' . $ticket['results']['title'], $trans);
            }
            else{
                return $this->error('Insufficient balance');
            }
        }
        return $this->error('Ticket not found', 404);
    }

    public function sendAndRequestFundQRCode(Request $request)
    {
        $inputs = [
            'qr_code' => $request->qr_code,
            'mobile_number' => $request->mobile_number,
            'amount' => $request->amount,
        ];
        $rules = [
            'qr_code' => 'required',
            'mobile_number' => 'required',
            'amount' => 'required|regex:/^\d+(\.\d{1,2})?$/'
        ];

        $validation = Validator::make($inputs, $rules);

        if ($validation->fails()) return $this->error($validation->errors()->all());


        //Get wallet who will recieve fund
        $wallet = $this->walletInterface->getWalletByqrcode($request->qr_code);
        $walletResult = json_decode($wallet->getContent(), true);
        //Get user who will recieve fund
        $user = $this->userInterface->getUserById($walletResult['results']['user_id']);
        $userResult = json_decode($user->getContent(), true);

        //Get user who will send fund
        $user1 = $this->userInterface->getUserByMobileNumber($request->mobile_number);
        $userResult1 = json_decode($user1->getContent(), true);
        //Get wallet who will send fund
        $wallet1 = $this->walletInterface->getWalletByUserId($userResult1['results']['id']);
        $walletResult1 = json_decode($wallet1->getContent(), true);

        //this will check if QR code is equal to sender fund QR code
        if($request->qr_code == $walletResult1['results']['qr_code']) return $this->error('Invalid transaction');

        if ((double)$walletResult1['results']['available_balance'] >= (double)$request->amount) {
            //transaction for fund reciever
            $trans = $this->walletTransactionInterface->insertWalletTransaction(
                $userResult['results']['mobile_number'],
                "Request-Fund",
                $this->dragonpayService->GenerateTnxId(),
                "Successfully recieved fund from ".$userResult['results']['first_name'] .' '.$userResult['results']['last_name'],
                "Wallet",
                $request->amount,
                0
            );

            //transaction for fund sender
            $trans = $this->walletTransactionInterface->insertWalletTransaction(
                $request->mobile_number,
                "Send-Fund",
                $this->dragonpayService->GenerateTnxId(),
                "Successfully sent fund to ".$userResult1['results']['first_name'] .' '.$userResult1['results']['last_name'],
                "Wallet",
                $request->amount,
                0
            );

            //add fund for reciever
            $amount = (double)$walletResult['results']['available_balance'] +  (double)$request->amount;
            $this->walletInterface->updateWalletBalance($amount, $walletResult['results']['id']);
            //deduct fund for sender
            $amount1 = (double)$walletResult1['results']['available_balance'] -  (double)$request->amount;
            $this->walletInterface->updateWalletBalance($amount1, $walletResult1['results']['id']);
            return $this->success('Request fund success', $trans);
        }
        else{
            return $this->error('Insufficient balance');
        }
    }
}
