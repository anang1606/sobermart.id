<?php

namespace Botble\Marketplace\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PaketMasterRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'nominal' => 'required',
            'description' => 'required',
        ];
    }
}
