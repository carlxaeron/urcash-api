<?php

namespace App\Repositories;

use App\PaymentMethod;
use App\Interfaces\PaymentMethodInterface;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodRepository implements PaymentMethodInterface
{
    // Use ResponseAPI Trait in this repository
    use ResponseAPI;

    public function getAllPaymentMethods()
    {
        try {
            $paymentMethod = PaymentMethod::all()->where('status', true);

            return $this->success("Payment methods", $paymentMethod);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function getPaymentMethodById($id)
    {
        try {
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) return $this->error("Payment method not found", 404);

            return $this->success("Payment method detail", $paymentMethod);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function enableDisablePaymentMethod($status, $id) // NO ROUTE
    {
        try {
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) return $this->error("Payment method not found", 404);

            $paymentMethod->status = $status;
            $paymentMethod->save();
            $flagStatus = 'disabled';

            if ($status == true) $flagStatus = 'enabled';

            return $this->success("Payment method successfully " . $flagStatus, $paymentMethod);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function updatePaymentMethod(Request $request, $id)
    {
        try {
            $paymentMethod = PaymentMethod::find($id);

            if (!$paymentMethod) return $this->error("Payment method not found", 404);

            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
            ];
            $rules = [
                'title' => 'nullable|unique:announcements,title',
                'description' => 'nullable',
                'account_name' => 'nullable|required_with:account_number',
                'account_number' => 'nullable|required_with:account_name'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            if ($request->has('title')) {
                $paymentMethod->title = $request->title;
            }
            if ($request->has('description')) {
                $paymentMethod->description = $request->description;
            }
            if ($request->has('account_name')) {
                $paymentMethod->account_name = $request->account_name;
            }
            if ($request->has('account_number')) {
                $paymentMethod->account_number = $request->account_number;
            }
            $paymentMethod->save();

            return $this->success("Payment method updated", $paymentMethod);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createPaymentMethod(Request $request)
    {
        try {
            $inputs = [
                'title' => $request->title,
                'description' => $request->description,
                'account_name' => $request->account_name,
                'account_number' => $request->account_number
            ];
            $rules = [
                'title' => 'required',
                'description' => 'required',
                'account_name' => 'required',
                'account_number' => 'required'
            ];
            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) return $this->error($validation->errors()->all());

            $inputs['status'] = 1;
            $paymentMethod = PaymentMethod::create($inputs);

            return $this->success("Payment method created", $paymentMethod);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
