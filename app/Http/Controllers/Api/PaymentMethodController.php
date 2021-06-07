<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\PaymentMethodInterface;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    protected $paymentMethodInterface;

    /**
     * Create a new constructor for this controller
     */
    public function __construct(PaymentMethodInterface $paymentMethodInterface)
    {
        $this->paymentMethodInterface = $paymentMethodInterface;
    }
    /**
     * Get all payment methods
     */
    public function index()
    {
        return $this->paymentMethodInterface->getAllPaymentMethods();
    }

    /**
     * Get payment method by ID
     */
    public function show($id) {
        return $this->paymentMethodInterface->getPaymentMethodById($id);
    }

    /**
     * Enable or disable a payment method
     */
    public function enableDisable(Request $request, $id) {
        return $this->paymentMethodInterface->enableDisablePaymentMethod($request->status, $id);
    }

    /**
     * Update a payment method.
     */
    public function update(Request $request, $id) {
        return $this->paymentMethodInterface->updatePaymentMethod($request, $id);
    }

    /**
     * Create payment method
     */
    public function create(Request $request) {
        return $this->paymentMethodInterface->createPaymentMethod($request);
    }
}
