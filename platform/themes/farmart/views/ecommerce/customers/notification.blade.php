@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="ggNFa+">
        {{-- <div class="Zl35pt"><button class="fIARET">Tandai sebagai sudah dibaca</button></div> --}}
        <div class="">
            <div class="stardust-dropdown l31zRX">
                <div class="stardust-dropdown__item-header">
                    @if (count($paginatedNotifications) > 0)
                        @foreach ($paginatedNotifications as $notification)
                            <a href="{{ route('customer.notification.read',$notification->id) }}" class="mAFvNF eivFaM @if ($notification->is_read === 0)
                                ZUkA8-
                            @endif">
                                <div class="ssWhg- _6Wd-Sz">
                                    <div class="yvbeD6 _9KTDdY">
                                        <div class="_9KTDdY vc8g9F"
                                            style="background-image: url('{{ RvMedia::url($notification->image) }}'); background-size: contain; background-repeat: no-repeat;">
                                        </div>
                                    </div>
                                </div>
                                <div class="VunZ9e qvZNVn">
                                    <h1 class="jS2Nf7">{{ $notification->title }}</h1>
                                    <div class="wBqQAl">
                                        {!! $notification->description !!}
                                    </div>
                                    <div class="PmKaN+">
                                        <p class="Y3aLQR">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                {{-- <div class="_0+L5GJ"><button class="_07Kc1J XEFU2N">Tampilkan Rincian</button></div> --}}
                            </a>
                        @endforeach
                    @endif
                </div>
                <div class="pagination mt-5">
                    {!! $paginatedNotifications->links() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
