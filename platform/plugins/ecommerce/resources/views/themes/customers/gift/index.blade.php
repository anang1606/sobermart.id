@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    @if (count($pakets) > 0)
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                @foreach ($pakets as $key => $paket)
                    <button class="nav-link {{ $key === 0 ? 'active' : '' }}" id="nav-{{ $key }}" data-bs-toggle="tab"
                        data-bs-target="#nav-paket-{{ $key }}" type="button" role="tab"
                        aria-controls="nav-paket-{{ $key }}" aria-selected="true">
                        {{ format_price($paket->paket->nominal) }}
                    </button>
                @endforeach
                <button class="nav-link" id="nav-request-gift" data-bs-toggle="tab" data-bs-target="#nav-paket-request-gift"
                    type="button" role="tab" aria-controls="nav-paket-request-gift" aria-selected="true">
                    Permintaan Hadiah
                </button>
            </div>
        </nav>
        <div class="tab-content py-3" id="nav-tabContent">
            @foreach ($pakets as $key => $paket)
                <div class="tab-pane fade {{ $key === 0 ? 'active show' : '' }}" id="nav-paket-{{ $key }}"
                    role="tabpanel" aria-labelledby="nav-{{ $key }}">
                    <div class="container-timeline">
                        <div class="list-box">
                            <ol class="time-list">
                                @php
                                    $progress = 0;
                                    $previousTargetReached = true; // Inisialisasi variabel flag
                                @endphp

                                @foreach ($paket->paket->details as $details)
                                    @php
                                        $progress = ($paket->member_count / $details->target) * 100;
                                    @endphp

                                    @if ($progress > 100)
                                        @php
                                            $progress = 100;
                                        @endphp
                                    @endif

                                    <li class="@if ($paket->member_count >= $details->target && !$details->is_claim) active @endif">
                                        @if ($previousTargetReached)
                                            <div class="indicatior" style="width: {{ $progress }}%"></div>
                                        @endif
                                        <div class="indicatior-target">
                                            {{ $details->target }}
                                        </div>
                                        <div class="time-item">
                                            <div class="box">
                                                <p style="font-size: 16px" class="font-weight-bold mb-1">
                                                    {{ $details->label }}
                                                </p>
                                                <p style="font-size: 13px">
                                                    {{ $details->description }}
                                                </p>

                                                @if ($paket->member_count >= $details->target)
                                                    @if ($details->is_claim)
                                                        <button disabled class="btn btn-claim mt-3">
                                                            Sudah Diklaim
                                                        </button>
                                                    @else
                                                        <a href="{{ route('customer.gift-target.claim', ['state_redux' => base64_encode(base64_encode($paket->paket->id . ',' . $details->target))]) }}"
                                                            class="btn btn-claim mt-3">
                                                            Klaim
                                                        </a>
                                                    @endif
                                                @else
                                                    <button disabled class="btn btn-claim mt-3">
                                                        Klaim
                                                    </button>
                                                    @php
                                                        $previousTargetReached = false;
                                                        $progress = 0;
                                                    @endphp
                                                @endif
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                                {{-- <div id="time-bar" style="width: {{ $progress }}%"></div> --}}
                            </ol>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="tab-pane fade" id="nav-paket-request-gift" role="tabpanel" aria-labelledby="nav-request-gift">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Peminta</th>
                                <th>Nik KTP</th>
                                <th>Paket</th>
                                <th>Hadiah</th>
                                <th>Status</th>
                                <th>Catatan Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($getRequests as $getRequest)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $getRequest->nama }}</td>
                                    <td>{{ $getRequest->nik_ktp }}</td>
                                    <td>{{ $getRequest->paket->name }} ({{ format_price($getRequest->paket->nominal) }})</td>
                                    <td>{{ (isset($getRequest->details->label)) ? $getRequest->details->label : '' }}</td>
                                    <td>{{ $getRequest->status }}</td>
                                    <td>
                                        <textarea style="background: transparent;border:none;resize:none;" readonly rows="3" class="form-control">{!! $getRequest->notes !!}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="text-center"
            style="
            display:flex;
            justify-content:center;
            align-items:center;
            width:80%;
            height:50vh;
            margin: auto;
        ">
            <h5>
                Silahkan Anda lakukan pembayaran untuk paket anda agar bisa melihat hadiah yang menarik.
            </h5>
        </div>
    @endif
@endsection
