@extends('core/base::layouts.base')

@section ('page')
    @include('core/base::layouts.partials.svg-icon')
    <div id="main">
        @yield('content')
    </div>
@stop

@section('javascript')
    @include('core/media::partials.media')
@endsection

@push('footer')
    @routes
@endpush
