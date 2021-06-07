<?php

namespace App\Http\Services\NexmoService;

/**
 * Class RandomStringGenerator
 * @package NexmoService
 *
 */
class SendService
{
    public function sendCode($mobileNumber){
        $basic  = new \Nexmo\Client\Credentials\Basic( env('NEXMO_API_KEY'),  env('NEXMO_API_SECRET'));
        $client = new \Nexmo\Client($basic);

        $digits = 4;
        //generate otp
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);

        $client->message()->send([
            'to' => $mobileNumber,
            'from' => 'G2GBox',
            'text' => $otp.' This is your G2GBox OTP valid for the next 5 minutes.'
        ]);

        return $otp;
    }

}
