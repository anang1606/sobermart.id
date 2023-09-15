@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="content-box row">
        <div class="col-sm-12 offset-md-1 col-md-10 offset-lg-2 col-lg-8 offset-xxl-3 col-xxl-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>
                                Buku Besar Akun
                            </h5>
                        </div>
                        <div class="col-md-12 mt-3">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tanggal-tab" data-bs-toggle="tab"
                                        data-bs-target="#tanggal-tab-pane" type="button" role="tab"
                                        aria-controls="tanggal-tab-pane" aria-selected="true">
                                        Harian
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="bulan-tab" data-bs-toggle="tab"
                                        data-bs-target="#bulan-tab-pane" type="button" role="tab"
                                        aria-controls="bulan-tab-pane" aria-selected="false">Bulanan</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tahun-tab" data-bs-toggle="tab"
                                        data-bs-target="#tahun-tab-pane" type="button" role="tab"
                                        aria-controls="tahun-tab-pane" aria-selected="false">Tahunan</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="tanggal-tab-pane" role="tabpanel"
                                    aria-labelledby="tanggal-tab" tabindex="0">
                                    <form action="{{ route('buku-besar.view','tanggal') }}" method="post">
                                        @csrf
                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <label for="a1">Tanggal</label>
                                                <input id="a1" type="date" name="date" class="form-control"
                                                    value="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="a2">Akun</label>
                                                <select name="akun" id="a2" class="form-control">
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach ($coa as $row)
                                                        <option value="{{ $row->idcoa }}">{{ $row->idcoa }} |
                                                            {{ $row->namacoa }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button id="btn1" type="submit"
                                                class="btn btn-primary btn-lg">Laporan</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="bulan-tab-pane" role="tabpanel"
                                    aria-labelledby="bulan-tab" tabindex="0">
                                    <form action="{{ route('buku-besar.view','bulan') }}" method="post">
                                        @csrf
                                        <div class="row form-group">
                                            <div class="col-12">
                                                <label for="b1">Bulan</label>
                                                <select name="bulan" id="b1" class="form-control">
                                                    <option value="01" {{ date('m') == '01' ? 'selected' : '' }}>Januari
                                                    </option>
                                                    <option value="02" {{ date('m') == '02' ? 'selected' : '' }}>Februari
                                                    </option>
                                                    <option value="03" {{ date('m') == '03' ? 'selected' : '' }}>Maret
                                                    </option>
                                                    <option value="04" {{ date('m') == '04' ? 'selected' : '' }}>April
                                                    </option>
                                                    <option value="05" {{ date('m') == '05' ? 'selected' : '' }}>Mei
                                                    </option>
                                                    <option value="06" {{ date('m') == '06' ? 'selected' : '' }}>Juni
                                                    </option>
                                                    <option value="07" {{ date('m') == '07' ? 'selected' : '' }}>Juli
                                                    </option>
                                                    <option value="08" {{ date('m') == '08' ? 'selected' : '' }}>Agustus
                                                    </option>
                                                    <option value="09" {{ date('m') == '09' ? 'selected' : '' }}>September
                                                    </option>
                                                    <option value="10" {{ date('m') == '10' ? 'selected' : '' }}>Oktober
                                                    </option>
                                                    <option value="11" {{ date('m') == '11' ? 'selected' : '' }}>November
                                                    </option>
                                                    <option value="12" {{ date('m') == '12' ? 'selected' : '' }}>Desember
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label for="b2">Tahun</label>
                                                <select name="tahun" id="b2" class="form-control">
                                                    @foreach ($tahun as $thn)
                                                        <option value="{{ $thn }}"
                                                            {{ date('Y') == $thn ? 'selected' : '' }}>{{ $thn }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label for="b3">Akun</label>
                                                <select name="akun" id="b3" class="form-control">
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach ($coa as $row)
                                                        <option value="{{ $row->idcoa }}">{{ $row->idcoa }} |
                                                            {{ $row->namacoa }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button id="btn2" type="submit"
                                                class="btn btn-primary btn-lg">Laporan</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane fade" id="tahun-tab-pane" role="tabpanel"
                                    aria-labelledby="tahun-tab" tabindex="0">
                                    <form action="{{ route('buku-besar.view','tahun') }}" method="post">
                                        @csrf
                                        <div class="row form-group">
                                            <div class="col-12">
                                                <label for="c1">Tahun</label>
                                                <select name="tahun" id="c1" class="form-control">
                                                    @foreach ($tahun as $thn)
                                                        <option value="{{ $thn }}"
                                                            {{ date('Y') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 mt-3">
                                                <label for="c2">Akun</label>
                                                <select name="akun" id="c2" class="form-control">
                                                    <option value="">-- Pilih Akun --</option>
                                                    @foreach ($coa as $row)
                                                        <option value="{{ $row->idcoa }}">{{ $row->idcoa }} |
                                                            {{ $row->namacoa }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-right">
                                            <button id="btn3" type="submit"
                                                class="btn btn-primary btn-lg">Laporan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
