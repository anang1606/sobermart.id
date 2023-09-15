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
                                        Buku Besar {{ $akun }} {{ $coaname }}
                                    </h5>
                                    <div class="element-inner-desc">Data Buku Besar {{ $rangeFilter }}</div>
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
                                                Buku Besar <br>
                                                {{ $rangeFilter }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th colspan="13" style="">
                                                <div style="width: 50%; float:left">
                                                    <div style="width: 20%; float:left">
                                                        Kode Akun <br>
                                                        Nama Akun
                                                    </div>
                                                    <div style="width: 80%; float:right">
                                                        {{ $akun }}<br>
                                                        {{ $coaname }}
                                                    </div>
                                                </div>

                                                <div style="width: 50%; float:right;">
                                                    <div style="width: 80%; float:left;text-align:right">
                                                        Saldo Awal <br>
                                                        Saldo Akhir
                                                    </div>
                                                    <div style="width: 20%; float:right;text-align:right">
                                                        {{ number_format($saldoawal, 0, ',', '.') }}<br>
                                                        {{ number_format($saldoakhir, 0, ',', '.') }}
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 3%;text-align:center;">{{ __('No') }}</th>
                                            <th style="width: 8%;text-align:center;">{{ __('Tanggal') }}</th>
                                            <th style="width: 10%;text-align:center;">{{ __('No Reff') }}</th>
                                            <th style="width: 22%;text-align:center;">{{ __('Keterangan') }}</th>
                                            <th style="width: 12%;text-align:center;">{{ __('Debit') }}</th>
                                            <th style="width: 12%;text-align:center;">{{ __('Kredit') }}</th>
                                            <th style="width: 12%;text-align:center;">{{ __('Saldo') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($acc as $row)
                                            <tr>
                                                <td class="nowrap">{{ $loop->iteration }}</td>
                                                <td class="nowrap">
                                                    {{ translateDay(date_format(date_create($row->tanggal), 'd F Y')) }}
                                                </td>
                                                <td class="nowrap">{{ $row->kode_reff }}</td>
                                                <td class="nowrap">
                                                    {{ $row->keterangan == null ? 'Tidak ada Keterangan' : $row->keterangan }}
                                                </td>
                                                <td class="nowrap" align="right">
                                                    {{ number_format($row->debit, 0, ',', '.') }}&nbsp;&nbsp;&nbsp;</th>
                                                <td class="nowrap" align="right">
                                                    {{ number_format($row->kredit, 0, ',', '.') }}&nbsp;&nbsp;&nbsp;</th>
                                                <td class="nowrap" align="right">
                                                    {{ number_format($row->saldo, 0, ',', '.') }}&nbsp;&nbsp;&nbsp;</td>
                                            </tr>
                                        @endforeach
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
