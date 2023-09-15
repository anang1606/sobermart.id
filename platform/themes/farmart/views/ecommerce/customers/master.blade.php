@php
    Theme::layout('full-width');
    Theme::asset()
        ->container('footer')
        ->remove('ecommerce-utilities-js');
@endphp
{!! Theme::partial('page-header', ['size' => 'xxxl']) !!}

@php
    $menus = [
        'customer.overview' => [
            'label' => __('Overview'),
        ],
        'customer.edit-account' => [
            'label' => __('Profile'),
        ],
        'customer.voucher-wallet' => [
            'label' => __('Voucher Saya'),
        ],
        'customer.gift-target' => [
            'label' => __('Gift'),
        ],
        'customer.ahli-waris' => [
            'label' => __('Ahli Waris'),
        ],
        'customer.payments' => [
            'label' => __('Waiting Payments'),
            'routes' => ['customer.payments'],
        ],
        'customer.orders' => [
            'label' => __('Orders'),
            'routes' => ['customer.orders.view'],
        ],
        'customer.history' => [
            'label' => __('History'),
            'routes' => ['customer.orders.history'],
        ],
        'customer.product-reviews' => [
            'label' => __('Product Reviews'),
            'routes' => ['customer.product-reviews'],
        ],
        'customer.downloads' => [
            'label' => __('Downloads'),
        ],
        'customer.order_returns' => [
            'label' => __('Order Return Requests'),
            'routes' => ['customer.order_returns', 'customer.order_returns.detail'],
        ],
        'customer.notification' => [
            'label' => __('Notifications'),
            'routes' => ['customer.notification'],
        ],
        'customer.address' => [
            'label' => __('Addresses'),
            'routes' => ['customer.address.create', 'customer.address.edit'],
        ],
        'customer.change-password' => [
            'label' => __('Change password'),
        ],
        'customer.member-list' => [
            'label' => __('Member'),
        ],
        'customer.bantuan' => [
            'label' => __('Bantuan'),
        ],
    ];

    $routeName = Route::currentRouteName();

    if (!EcommerceHelper::isEnabledSupportDigitalProducts()) {
        unset($menus['customer.downloads']);
    }

    if (!EcommerceHelper::isReviewEnabled()) {
        unset($menus['customer.product-reviews']);
    }
@endphp
<div class="container-xxxl">
    <div class="row my-4">
        <div class="col-md-3">
            <ul class="nav flex-column dashboard-navigation">
                @foreach ($menus as $key => $item)
                    <li class="nav-item">
                        <a class="nav-link
                            @if ($routeName == $key || in_array($routeName, Arr::get($item, 'routes', []))) active @endif"
                            aria-current="@if ($routeName == $key || in_array($routeName, Arr::get($item, 'routes', []))) true @else false @endif"
                            href="{{ route($key) }}" style="display: flex;justify-content: space-between;">
                            {{ Arr::get($item, 'label') }}
                            @if (in_array('customer.payments', Arr::get($item, 'routes', [])) && countWaitingPayment() > 0)
                                <span class="badge badge-primary">{{ countWaitingPayment() }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
                @if (is_plugin_active('marketplace'))
                    @if (auth('customer')->user()->is_vendor)
                        <li class="nav-item">
                            <a class="nav-link" aria-current="false"
                                href="{{ route('marketplace.vendor.dashboard') }}">{{ __('Vendor dashboard') }}</a>
                        </li>
                    {{-- @else
                        <li class="nav-item @if ($routeName == 'marketplace.vendor.become-vendor') active @endif">
                            <a class="nav-link" aria-current="@if ($routeName == 'marketplace.vendor.become-vendor') active @endif"
                                href="{{ route('marketplace.vendor.become-vendor') }}">{{ __('Become a vendor') }}</a>
                        </li> --}}
                    @endif
                @endif
                <li class="nav-item">
                    <a class="nav-link" aria-current="false"
                        href="{{ route('customer.logout') }}">{{ __('Logout') }}</a>
                </li>
            </ul>
        </div>
        <div class="col-md-9">
            <div class="customer-dashboard-container">
                <div class="customer-dashboard-content">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>
