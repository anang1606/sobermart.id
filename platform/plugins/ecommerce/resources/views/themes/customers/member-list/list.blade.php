@extends(EcommerceHelper::viewPath('customers.master'))

@section('content')
    @if ($is_member)
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div style="background: #7054DB" class="card-body">
                        <div class="row">
                            <div class="col-md-12 d-flex pl-4 flex-column">
                                <h4 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                    Total Saldo
                                </h4>
                                <h3 style="font-size: 2.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                    <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                    {{ str_replace('Rp', '', format_price(auth('customer')->user()->commissions)) }}
                                </h3>
                            </div>
                            <div class="col-md-3 d-flex pl-4 flex-column mt-3">
                                <h4 style="font-size: 0.95rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                    Total Komisi Referral
                                </h4>
                                <h3 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                    <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                    {{ str_replace('Rp', '', format_price(auth('customer')->user()->commissions_referral)) }}
                                </h3>
                            </div>
                            <div class="col-md-3 d-flex pl-4 flex-column mt-3">
                                <h4 style="font-size: 0.95rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                    Total Komisi Belanja Member
                                </h4>
                                <h3 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                    <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                    {{ str_replace('Rp', '', format_price(auth('customer')->user()->commissions_shopping)) }}
                                </h3>
                            </div>
                            <div class="col-md-3 d-flex pl-4 flex-column mt-3">
                                <h4 style="font-size: 0.95rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                    Total Withdrawal
                                </h4>
                                <h3 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                    <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                    {{ str_replace('Rp', '', format_price($total_witdrawal)) }}
                                </h3>
                            </div>
                            <div class="col-md-12 mt-4">
                                <button class="btn btn-light btn-lg withdrawal-btn">
                                    Withdrawals
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-4" id="withdrawal-request">
                <form action="" method="POST">
                    <div class="row">
                        <div class="col-md-12">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="amount" class="control-label required" aria-required="true">
                                    Amount (balance: {{ format_price(auth('customer')->user()->commissions) }})
                                </label>
                                <input class="form-control" min="50000" placeholder="Amount you want to withdrawal"
                                    data-counter="120" max="{{ auth('customer')->user()->commissions }}" v-pre=""
                                    name="amount" type="number" id="amount">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label for="description" class="control-label">Description</label>
                                <textarea class="form-control" rows="3" placeholder="Short description" data-counter="200" v-pre=""
                                    name="description" cols="50" id="description"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bank_info_name">{{ __('Bank Name') }}:</label>
                                <input id="bank_info_name" type="text" class="form-control" name="bank_info[name]"
                                    placeholder="{{ __('Bank Name') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="bank_info_number">{{ __('Account Number') }}:</label>
                                <input id="bank_info_number" type="text" class="form-control"
                                    placeholder="{{ __('Bank number') }}" name="bank_info[number]">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bank_info_full_name">{{ __('Account Holder Name') }}:</label>
                                <input id="bank_info_full_name" type="text" class="form-control"
                                    placeholder="{{ __('Full name') }}" name="bank_info[full_name]">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">
                                Submit
                            </button>
                            <button type="button" class="btn btn-danger withdrawal-data">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12 mt-5" id="withdrawal-data">
                <div class="d-flex mb-3" style="align-items: flex-end;justify-content: space-between;">
                    <h5 style="margin: 0">
                        Withdrawals
                    </h5>
                    <button type="button" class="btn btn-info withdrawal-request" style="color: white">
                        Request
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20px">No.</th>
                                <th>{{ trans('plugins/marketplace::withdrawal.amount') }}</th>
                                <th>{{ trans('core/base::tables.status') }}</th>
                                <th>{{ trans('core/base::tables.created_at') }}</th>
                                <th>{{ trans('core/base::tables.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($get_wihdrawal) > 0)
                                @php
                                    $no = 1;
                                @endphp
                                @foreach ($get_wihdrawal as $withdrawal)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>
                                            {{ format_price($withdrawal->amount) }}
                                        </td>
                                        <td>
                                            {!! BaseHelper::clean($withdrawal->status->toHtml()) !!}
                                        </td>
                                        <td>
                                            {{ BaseHelper::formatDate($withdrawal->created_at) }}
                                        </td>
                                        <td>
                                            @if ($withdrawal->status == 'pending')
                                                <a href="?cancel={{ base64_encode($withdrawal) }}" class="btn btn-danger">
                                                    Cancel
                                                </a>
                                            @endif
                                            @if ($withdrawal->status == 'completed')
                                                <a href="{{ route('customer.withdrawal.show', $withdrawal->id) }}"
                                                    class="btn btn-icon btn-sm btn-success">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="5">
                                        No data available
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            @if (count($members) > 0)
                <div class="col-md-12 mt-4" id="details-member">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            @foreach ($members as $key => $member)
                                <button class="nav-link {{ $key === 0 ? 'active' : '' }}" id="nav-{{ $key }}"
                                    data-bs-toggle="tab" data-bs-target="#nav-paket-{{ $key }}" type="button"
                                    role="tab" aria-controls="nav-paket-{{ $key }}" aria-selected="true">
                                    {{ format_price($member['nominal']) }}
                                </button>
                            @endforeach
                        </div>
                    </nav>
                    <div class="tab-content py-3" id="nav-tabContent">
                        @foreach ($members as $key => $member)
                            @php
                                $commissions = 0;
                            @endphp
                            <div class="tab-pane fade {{ $key === 0 ? 'active show' : '' }}"
                                id="nav-paket-{{ $key }}" role="tabpanel"
                                aria-labelledby="nav-{{ $key }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td style="font-size: 16.4px">
                                                        Leader
                                                    </td>
                                                    <td style="font-size: 16.4px">
                                                        :
                                                    </td>
                                                    <td style="font-size: 16.4px">
                                                        <b>{{ $is_member->customer->name }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size: 16.4px">
                                                        Kode Referral
                                                    </td>
                                                    <td style="font-size: 16.4px">
                                                        :
                                                    </td>
                                                    <td style="font-size: 16.4px">
                                                        <b>{{ $is_member->code }}</b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="table-responsive mt-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th width="20px">No.</th>
                                                <th>Nama Member</th>
                                                <th>Total Belanja</th>
                                                <th>Commission</th>
                                                <th>Status</th>
                                                <th>Tanggal Bergabung</th>
                                                <th>Masa Berlaku</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($member['members']) > 0)
                                                @php
                                                    $no = 1;
                                                @endphp
                                                @foreach ($member['members'] as $member)
                                                    @php
                                                        $commissions += $member['commission'];
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $member['name'] }}</td>
                                                        <td>{{ format_price($member['total_belanja']) }}</td>
                                                        <td>{{ format_price($member['commission']) }}</td>
                                                        <td>{{ $member['status'] }}</td>
                                                        <td>{{ date_format(new DateTime($member['join_date']), 'j M Y') }}</td>
                                                        <td>{{ date_format(new DateTime($member['expired_date']), 'j M Y') }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="3">Total Estimasi Commission : </td>
                                                    <td colspan="4">{{ format_price($commissions) }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center">No data member avalable </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="col-md-12 mt-5" id="details-member">
                    <div class="css-18s3rjz">
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="logo"
                            class="css-1rahovo" />
                        <h5 class="css-w0kc19">
                            Member Area
                        </h5>
                        <p class="css-1hnyl5u">
                            Ajak member dan dapatkan komisi tiap member join dengan kode referal kan kamu dan dapatkan
                            komisi tiap member kamu belanja.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="css-18s3rjz">
            <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="logo" class="css-1rahovo" />
            <h5 class="css-w0kc19">
                Member Area
            </h5>
            <p class="css-1hnyl5u">Ingin Merasakan Keistimewaan? Segera Bergabung dan Nikmati Promo Kami!.</p>
            <a href="{{ route('customer.member') }}" class="btn css-1k9qobw-unf-btn">
                Gabung Sekarang
            </a>
        </div>
    @endif
@endsection
