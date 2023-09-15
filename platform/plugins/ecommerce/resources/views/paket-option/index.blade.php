@php
    $value = count($values) ? ($values[0] ?? []) : [];
    $isDefaultLanguage = ! defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') ||
        ! request()->input('ref_lang') ||
        request()->input('ref_lang') == Language::getDefaultLocaleCode();
@endphp

<div class="col-md-12 option-setting-tab">
    <table class="table table-bordered setting-option">
        <thead>
            <tr>
                @if ($isDefaultLanguage)
                    <th scope="col">#</th>
                @endif
                <th scope="col">{{ trans('plugins/ecommerce::product-option.label') }}</th>
                <th scope="col">
                    Description
                </th>
                <th width="120px">
                    Target Member
                </th>
                @if ($isDefaultLanguage)
                    <th scope="col"></th>
                @endif
            </tr>
        </thead>
        <tbody class="option-sortable">
            @if ($values->count())
                @foreach ($values as $key => $value)
                    <tr class="option-row ui-state-default" data-index="{{ $key }}">
                        <input type="hidden" name="options[{{ $key }}][id]" value="{{ $value->id }}">
                        @if ($isDefaultLanguage)
                            <td class="text-center">
                                <i class="fa fa-sort"></i>
                            </td>
                        @endif
                        <td>
                            <input type="text" class="form-control option-label" name="options[{{ $key }}][option_value]" value="{{ $value->label }}"
                                placeholder=""/>
                        </td>
                        <td>
                            <input type="text" class="form-control option-description" name="options[{{ $key }}][option_description]" value="{{ $value->description }}"
                                placeholder=""/>
                        </td>
                        <td>
                            <input type="text" class="form-control option-target" name="options[{{ $key }}][option_target]" value="{{ $value->target }}"
                                placeholder=""/>
                        </td>
                        @if ($isDefaultLanguage)
                            <td style="width: 50px">
                                <button class="btn btn-default remove-row" data-index="0"><i class="fa fa-trash"></i></button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                @if ($isDefaultLanguage)
                    <td class="text-center">
                        <i class="fa fa-sort"></i>
                    </td>
                @endif
                <td>
                    <input type="text" class="form-control option-label" required name="options[0][option_value]" value=""
                           placeholder=""/>
                </td>
                <td>
                    <input type="text" class="form-control option-description" required name="options[0][option_description]" value=""
                           placeholder=""/>
                </td>
                <td>
                    <input type="number" class="form-control option-target" required name="options[0][option_target]" value=""
                           placeholder=""/>
                </td>
                @if ($isDefaultLanguage)
                    <td style="width: 50px">
                        <button class="btn btn-default remove-row" data-index="0"><i class="fa fa-trash"></i></button>
                    </td>
                @endif
            @endif
        </tbody>
    </table>
    <button type="button" class="btn btn-info mt-3 add-new-row" id="add-new-row">{{ trans('plugins/ecommerce::product-option.add_new_row') }}</button>
</div>
