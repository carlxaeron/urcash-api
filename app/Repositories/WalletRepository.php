<?php

namespace App\Repositories;

use App\Interfaces\WalletInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletRepository implements WalletInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getWallet(Request $request)
    {
        try {
            $getUserId = DB::table('users')
                ->where('mobile_number', '=',  $request->mobile_number)
                ->first()->id;

            $wallet = DB::table('wallets')
                ->where('user_id', '=',  $getUserId)
                ->first();

            $balance = array(
                'balance' => $wallet->available_balance
            );

            return $this->success("Available balance", $balance);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getWalletByUserId($user_id)
    {
        try {
            $wallet = DB::table('wallets')
                ->where('user_id', '=',  $user_id)
                ->first();

            return $this->success("User wallet", $wallet);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updateWalletBalance($amount, $wallet_id)
    {
        try {
            DB::update('UPDATE wallets SET available_balance = ? WHERE id = ?', [$amount,  $wallet_id]);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getWalletByqrcode($qr_code)
    {
        try {
            $wallet = DB::table('wallets')
                ->where('qr_code', '=',  $qr_code)
                ->first();

            return $this->success("User wallet", $wallet);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getQrCodeByMobileNumber(Request $request)
    {
        try {

            $getUserId = DB::table('users')
                ->where('mobile_number', '=',  $request->mobile_number)
                ->first()->id;

            $wallet = DB::table('wallets')
                ->where('user_id', '=', $getUserId)
                ->first();
            if(!$wallet){

                return $this->error("User wallet not found", 404);
            }

            return $this->success("User wallet", array('qr_code' => $wallet->qr_code));
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
