<?php

namespace Botble\Accounting\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ExpensesRequest extends Request
{
    public function rules(): array
    {
        return [
            'coadebit' => 'required',
            'coakredit' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'kode_reff' => 'required',
        ];
    }
}
