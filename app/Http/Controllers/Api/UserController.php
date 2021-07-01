<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\UserInterface;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(UserInterface $userInterface)
    {
        $this->userInterface = $userInterface;
    }

    /**
     * Display a listing of user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->userInterface->getAllUsers();
    }

    public function indexV1()
    {
        return $this->userInterface->getAllUsersV1();
    }

    public function verifyMerchant(Request $request){
        return $this->userInterface->verifyMerchant($request);
    }

    /**
     * Login user
     *
     * @param  \App\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return $this->userInterface->login($request);
    }

    /**
     * Login user v1
     *
     * @param  \App\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function loginV1(Request $request)
    {
        return $this->userInterface->loginV1($request);
    }
    public function loginV1WithRed(Request $request)
    {
        return $this->userInterface->loginV1WithRed($request);
    }

    /**
     * Create support ticket based on mobile_number, email and birthdate
     */
    public function requestReviewUnlockAccount(Request $request)
    {
        return $this->userInterface->requestReviewUnlockAccount($request);
    }

    /**
     * Set MPIN of user
     */
    public function setMpin(Request $request)
    {
        return $this->userInterface->setMpin($request);
    }

    /**
     * Send reset password link via email by using the user's mobile_number
     */
    public function sendResetPasswordLinkEmail(Request $request)
    {
        return $this->userInterface->sendResetPasswordLinkEmail($request);
    }

    /**
     * Reset user password by providing new password
     */
    public function resetPasswordWithToken(Request $request)
    {
        return $this->userInterface->resetPasswordWithToken($request);
    }

    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        return $this->userInterface->registerUser($request);
    }

    public function registerV1(Request $request)
    {
        return $this->userInterface->registerUserV1($request);
    }

    /**
     * Update user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->userInterface->updateUser($request, $id);
    }

    /**
     * Logout user
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        return $this->userInterface->logout();
    }

    /**
     * Resend user OTP
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function resendOtp(Request $request)
    {
        return $this->userInterface->updateOtpAndSend($request->mobile_number);
    }

    /**
     * Validate OTP
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function validateOtp(Request $request)
    {
        return $this->userInterface->validateOtp($request);
    }

    public function validateOtpV1(Request $request)
    {
        return $this->userInterface->validateOtpV1($request);
    }

    /**
     * Reset password
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        return $this->userInterface->resetPassword($request);
    }

    /**
     * Get user by mobile number
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getUserByMobileNumber(Request $request)
    {
        return $this->userInterface->getUserByMobileNumber($request->mobile_number);
    }
    
    /**
     * Get user details
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getUserInfo()
    {
        return $this->userInterface->getUserInfo();
    }

    public function updateUserAddress(Request $request)
    {
        return $this->userInterface->updateUserAddress($request);
    }

    /**
     * Get user purchases
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function getUserPurchases()
    {
        return $this->userInterface->getUserPurchases();
    }

    /**
     * Login using mpin
     *
     * @param  \Illuminate\Http\Request ; $request
     * @return \Illuminate\Http\Response
     */
    public function mpinLogin(Request $request)
    {
        return $this->userInterface->mpinLogin($request);
    }

    /**
     * User profile picture
     *
     * @param  \Illuminate\Http\Request ; $request
     * @param  User $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePicture(Request $request, $id)
    {
        return $this->userInterface->updateProfilePicture($request, $id);
    }

    /**
     * Register Admin
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function registerAdmin(Request $request)
    {
        return $this->userInterface->registerAdmin($request);
    }

     /**
     * Update Admin
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function updateAdmin(Request $request, $id)
    {
        return $this->userInterface->updateAdmin($request, $id);
    }

     /**
     * Get Admins
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function getAdmins()
    {
        return $this->userInterface->getAdmins();
    }
}
