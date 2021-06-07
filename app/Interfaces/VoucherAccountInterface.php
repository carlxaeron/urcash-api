<?php

namespace App\Interfaces;

interface VoucherAccountInterface
{
    /**
     * Get Vaoucher Account by shop ID
     *
     * @access  public
     */
    public function  getVoucherAccountByShopId($shop_id);

    /**
     * Pay Vaoucher Account
     *
     * @access  public
     */
    public function pay($payor_shop_id,$payee_shop_id, $amount);
}
