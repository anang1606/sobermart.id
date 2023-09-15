@extends(BaseHelper::getAdminMasterLayoutTemplate())
<style>
    .split {
        width: 50%;
        position: fixed;
        z-index: 1;
        top: 0;
        overflow-x: hidden;
        padding-top: 20px;
    }

    .left {
        left: 0;
        background-color: #111;
    }

    .right {
        right: 0;
        background-color: red;
    }

    .table th {
        color: #111 !important;
        font-size: 14px !important;
    }
</style>

@php
    function translateDay($str)
    {
        $searchVal = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $replaceVal = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $res = str_replace($searchVal, $replaceVal, $str);
        return $res;
    }
@endphp
@section('content')
    <div class="content-box row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <div class="element-info-with-icon">
                                <div class="element-info-icon">
                                    <a href="{{ url('laporan/buku-besar') }}" class="text-warning"><i
                                            class="os-icon os-icon-arrow-left-circle"></i></a>
                                </div>
                                <div class="element-info-text">
                                    <h5 class="element-inner-header">
                                        Laporan Ongkos Kirim
                                    </h5>
                                    <div class="element-inner-desc">Data Laporan Ongkos Kirim {{ $rangeFilter }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <div class="table-responsive pb-3">
                                <table id="dataTable"
                                    class="table table-bordered table-striped table-hover display responsive w-100">
                                    <thead>
                                        <tr>
                                            <th colspan="13" style="" class="text-center">
                                                Laporan Ongkos Kirim <br>
                                                {{ $rangeFilter }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 3%;text-align:left;">{{ __('No') }}</th>
                                            <th style="width: 42%;text-align:left;">{{ __('Kurir') }}</th>
                                            <th style="width: 12%;text-align:right;">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalAll = 0;
                                        @endphp
                                        @foreach ($totalPengirimans as $totalPengiriman)
                                        @php
                                            $totalAll += $totalPengiriman->total_price;
                                        @endphp
                                            <tr>
                                                <td class="nowrap">{{ $loop->iteration }}</td>
                                                <td>
                                                    {{ $totalPengiriman->short_provider }}
                                                </td>
                                                <td style="text-align:right">
                                                    {{ format_price($totalPengiriman->total_price) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="2" style="text-align: left">
                                                Total Keseluruhan
                                            </td>
                                            <td style="text-align:right">
                                                {{ format_price($totalAll) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
