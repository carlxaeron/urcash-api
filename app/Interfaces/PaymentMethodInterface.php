<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface PaymentMethodInterface
{
    public function getAllPaymentMethods();

    public function getPaymentMethodById($id);

    public function enableDisablePaymentMethod($status, $id);

    public function updatePaymentMethod(Request $request, $id);

    public function createPaymentMethod(Request $request);
}
