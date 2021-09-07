<?php

namespace App\Interfaces;

use App\User;
use Illuminate\Http\Request;

interface UserInterface
{
    /**
     * Login user
     *
     * @method  Post api/login
     * @access  public
     */
    public function login(Request $request);

    /**
     * Login user
     *
     * @method  Post api/v1/login
     * @access  public
     */
    public function loginV1(Request $request);

    /**
     * Login user with RED account
     *
     * @method  Post api/v1/login-with-red
     * @access  public
     */
    public function loginV1WithRed(Request $request);

    public function linkV1WithRed(Request $request);

    public function getInfoWithRed();

    public function getLinkedWithRed();

    public function getListPackagesWithRed();

    public function verifyMerchant(Request $request);

    /**
     * Get all users
     *
     * @method  GET api/users
     * @access  public
     */
    public function getAllUsers();

    public function getAllUsersV1();

    /**
     * Get User By ID
     *
     * @param   integer     $id
     *
     * @method  GET api/users/{id}
     * @access  public
     */
    public function getUserById($id);

    /**
     * Create support ticket based on mobile_number, email and birthdate
     *
     * @method  POST api/request_review_account
     * @access  public
     */
    public function requestReviewUnlockAccount(Request $request);

    /**
     * Set MPIN of user
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function setMpin(Request $request);

    /**
     * Send reset password link via email by using the user's mobile_number
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function sendResetPasswordLinkEmail(Request $request);

    public function sendResetPasswordLinkEmailV1(Request $request);

    /**
     * Reset user password by providing new password
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function resetPasswordWithToken(Request $request);

    public function resetPasswordWithTokenV1(Request $request);

    /**
     * Create user
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @method  POST    api/register-user       For Create
     * @access  public
     */
    public function registerUser(Request $request);

    public function registerUserV1(Request $request);

    /**
     * Update user
     *
     * @param   integer $id
     * @param   \Illuminate\Http\Request    $request
     *
     * @method  POST    api/update-user       For Create
     * @access  public
     */
    public function updateUser(Request $request, $id);

    /**
     * Delete user
     *
     * @param   integer     $id
     *
     * @method  DELETE  api/users/{id}
     * @access  public
     */
    public function deleteUser($id);

    /**
     * Logout user
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @method  POST  api/logout
     * @access  public
     */
    public function logout();

    /**
     * Get user by mobile number
     *
     * @param   $mobile_number
     *
     * @access  public
     */
    public function getUserByMobileNumber($mobile_number);

    /**
     * Get user details
     *
     * @access  public
     */
    public function getUserInfo();

    public function updateUserAddress(Request $request);

    public function updateUserAvatar(Request $request);

    /**
     * Get user purchases
     *
     * @access  public
     */
    public function getUserPurchases();

    /**
     * Update OTP and send
     *
     * @param   $mobile_number
     * @method  POST  api/logout
     * @access  public
     */
    public function updateOtpAndSend($mobile_number);

    /**
     * validate OTP
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function validateOtp(Request $request);
    
    public function validateOtpV1(Request $request);

    /**
     * reset Password
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function resetPassword(Request $request);

    /**
     * Login using mpin
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function mpinLogin(Request $request);

    /**
     * Update user profile picture
     *
     * @param   \Illuminate\Http\Request    $request
     * @param   User   $id
     *
     * @access  public
     */
    public function updateProfilePicture(Request $request, $id);

    /**
     * Register Admin
     *
     * @param   \Illuminate\Http\Request    $request
     *
     * @access  public
     */
    public function registerAdmin(Request $request);

    /**
     * Update Admin
     *
     * @param   \Illuminate\Http\Request    $request
     * @param   User   $id
     *
     * @access  public
     */
    public function updateAdmin(Request $request, $id);

     /**
     * get Admins
     *
     * @access  public
     */
    public function getAdmins();

    public function addToCart(Request $request);

    public function getCart(Request $request);

    public function deleteCheckedCart();
}
