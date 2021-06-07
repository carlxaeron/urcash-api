<?php

namespace App\Interfaces;

interface VoucherAccountTransactionInterface
{
    /**
     * Get Voucher Account Transaction by shop ID
     *
     * @access  public
     */
    public function  getVoucherAccountTransactionByVoucherAccountId($shop_id);

     /**
     * Create Voucher Account Transaction
     *
     * @access  public
     */
    public function createVoucherAccountTransaction($request,  $trans_id);

    /**
     * Update Voucher Account Transaction
     *
     * @access  public
     */
    public function updateVoucherAccountTransactionStatus($shop_id, $amount);


    /**
     * Get Voucher Account Transaction by ID
     *
     * @access  public
     */
    public function getVoucherAccountTransactionById($id);

     /**
     * Get Voucher Account Transactions by ID
     *
     * @access  public
     */
    public function getVoucherAccountTransactionsByAccountId($id);
     /**
     * Get PAY Transactions by ID
     *
     * @access  public
     */
    public function getPayTransactionsByAccountId($id);
}
