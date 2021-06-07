<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface VoucherInterface
{
     /**
     * Get all Voucher
     *
     * @access  public
     */
    public function getAllVouchers();

    /**
     * Get Voucher by ID
     *
     * @access  public
     */
    public function getVoucherById($id);

     /**
     * Create Voucher
     *
     * @access  public
     */
    public function createVoucher(Request $request);

     /**
     * Update Voucher
     *
     * @access  public
     */
    public function updateVoucher(Request $request, $id);

     /**
     * delete Voucher
     *
     * @access  public
     */
    public function deleteVoucher($id);
}
