<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface VoucherOrderInterface
{

     /**
     * Get user Voucher Orders
     *
     * @access  public
     */
    public function getVoucherOrders();

     /**
     * Get user Voucher Order By ID
     *
     * @access  public
     */
    public function getVoucherOrderById($user_id);

     /**
     * Create Voucher Order
     *
     * @access  public
     */
    public function createVoucherOrder($request);

     /**
     * Upload Receipt
     *
     * @access  public
     */
    public function uploadProofOfPayment($request);

    /**
     * Verify Voucher Order
     *
     * @access  public
     */
    public function verifyVoucherOrder($id);

    /**
     * Reject Voucher Order
     *
     * @access  public
     */
    public function rejectVoucherOrder($id);

    /**
     * Voucher Orders to verify
     *
     * @access  public
     */
    public function toVerify();

    /**
     * Voucher Order history
     *
     * @access  public
     */
    public function history();

    /**
     * Voucher Order history
     *
     * @access  public
     */
    public function countPendingRequest();

    /**
     * Voucher Order sum unpaid
     *
     * @access  public
     */
    public function unpaid();

    /**
     * Voucher Order fees collected
     *
     * @access  public
     */
    public function feesCollected();

    /**
     * Voucher Order sold
     *
     * @access  public
     */
    public function voucherSold();

    /**
     * Voucher Orders with no proof of payment
     *
     * @access  public
     */
    public function noProofOfPayment($id);

    /**
     * Voucher Order cancel
     *
     * @access  public
     */
    public function cancelOrder($id);
}
