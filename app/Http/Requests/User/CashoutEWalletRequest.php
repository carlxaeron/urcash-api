<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CashoutEWalletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shop_id' => 'required',
            'e_wallet_id' => 'required',
            'account_name' => 'required|string',
            'account_number' => 'required|string',
            'amount' => 'required'
        ];
    }
}
