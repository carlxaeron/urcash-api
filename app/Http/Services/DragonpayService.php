<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Interfaces\WalletInterface;
use App\Interfaces\WalletTransactionInterface;

/**
 * Class RandomStringGenerator
 * @package DragonpayService
 *
 */
class DragonpayService
{
    use ResponseAPI;

    protected $walletInterface;
    protected $walletTransactionInterface;
    protected $userInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(
        WalletInterface $walletInterface,
        WalletTransactionInterface $walletTransactionInterface
    ) {
        $this->walletInterface = $walletInterface;
        $this->walletTransactionInterface = $walletTransactionInterface;
    }


    //get database current time
    public function GetCurrentTime()
    {
        $getTime = DB::select('Select now() as date_now');
        return $getTime[0]->date_now;
    }

    public function GenerateTnxId()
    {
        $num = 0;
        $getLatestTransactionId =  DB::select("SELECT * FROM wallet_transactions ORDER by id DESC LIMIT 1");
        if ($getLatestTransactionId == null) {
            $num = 0;
        } else {
            $num = $getLatestTransactionId[0]->id + 1;
        }
        return date('YmdHms') . ($num);
    }
    //Pay in
    public function pay($amount, $email)
    {

        $parameters = array(
            'merchantid' => env('MERCHANT_ID'),
            'txnid' => $this->GenerateTnxId(),
            'amount' => $amount,
            'ccy' => 'PHP',
            'description' =>  "G2GBox Fund wallet",
            'email' =>  $email
        );

        // Transform amount to correct format. (2 decimal places,
        // decimal separated by period, no thousands separator)
        $parameters['amount'] = number_format($parameters['amount'], 2, '.', '');
        // Unset later from parameter after digest.
        $parameters['key'] = env('MERCHANT_PASSWORD');
        $digest_string = implode(':', $parameters);
        unset($parameters['key']);
        // NOTE: To check for invalid digest errors,
        // uncomment this to see the digest string generated for computation.
        // var_dump($digest_string); $is_link = true;
        $parameters['digest'] = sha1($digest_string);

        if (env('APP_ENV') == "local") {
            $url = 'http://test.dragonpay.ph/Pay.aspx?';
        } elseif (env('APP_ENV') == "production") {
            $url = 'https://gw.dragonpay.ph/Pay.aspx?';
        }

        $result = array('url' => $url .= http_build_query($parameters, '', '&'), 'txn_id' => $parameters['txnid']);

        return $result;;
    }
    //Pay out
    public function payout($user, $amount, $proc_id)
    {
        $runDate = date('Y-m-d', strtotime(DB::select('Select now() + INTERVAL 1 DAY as run_date')[0]->run_date));
        $fee = $amount * 0.01;
        if (env('APP_ENV') == "local") {

            $url = 'https://test.dragonpay.ph/api/payout/merchant/v1/' . env('MERCHANT_ID') . '/post';
        } elseif (env('APP_ENV') == "production") {

            $url = 'https://gw.dragonpay.ph/api/payout/merchant/' . env('MERCHANT_ID') . '/post';
        }

        $address = DB::table('addresses')
            ->where('user_id', '=',  $user['id'])
            ->first();

        $parameters = array(
            'TxnId' => $this->GenerateTnxId(),
            'FirstName' => $user['first_name'],
            'MiddleName' => $user['middle_name'],
            'LastName' => $user['last_name'],
            'Amount' => $amount,
            'Currency' => 'PHP',
            'Description' => 'Widthraw aex fund',
            'ProcId' => $proc_id,
            'ProcDetail' => $user['mobile_number'],
            'RunDate' => $runDate,
            'Email' => $user['email'],
            'MobileNo' => $user['mobile_number'],
            'BirthDate' => $user['birthdate'],
            'Nationality' => 'Filipino',
            'Address' =>
            [
                'Street1' => $address->street1,
                'Street2' => $address->street2,
                'Barangay' => $address->barangay,
                'City' => $address->city,
                'Province' => $address->province,
                'Country' => 'PH'
            ]
        );

        try {
            $client = new Client(['headers' => ['Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . env('DRAGONPAY_TOKEN')]]);
            $apiResult = $client->post($url, [\GuzzleHttp\RequestOptions::JSON => $parameters]);
            $responsStatusCode = $apiResult->getStatusCode();
            if ($responsStatusCode == 200) {
                $jsonResult = json_decode($apiResult->getBody(), true);

                if ($jsonResult['Code'] == 0) {

                    $getWalletId = DB::table('wallets')
                        ->where('user_id', '=',  $user['id'])
                        ->first()->id;

                    DB::table('wallet_transactions')->insert(
                        [
                            'transaction_type' => 'Payout',
                            'txn_id' => $parameters['TxnId'],
                            'refNo' => $jsonResult['Message'],
                            'wallet_id' => $getWalletId,
                            'transaction_description' => "Widthraw fund",
                            'payment_method' => 'Dragonpay',
                            'amount' => $amount,
                            'charge_amount' => $fee,
                            'created_at' => $this->GetCurrentTime(),
                            'status' => 'P'
                        ]
                    );
                    return $this->success("Successfully sent request", ["email" => $user['email'], 'amount' => $amount + $fee]);
                }
            } else {
                return $this->error("Failed to send request");
            }
        } catch (RequestException  $e) {
            if ($e->hasResponse()) {
                return $this->error(Psr7\str($e->getResponse()));
            }
        }
    }
    //
    public function PostBack(Request $request)
    {
        $getTransaction = DB::table('wallet_transactions')
            ->where('txn_id', '=', $request->merchantTxnId)
            ->first();

        if ($getTransaction) {
            $wallet = DB::table('wallets')
                ->where('id', '=', $getTransaction->wallet_id)->first();

            if ($wallet) {
                //pay-out
                if ($getTransaction->transaction_type == 'Payout' && $getTransaction->status == 'P') {
                    $totalDeduction = $getTransaction->amount + $getTransaction->charge_amount;

                    $amount = $wallet->available_balance - $totalDeduction;
                    //update wallet balance
                    $this->walletInterface->updateWalletBalance($amount, $wallet->id);
                    //Update wallet transaction status
                    $this->walletTransactionInterface->updateWalletTransactionStatus($request->status, $this->GetCurrentTime(), $getTransaction->id);
               }
                //pay-in
                if ($getTransaction->transaction_type == 'Fund'  && $getTransaction->status == 'P') {
                    $amount = $wallet->available_balance + $getTransaction->amount;
                    //update wallet balance
                    $this->walletInterface->updateWalletBalance($amount, $wallet->id);
                    //Update wallet transaction status
                    $this->walletTransactionInterface->updateWalletTransactionStatus($request->status, $this->GetCurrentTime(), $getTransaction->id);
                }
                //Other
                else {
                    $this->walletTransactionInterface->updateWalletTransactionStatus($request->status, $this->GetCurrentTime(), $getTransaction->id);
                }
            }
        }
        return "result=Ok";
    }
}
