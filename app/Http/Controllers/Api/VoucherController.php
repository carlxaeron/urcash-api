<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\VoucherInterface;

class VoucherController extends Controller
{
    protected $voucherInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(VoucherInterface $voucherInterface)
    {
        $this->voucherInterface = $voucherInterface;
    }

    /**
     * Get all voucher.
     */
    public function index()
    {
        return $this->voucherInterface->getAllVouchers();
    }

    /**
     * Get voucher by ID.
     */
    public function show($id)
    {
        return $this->voucherInterface->getVoucherById($id);
    }

    /**
     * Update a voucher.
     */
    public function update(Request $request, $id)
    {
        return $this->voucherInterface->updateVoucher($request, $id);
    }

    /**
     * Delete a voucher.
     */
    public function delete($id)
    {
        return $this->voucherInterface->deleteVoucher($id);
    }

    /**
     * Create a voucher.
     */
    public function create(Request $request)
    {
        return $this->voucherInterface->createVoucher($request);
    }
}
