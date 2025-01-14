<?php
namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentInterface {

    public function generatePaymentGateway();

    public function paymentRequest(Request $request);

    public function paymentCallback(Request $request, $id);

    public function paymentChecker(Request $request);
}