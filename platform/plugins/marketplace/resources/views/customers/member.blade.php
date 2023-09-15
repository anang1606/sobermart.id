@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="widget meta-boxes">
        <div class="widget-body">
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
                                            {{ str_replace('Rp', '', format_price($customer->commissions)) }}
                                        </h3>
                                    </div>
                                    <div class="col-md-3 d-flex pl-4 flex-column mt-3">
                                        <h4 style="font-size: 0.95rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                            Total Komisi Referral
                                        </h4>
                                        <h3 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                            <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                            {{ str_replace('Rp', '', format_price($customer->commissions_referral)) }}
                                        </h3>
                                    </div>
                                    <div class="col-md-3 d-flex pl-4 flex-column mt-3">
                                        <h4 style="font-size: 0.95rem;color:#ffffffe3;font-weight: 400;line-height:1.75rem">
                                            Total Komisi Belanja Member
                                        </h4>
                                        <h3 style="font-size: 1.25rem;color:#ffffffe3;font-weight: 600;letter-spacing:4px">
                                            <span style="color: rgba(255, 255, 255, 0.583);letter-spacing:1px">Rp.</span>
                                            {{ str_replace('Rp', '', format_price($customer->commissions_shopping)) }}
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
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (count($members) > 0)
                        <div class="col-md-12 mt-4">
                            <nav>
                                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                    @foreach ($members as $key => $member)
                                        <button class="nav-link {{ $key === 0 ? 'active' : '' }}"
                                            id="nav-{{ $key }}" data-bs-toggle="tab"
                                            data-bs-target="#nav-paket-{{ $key }}" type="button" role="tab"
                                            aria-controls="nav-paket-{{ $key }}" aria-selected="true">
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
                                                        <td style="font-size: 16.4px" width="110px">
                                                            Leader
                                                        </td>
                                                        <td style="font-size: 16.4px" width="16px">
                                                            :
                                                        </td>
                                                        <td style="font-size: 16.4px">
                                                            <b>{{ $is_member->customer->name }}</b>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-size: 16.4px" width="110px">
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
                            <table class="table table-striped">
                                <tbody>
                                    <tr>
                                        <td style="text-align: center">
                                            No data
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @else
                <div class="col-md-12 mt-5" id="details-member">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td style="text-align: center">
                                    No data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@stop
