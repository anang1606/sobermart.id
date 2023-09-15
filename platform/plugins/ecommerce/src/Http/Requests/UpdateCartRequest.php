<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdateCartRequest extends Request
{
    public function rules(): array
    {
        $rules = [];
        // foreach (array_keys($this->input('items', [])) as $rowId) {
        // }
        $rules = [
            'rowId' => 'required|integer',
            'qty' => 'required|integer',
        ];

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        // foreach (array_keys($this->input('items', [])) as $rowId) {
        // }
        $messages = [
            'rowId.required' => __('Cart item ID is required!'),
            'qty.required' => __('Quantity is required!'),
            'qty.integer' => __('Quantity must be a number!'),
        ];

        return $messages;
    }
}