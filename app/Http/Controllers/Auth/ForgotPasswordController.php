<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails; // Methods already being overridden below; use SendsPasswordResetEmails; if using
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
//    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm() {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request) {
        $request->validate(['mobile_number' => 'required|starts_with:63|numeric|digits:12|exists:users,mobile_number']);
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );
        $user = User::where('mobile_number', '=', $request->mobile_number)->first();
        $request->email = $user->email;

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the needed authentication credentials from the request
     */
    protected function credentials(Request $request) {
        return $request->only('mobile_number');
    }

    /**
     * Get the response for a successful password reset link
     */
    protected function sendResetLinkResponse(Request $request, $response) {
        return back()->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset link
     */
    protected function sendResetLinkFailedResponse(Request $request, $response) {
        return back()->withInput($request->only('mobile_number'))
            ->withErrors(['mobile_number' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset
     */
    public function broker() {
        return Password::broker();
    }
}
