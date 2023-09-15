@extends(BaseHelper::getAdminMasterLayoutTemplate())
<style>
    .table th{
        color: #111 !important;
        font-size: 14px !important;
    }
    .table th,
    .table td {
        border: 1px solid #111 !important;
    }
    table {border: none !important;}
</style>
@section('content')
    <div class="content-box row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <div class="element-info-with-icon">
                                <div class="element-info-text">
                                    <h5 class="element-inner-header">
                                        Laporan Neraca Lajur
                                    </h5>
                                    <div class="element-inner-desc">Laporan Neraca Lajur Periode {{ $rangeFilter }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 mt-5">
                            <div class="table-responsive pb-3">
                                <table id="datatable" class="align-middle table table-striped table-bordered table-hover display responsive w-100" style="border: none;">
                                    <thead>
                                        <tr>
                                            <th colspan="13" style="background:#FFFFFF;" class="text-center">
                                                Neraca Lajur <br>
                                                Periode  {{ $rangeFilter }}
                                            </th>
                                        </tr>
                                        <tr>
                                            <th rowspan="2" style="width: 3%;background:#FFFFFF; text-align:center;">KODE</th>
                                            <th rowspan="2" colspan="2" style="width: 17%;background:#FFFFFF; text-align:center;">NAMA AKUN</th>
                                            <th colspan="2" style="background:#FFFFFF; width:13%; text-align:center;">SALDO AWAL</th>
                                            <th colspan="2" style="background:#FFFFFF; width:13%; text-align:center;">MUTASI</th>
                                            <th colspan="2" style="background:#FFFFFF; width:13%; text-align:center;">SALDO AKHIR</th>
                                            <th colspan="2" style="background:#FFFFFF; width:13%; text-align:center;">LABA RUGI</th>
                                            <th colspan="2" style="background:#FFFFFF; width:13%; text-align:center;">NERACA</th>
                                        </tr>
                                        <tr>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">DEBIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">KREDIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">DEBIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">KREDIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">DEBIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">KREDIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">DEBIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">KREDIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">DEBIT</th>
                                            <th style="width: 8%;background:#FFFFFF; text-align:center;">KREDIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($neraca as $row)
                                        <tr>
                                            <td style="text-center">{{$row->idcoa}}</td>
                                            <td colspan="2"> <h6><b> {{$row->namacoa}} </b></h6></td>
											<td style="text-align:right">{{number_format($row->adebit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->akredit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->bdebit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->bkredit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->cdebit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->ckredit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->ddebit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->dkredit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->edebit,0,',','.')}}</td>
                                            <td style="text-align:right">{{number_format($row->ekredit,0,',','.')}}</td>
                                        </tr>
                                        @endforeach

                                            <tr style="background:#FFFFFF;">
                                                <td colspan="3"> <h6><b> Total : </b></h6></td>
                                                <td style="text-align:right">{{number_format($total1->tadebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->takredit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tbdebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tbkredit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tcdebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tckredit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tddebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tdkredit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tedebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tekredit,0,',','.')}}</td>


                                            </tr>
                                            <tr style="background:#FFFFFF; text-align:right;">
                                                <td colspan="9"> <h6><b> Laba / Rugi : </b></h6></td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                            </tr>
                                            <tr style="background:#FFFFFF; text-align:right;">
                                                <td colspan="11"> <h6><b> Balance : </b></h6></td>
                                                <td style="text-align:right">{{number_format($total1->tedebit,0,',','.')}}</td>
                                                <td style="text-align:right">{{number_format($total1->tekredit,0,',','.')}}</td>
                                            </tr>
                                    </tbody>
                                    {{-- <tfoot>
                                        <tr>
                                            <td colspan="6" class="p-0">
                                                <table class="p-0 w-100">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-right pt-1 pr-2" colspan="8">
                                                                <b>Total</b>
                                                            </td>
                                                            <td style="width:215px{{(0>0)?';background:#039f0e':''}}">
                                                                <div>
                                                                    <small>Jml&nbsp;Pembayaran</small><br>
                                                                    <span>Rp.</span><b class="float-right pr-2">{{ number_format(0) }}</b>
                                                                </div>
                                                            </td>
                                                            <td style="width:215px{{(0>0)?';background:#b57806':''}}">
                                                                <div>
                                                                    <small>Sisa&nbsp;Pembayaran</small><br>
                                                                    <span>Rp.</span><b class="float-right pr-2">{{ number_format(0) }}</b>
                                                                </div>
                                                            </td>
                                                            <td style="width:215px{{(0>0)?';background:#0068d5':''}}">
                                                                <div>
                                                                    <small>Total&nbsp;Pembelian</small><br>
                                                                    <span>Rp.</span><b class="float-right pr-2">{{ number_format(0) }}</b>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tfoot> --}}
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
