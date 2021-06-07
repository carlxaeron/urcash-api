<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords; // Methods already being overridden below; use ResetPasswords; if using
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
//    use ResetsPasswords;

    protected $redirectTo = '/'; // Where to redirect users after resetting their password.

    /**
     * Display the password reset view for the given token. If no token is present, display the link request form.
     */
    public function showResetForm(Request $request, $token = null) {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'mobile_number' => $request->mobile_number]
        );
    }

    /**
     * Validate and reset user password
     */
    public function reset(Request $request) {
        $this->validate($request, [
            'mobile_number' => 'required|starts_with:63|numeric|digits:12|exists:users,mobile_number',
            'password' => 'required|integer|confirmed',
        ]);

        // Get the password reset credentials from the request. Here we will attempt to reset the user's password.
        // If it is successful we will update the password on an actual user model and persist it to the database.
        $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );
        return view('welcome');
    }

    /**
     * Get the password reset credentials from the request.
     */
    protected function credentials(Request $request) {
        return $request->only('mobile_number', 'password', 'password_confirmation', 'token');
    }

    /**
     * Reset the given user's password.
     */
    protected function resetPassword($user, $password) {
        $user->password = Hash::make($password);
        $user->remember_token = Str::random(60);
        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Get the response for a successful password reset
     */
    protected function sendResetResponse(Request $request, $response) {
        return redirect($this->redirectPath())->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset
     */
    protected function sendResetFailedResponse(Request $request, $response) {
        return redirect()->back()
            ->withInput($request->only('mobile_number'))
            ->withErrors(['mobile_number' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset
     */
    public function broker() {
        return Password::broker();
    }
}
