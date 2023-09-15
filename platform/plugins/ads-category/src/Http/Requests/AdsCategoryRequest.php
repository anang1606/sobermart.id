<?php

namespace Botble\AdsCategory\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use AdsManager;

class AdsCategoryRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'categories_id' => 'required',
            'url' => 'required',
            'expired_at' => 'required',
            'key' => 'required|max:120|unique:ads,key,' . $this->route('ads'),
            'order' => 'required|integer|min:0|max:127',
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
