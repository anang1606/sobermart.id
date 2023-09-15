<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BroadcastRequest extends Request
{
    public function rules(): array
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
        ];
    }
}
