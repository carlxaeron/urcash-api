<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentInterface;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $interface;
    public function __construct(PaymentInterface $interface)
    {
        $this->interface = $interface;
    }

    public function generatePaymentGateway()
    {
        return $this->interface->generatePaymentGateway();
    }
    
    public function paymentRequest(Request $request)
    {
        return $this->interface->paymentRequest($request);
    }

    public function paymentCallback(Request $request, $id = false)
    {
        return $this->interface->paymentCallback($request, $id);
    }

    public function paymentChecker(Request $request)
    {
        return $this->interface->paymentChecker($request);
    }
}
