<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\VoucherAccountInterface;

class VoucherAccountController extends Controller
{
    protected $voucherAccountInterface;
    public function __construct(VoucherAccountInterface $voucherAccountInterface)
    {
        $this->voucherAccountInterface = $voucherAccountInterface;
    }

    public function pay(Request $request)
    {
       return $this->voucherAccountInterface->pay($request->payor_shop_id, $request->payee_shop_id, $request->amount);
    }
}
