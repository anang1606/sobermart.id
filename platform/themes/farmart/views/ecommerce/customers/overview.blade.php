@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    @php
        $customer = auth('customer')->user();
    @endphp
    <p>{!! BaseHelper::clean(
        __('Hello <strong>:name</strong> (not <strong>:name</strong>? <a class="text-primary" href=":link">Log out</a>)', [
            'name' => $customer->name,
            'link' => route('customer.logout'),
        ]),
    ) !!}</p>

    <p>{!! BaseHelper::clean(
        __(
            'From your account dashboard you can view your <a class="text-primary" href=":order">recent orders</a>, manage your <a class="text-primary" href=":addresses">shipping and billing addresses</a>, and <a class="text-primary" href=":edit_account">edit your password and account details</a>.',
            [
                'order' => route('customer.orders'),
                'addresses' => route('customer.address'),
                'edit_account' => route('customer.edit-account'),
            ],
        ),
    ) !!}</p>

    @if (!$customer->orders()->count())
        <div class="alert alert-info d-flex align-items-center justify-content-between border-0" role="alert">
            <div>
                <span class="svg-icon">
                    <svg>
                        <use href="#svg-icon-check-circle" xlink:href="#svg-icon-check-circle"></use>
                    </svg>
                </span>
                <span class="ms-2">{{ __('No order has been made yet') }}.</span>
            </div>
            <a class="box-shadow" href="{{ route('public.products') }}">{{ __('Browse products') }}</a>
        </div>
    @endif

    <p>{{ __('The following addresses will be used on the checkout page by default') }}.</p>
    <div class="customer-address py-3">
        <div class="d-flex justify-content-between py-2">
            <h4>{{ __('Addresses') }}</h4>
            <a class="add-address text-primary" href="{{ route('customer.address.create') }}">{{ __('Add') }}</a>
        </div>
        @include(Theme::getThemeNamespace() . '::views.ecommerce.customers.address.items', [
            'addresses' => $customer->addresses()->limit(3)->get(),
        ])
    </div>
    <div class="customer-address py-3">
        @if (count($member_pakets) > 0)
            <div class="d-flex justify-content-between py-2">
                <h4>{{ __('Referral') }}</h4>
                 <a class="add-address text-primary" href="{{ route('customer.member') }}">{{ __('Add') }}</a>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>ID</td>
                        <td>Referral Code</td>
                        <td>Default</td>
                        <td>Paket</td>
                        <td>Tanggal Bergabung</td>
                        <td>Masa Berlaku</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($member_pakets as $key => $member_paket)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                {{ $member_paket->uuid }}
                            </td>
                            <td>
                                {{ $member_paket->code }}
                                <img
                                onclick="copyToClipboard('{{ $member_paket->code }}')"
                                style="width:18px;margin-left:6px;cursor:pointer"
                                src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAAA30lEQVR4nO3XSw7CMAyE4f8SSNyULWdkw3GGDUFIoEIS2+ExI3UXqf1q5wWxUcDTsmNhIiFnYP8LEK3EREO0CpMB0QpMFkTVmEMiRN+GaVEvJmPtPyZCBJye7TORkPu/NVqZllfjHioTCVEApkW9mGjILKbl3fG3d2VAZjDDyYKMYj4SMttm6vmGbEgVhgpIBYYqSDaGSkgmhmpIFoZIyGgMIbgiVS2p7NYy5BpXBLcWniPyqoWXX7yP4J0dH1F81sKHRnyMx/cRfLHSP191QyJDcEXk1tqI5wieI4TPkQsNRokspwnZAwAAAABJRU5ErkJggg==">
                                </td>
                            <td>
                                @if ($member_paket->is_active)
                                    <button disabled class="btn btn-danger">
                                        Active
                                    </button>
                                @else
                                    <a href="{{ route('customer.paket.active',$member_paket->id) }}" class="btn btn-success">
                                        Active
                                    </a>
                                @endif
                            </td>
                            <td>{{ $member_paket->paket->name }}</td>
                            <td>{{ date_format(new DateTime($member_paket->created_at), 'j M Y, H:i') }}</td>
                            <td>{{ date_format(new DateTime($member_paket->expire_date), 'j M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="row row-cols-md-3 row-cols-sm-2 row-cols-1">
            </div>
        @endif
    </div>
@endsection
