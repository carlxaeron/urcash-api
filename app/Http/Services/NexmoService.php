<?php

namespace App\Http\Services\NexmoService;

use App\User;
use Illuminate\Support\Facades\Mail;

/**
 * Class RandomStringGenerator
 * @package NexmoService
 *
 */
class SendService
{
    public function sendCode($mobileNumber){
        $digits = 4;
        //generate otp
        $otp = rand(pow(10, $digits-1), pow(10, $digits)-1);

        $MESSAGE = $otp.' This is your G2GBox OTP valid for the next 5 minutes.';

        if(config('app.env') == 'local') {
            $user = User::where('mobile_number', $mobileNumber)->first();
            Mail::raw($MESSAGE, function ($message) use($user) {
                $message->to($user->email)->subject('OTP');
            });
        }
        else {
            $basic  = new \Nexmo\Client\Credentials\Basic( env('NEXMO_API_KEY'),  env('NEXMO_API_SECRET'));
            $client = new \Nexmo\Client($basic);
    
            $client->message()->send([
                'to' => $mobileNumber,
                'from' => 'G2GBox',
                'text' => $MESSAGE
            ]);
        }
        return $otp;
    }

}
