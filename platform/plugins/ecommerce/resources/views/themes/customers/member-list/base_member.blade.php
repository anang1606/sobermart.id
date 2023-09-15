<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    @if (theme_option('favicon'))
        <link rel="shortcut icon" href="{{ RvMedia::getImageUrl(theme_option('favicon')) }}">
    @endif

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ page_title()->getTitle(false) }}</title>

    @yield('header', view(MarketplaceHelper::viewPath('dashboard.layouts.header')))

    <script>
        window.siteUrl = "{{ route('public.index') }}";
    </script>

    <!-- Put translation key to translate in VueJS -->
    <script type="text/javascript">
        'use strict';
        window.trans = Object.assign(window.trans || {}, JSON.parse('{!! addslashes(json_encode(trans('plugins/marketplace::marketplace'))) !!}'));

        var BotbleVariables = BotbleVariables || {};
        BotbleVariables.languages = {
            tables: {!! json_encode(trans('core/base::tables'), JSON_HEX_APOS) !!},
            notices_msg: {!! json_encode(trans('core/base::notices'), JSON_HEX_APOS) !!},
            pagination: {!! json_encode(trans('pagination'), JSON_HEX_APOS) !!},
            system: {
                character_remain: '{{ trans('core/base::forms.character_remain') }}'
            }
        };
    </script>

    @stack('header')
</head>
<body>
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif

    @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp
    <div class="row">
        <div class="col-md-9">
            @if ($showFields && $form->hasMainFields())
                <div class="main-form">
                    <div class="{{ $form->getWrapperClass() }}">
                        @foreach ($fields as $key => $field)
                            @if ($field->getName() == $form->getBreakFieldPoint())
                                @break
                            @else
                                @unset($fields[$key])
                            @endif
                            @if (!in_array($field->getName(), $exclude))
                                {!! $field->render() !!}
                                @if ($field->getName() == 'name' && defined('BASE_FILTER_SLUG_AREA'))
                                    {!! apply_filters(BASE_FILTER_SLUG_AREA, null, $form->getModel()) !!}
                                @endif
                            @endif
                        @endforeach
                        <div class="clearfix"></div>
                    </div>
                </div>
            @endif

            @foreach ($form->getMetaBoxes() as $key => $metaBox)
                {!! $form->getMetaBox($key) !!}
            @endforeach

            @php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp
        </div>
        <div class="col-md-3 right-sidebar">
            {!! $form->getActionButtons() !!}
            @php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp

            @foreach ($fields as $field)

                @if (!in_array($field->getName(), $exclude))
                    @if ($field->getType() == 'hidden')
                        {!! $field->render() !!}
                    @else
                        @if ($field->getOption('label') <> 'Categories')

                            <div class="widget meta-boxes">
                                <div class="widget-title">
                                    <h4>{!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}</h4>
                                </div>
                                <div class="widget-body">
                                    {!! $field->render([], in_array($field->getType(), ['radio', 'checkbox'])) !!}
                                </div>
                            </div>
                        @endif

                    @endif
                @endif
            @endforeach

            @php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
        </div>
    </div>

    @if ($showEnd)
        {!! Form::close() !!}
    @endif
    {!! Assets::renderFooter() !!}
</body>
</html>
