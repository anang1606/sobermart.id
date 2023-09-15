@php
    $menus = collect([
        [
            'key'   => 'marketplace.vendor.dashboard',
            'icon'  => 'icon-home',
            'name'  => __('Dashboard'),
            'order' => 1,
        ],
        [
            'key'    => 'marketplace.vendor.products.index',
            'icon'   => 'icon-database',
            'name'   => __('Products'),
            'routes' => [
                'marketplace.vendor.products.create',
                'marketplace.vendor.products.edit',
            ],
            'order' => 2,
        ],
        [
            'key' => 'marketplace.vendor.chats.index',
            'icon' => 'icon-database',
            'name' => __('Chats'),
            'routes' => ['marketplace.vendor.products.create', 'marketplace.vendor.products.edit'],
            'order' => 2,
        ],
        [
            'key'    => 'marketplace.vendor.orders.index',
            'icon'   => 'icon-bag2',
            'name'   => __('Orders'),
            'routes' => [
                'marketplace.vendor.orders.edit',
            ],
            'order' => 3,
        ],
        [
            'key'    => 'marketplace.vendor.order-returns.index',
            'icon'   => 'icon-bag2',
            'name'   => __('Order Returns'),
            'routes' => [
                'marketplace.vendor.order-returns.edit',
            ],
            'order' => 4,
        ],
        [
            'key'    => 'marketplace.vendor.revenueall.index',
            'icon'   => 'icon-bag-dollar',
            'name'   => __('Revenue'),
            'order' => 5,
        ],
        [
            'key'    => 'marketplace.vendor.etalase.index',
            'icon'   => 'icon-bag-dollar',
            'name'   => __('Etalase'),
            'order' => 6,
        ],
        [
            'key'    => 'marketplace.vendor.discounts.index',
            'icon'   => 'icon-gift',
            'name'   => __('Coupons'),
            'routes' => [
                'marketplace.vendor.discounts.create',
                'marketplace.vendor.discounts.edit',
            ],
            'order' => 7,
        ],

        [
            'key'    => 'marketplace.vendor.withdrawals.index',
            'icon'   => 'icon-bag-dollar',
            'name'   => __('Withdrawals'),
            'routes' => [
                'marketplace.vendor.withdrawals.create',
                'marketplace.vendor.withdrawals.edit',
            ],
            'order' => 8,
        ],

        [
            'key'   => 'marketplace.vendor.settings',
            'icon'  => 'icon-cog',
            'name'  => __('Settings'),
            'order' => 10,
        ],
       /* [
            'key'   => 'customer.overview',
            'icon'  => 'icon-user',
            'name'  => __('Customer dashboard'),
            'order' => 11,
        ],*/
    ]);

    if (EcommerceHelper::isReviewEnabled()) {
        $menus->push([
            'key'   => 'marketplace.vendor.reviews.index',
            'icon'  => 'icon-star',
            'name'  => __('Reviews'),
            'order' => 9,
        ]);
    }

    $currentRouteName = Route::currentRouteName();
@endphp

<ul class="menu">
    @foreach ($menus->sortBy('order') as $item)
        <li>
            @if (in_array('marketplace.vendor.chats.index', Arr::get($item, 'routes', []))))

            @else
                <a @if ($currentRouteName == $item['key'] || in_array($currentRouteName, Arr::get($item, 'routes', []))) class="active" @endif href="{{ route($item['key']) }}">
                    <i class="{{ $item['icon'] }}"></i>{{ $item['name'] }}
                </a>
            @endif
        </li>
    @endforeach
</ul>
