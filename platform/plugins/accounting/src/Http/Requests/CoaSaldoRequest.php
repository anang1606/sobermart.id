<?php

namespace Botble\Accounting\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CoaSaldoRequest extends Request
{
    public function rules(): array
    {
        return [
            'kredit' => 'required',
            'debit' => 'required',
        ];
    }
}
