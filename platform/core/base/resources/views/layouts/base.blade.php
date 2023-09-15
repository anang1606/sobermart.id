<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>{!! BaseHelper::clean(page_title()->getTitle()) !!}</title>

    <meta name="robots" content="noindex,follow" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (setting('admin_logo') || config('core.base.general.logo'))
        <meta property="og:image"
            content="{{ setting('admin_logo') ? RvMedia::getImageUrl(setting('admin_logo')) : url(config('core.base.general.logo')) }}">
    @endif
    <meta name="description"
        content="{{ strip_tags(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) }}">
    <meta property="og:description"
        content="{{ strip_tags(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) }}">

    @if (setting('admin_favicon') || config('core.base.general.favicon'))
        <link rel="icon shortcut"
            href="{{ setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon'), 'thumb') : url(config('core.base.general.favicon')) }}">
    @endif

    <link rel="preconnect" href="{{ BaseHelper::getGoogleFontsURL() }}">

    {!! Assets::renderHeader(['core']) !!}

    <script>
        window.siteUrl = "{{ url('') }}";
        window.siteEditorLocale = "{{ apply_filters('cms_site_editor_locale', App::getLocale()) }}";
    </script>

    @if (BaseHelper::adminLanguageDirection() == 'rtl')
        <link rel="stylesheet" href="{{ asset('vendor/core/core/base/css/rtl.css') }}">
    @endif

    @yield('head')

    @stack('header')
    @php
        $currentRouteName = Route::currentRouteName();
    @endphp
    @if ($currentRouteName === 'marketplace.support-message.index' || $currentRouteName === 'marketplace.support-message.details')
    <link rel="stylesheet" href="{{ asset('themes/farmart/css/support-message.css') }}" />
    @endif
</head>

<body @if (BaseHelper::adminLanguageDirection() == 'rtl') dir="rtl" @endif
    class="@yield('body-class', 'page-sidebar-closed-hide-logo page-content-white page-container-bg-solid') {{ session()->get('sidebar-menu-toggle') ? 'page-sidebar-closed' : '' }}"
    style="@yield('body-style')">

    {!! apply_filters(BASE_FILTER_HEADER_LAYOUT_TEMPLATE, null) !!}

    <div id="app">
        @yield('page')
    </div>

    @include('core/base::elements.common')

    {!! Assets::renderFooter() !!}

    @yield('javascript')

    <div id="stack-footer">
        @stack('footer')
    </div>

    {!! apply_filters(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, null) !!}
</body>

</html>
