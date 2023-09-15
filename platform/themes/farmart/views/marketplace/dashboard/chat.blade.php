@extends(MarketplaceHelper::viewPath('dashboard.layouts.master'))

@section('content')
    <div style="width: 100%;height:100vh">
        <div
            style="overflow: hidden;width: 100%;height: 100%;display: flex;background:white;">
            <iframe id="frame-chat"
                src="https://chat.sobermart.id/?nref=sm&key={{ auth('customer')->user()->id }}&type=vendor&logo={{ RvMedia::getImageUrl(theme_option('logo')) }}&store=none"
                width="100%" height="100%"></iframe>
        </div>
    </div>
    <script>
        const iframeChat = document.getElementById('frame-chat')
        function getWindowWidth() {
            var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
            return width;
        }

        setTimeout(() => {
            if (getWindowWidth() >= 1450) {
                iframeChat.src = iframeChat.src.replace('nref=sm','nref=md')
            }
        },250)
    </script>
@stop
