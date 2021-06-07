<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface WalletInterface
{
     /**
     * Get user Wallet
     *
     * @access  public
     */
    public function getWallet(Request $request);

     /**
     * Get user Wallet by user_id
     *
     * @access  public
     */
    public function getWalletByUserId($user_id);

     /**
     * Update user Wallet balance by user_id
     *
     * @access  public
     */
    public function updateWalletBalance($amount, $wallet_id);

     /**
     * Get user Wallet by qr_code
     *
     * @access  public
     */
    public function getWalletByqrcode($qr_code);

    /**
     * Get Wallet QR code by mobile number
     *
     * @access  public
     */
    public function getQrCodeByMobileNumber(Request $request);
}
