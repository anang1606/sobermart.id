<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Support\Http\Requests\Request;
class MemberWithdrawalRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:' . (auth('customer')->user()->commissions),
            'description' => 'nullable|max:400',
            'bank_info' => 'required|array',
            'bank_info.name' => 'required',
            'bank_info.number' => 'required',
            'bank_info.full_name' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'amount.max' => __('The balance is not enough for withdrawal'),
            'bank_info.required' => __('The bank information is required.'),
            'bank_info.array' => __('The bank information must be an array.'),
            'bank_info.name.required' => __('The bank name is required.'),
            'bank_info.number.required' => __('The bank number is required.'),
            'bank_info.full_name.required' => __('The full name is required.'),
        ];
    }
}
