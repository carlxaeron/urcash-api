<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\TicketPurchaseInterface;
use App\Interfaces\UserInterface;
use App\Interfaces\WalletInterface;
use App\Interfaces\TicketInterface;
use App\Traits\ResponseAPI;
use App\Interfaces\WalletTransactionInterface;
use App\Http\Services\DragonpayService;

class TicketPurchaseController extends Controller
{
    use ResponseAPI;

    protected $ticketPurchaseInterface;
    protected $userInterface;
    protected $walletInterface;
    protected $ticketInterface;
    protected $walletTransactionInterface;
    protected $dragonpayService;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(
        TicketPurchaseInterface $ticketPurchaseInterface,
        UserInterface $userInterface,
        WalletInterface $walletInterface,
        TicketInterface $ticketInterface,
        WalletTransactionInterface $walletTransactionInterface,
        DragonpayService $dragonpayService
    ) {
        $this->ticketPurchaseInterface = $ticketPurchaseInterface;
        $this->userInterface = $userInterface;
        $this->walletInterface = $walletInterface;
        $this->ticketInterface = $ticketInterface;
        $this->walletTransactionInterface = $walletTransactionInterface;
        $this->dragonpayService = $dragonpayService;
    }

    public function purchaseTicket(Request $request)
    {
        //TO DO
        //Get User
        $user = $this->userInterface->getUserByMobileNumber($request->mobile_number);
        $userResult = json_decode($user->getContent(), true);
        //Get user wallet
        $wallet = $this->walletInterface->getWalletByUserId($userResult['results']['id']);
        $walletResult = json_decode($wallet->getContent(), true);
        //get ticket amount
        $ticketAmount = json_decode($this->ticketInterface->getTicketById($request->ticket_id)->getContent(), true);
        //calculate ticket amount and number of tickets
        $ticketTotalAmount =  $ticketAmount['results']['amount'] * $request->no_of_tickets;
        $fee = $ticketTotalAmount * 0.01;
        //total calculated amount
        $totalAmount = $ticketTotalAmount + $fee;
        //check wallet balance
        if ($walletResult['results']['available_balance'] < $totalAmount) return $this->error('Insufficient funds please top-up');
        //insert wallet transaction
        $trans = $this->walletTransactionInterface->insertWalletTransaction(
            $request->mobile_number,
            "B2P",
            $this->dragonpayService->GenerateTnxId(),
            "Purchase Ticket",
            "Wallet",
            $ticketTotalAmount,
            $fee
        );
        if (json_decode($trans->getContent(), true)['results']['wallet_transaction'] == false) return $this->error('There is somehting wrong with the process. Please try again');

        //deduct user wallet
        $amount = $walletResult['results']['available_balance'] - $totalAmount;
        //update wallet balance
        $this->walletInterface->updateWalletBalance($amount, $walletResult['results']['id']);

        return $this->success('Purchase ticket success', array('total_amount'=>$totalAmount));
    }
}
