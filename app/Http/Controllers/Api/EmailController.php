<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\VerificationCode;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerifiedSuccessfully;
use App\Mail\ResendVerificationCode;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailController extends Controller
{
    // Use ResponseAPI trait in this repository
    use ResponseAPI;

    public function verifyEmail (Request $request) {
        try {
            $inputs = ['code' => $request->code];
            $rules = ['code' => 'required|numeric|digits:6'];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $find_verification_code = VerificationCode::where('code', '=', $request->code)->first();

            if (!$find_verification_code) { // If code is incorrect
                return $this->error("Verification code is incorrect.");
            } elseif ($find_verification_code->is_verified == True) { // If verification code is already verified
                return $this->error("Verification code was already used.");
            }

            $find_user = User::find($find_verification_code->user_id);

            // If code and user exists
            if ($find_verification_code and $find_user) {
                // Set is_verified field to true on VerificationCodes table
                $find_verification_code->is_verified = True;
                $find_verification_code->save();

                // Set email_verified_at field to current time on Users table
                $find_user->email_verified_at = Carbon::now()->toDateTimeString(); // format: YYYY-MM-DD HH:MM:SS
                $find_user->save();

                Mail::to($find_user->email)->send(new EmailVerifiedSuccessfully($find_user));

                return $this->success("Your email is now verified.", $find_user);
            }
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function resendVerification (Request $request) {
        try {
            $messages = ['user_id.exists' => 'The :attribute is not found.']; // Override validation messages
            $inputs = ['user_id' => $request->user_id];
            $rules = ['user_id' => 'required|exists:users,id'];

            $validation = Validator::make($inputs, $rules, $messages);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $user = User::find($request->user_id);
            $verification_code = VerificationCode::where('user_id', '=', $user->id)->first();

            // Check if verification code is already verified or not
            if ($verification_code->is_verified == True) {
                return $this->error("Verification code was already used.");
            }

            // Send email
            Mail::to($user->email)->send(new ResendVerificationCode($user, $verification_code->code));

            return $this->success("Verification code was resent to $user->email", $user);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
