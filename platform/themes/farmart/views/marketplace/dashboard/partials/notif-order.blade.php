@php
    use Botble\Ecommerce\Models\Order;
    $get_order = Order::where([['store_id', auth('customer')->user()->store->id], ['status', 'pending']])
        ->orderBy('created_at', 'DESC')
        ->limit(10)
        ->get();
@endphp

<div class="dropdown">
    <button style="background-color: transparent" class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        <i class="fas fa-shopping-cart"></i>
        @if (count($get_order) > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ count($get_order) }}
            </span>
        @endif
    </button>
    <ul class="dropdown-menu dropdown-menu-right vendor-dropdown"
        style="
        min-width: 160px;
        max-width: 275px;
        width: 275px;">
        <li>
        <li class="external">
            <h3>
                You have
                <span class="bold">
                    {{ count($get_order) }}
                </span> New Order(s)
            </h3>
            <a href="{{ route('marketplace.vendor.orders.index') }}">View all</a>
        </li>
        </li>
        <li>
            <ul class="dropdown-menu-list scroller">
                @foreach ($get_order as $order)
                    <li>
                        <a href="{{ route('marketplace.vendor.orders.edit',$order->id) }}">
                            <span class="photo" >
                                <img 
                                    src="{{ $order->user->id ? $order->user->avatar_url : $order->address->avatar_url }}"
                                    class="rounded-circle" alt="{{ $order->address->name }}">
                            </span>
                            <span class="subject">
                                <span class="from"> {{ $order->address->name ?: $order->user->name }} </span><span
                                    class="time">{{ $order->created_at->toDateTimeString() }}
                                </span>
                            </span>
                            <span class="message"> {{ $order->address->phone ? $order->address->phone . ' - ' : null }}
                                {{ $order->address->email ?: $order->user->email }}
                            </span>
                        </a>
                    </li>
                    @if (count($get_order) > 10)
                        <li class="text-center"><a href="{{ route('orders.index') }}">{{ trans('plugins/ecommerce::order.view_all') }}</a></li>
                    @endif
                @endforeach
            </ul>
        </li>
    </ul>
</div>
