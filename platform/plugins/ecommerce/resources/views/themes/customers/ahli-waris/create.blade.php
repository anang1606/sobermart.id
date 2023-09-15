@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    @if (isset($ahliWaris))
    {!! Form::open(['url' =>  route('customer.ahli-waris.update',$ahliWaris->id), 'method' => 'POST']) !!}
    @else
    {!! Form::open(['url' => route('customer.ahli-waris.store','create'), 'method' => 'POST']) !!}
    @endif
    <div class="form-header py-4">
        <h3>{{ SeoHelper::getTitle() }}</h3>
    </div>
    <div class="form-content">
        <div class="mb-3">
            <label for="name">{{ __('Full Name') }}:</label>
            <input id="name" type="text" class="form-control @if ($errors->has('name')) is-invalid @endif"
                name="name" value="{{ isset($ahliWaris) ? $ahliWaris->name : '' }}"
                placeholder="{{ __('Enter Full Name') }}" required minlength="3" maxlength="120">
            @if ($errors->has('name'))
                <div class="invalid-feedback">
                    {{ $errors->first('name') }}
                </div>
            @endif
        </div>
        <div class="mb-3">
            <label for="nik">{{ __('NIK KTP:') }}</label>
            <input id="nik" type="number" min="15"
                class="form-control @if ($errors->has('email')) is-invalid @endif" name="nik"
                value="{{ isset($ahliWaris) ? $ahliWaris->nik : '' }}" placeholder="{{ __('Enter nik') }}" required>
            @if ($errors->has('nik'))
                <div class="invalid-feedback">
                    {{ $errors->first('nik') }}
                </div>
            @endif
        </div>
        <div class="mb-3">
            <label for="phone">{{ __('Phone:') }}</label>
            <input id="phone" type="text" class="form-control @if ($errors->has('email')) is-invalid @endif"
                name="phone" value="{{ isset($ahliWaris) ? $ahliWaris->phone : '' }}"
                placeholder="{{ __('Enter Phone') }}" required>
            @if ($errors->has('phone'))
                <div class="invalid-feedback">
                    {{ $errors->first('phone') }}
                </div>
            @endif
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <b>
                    Alamat Sesuai KTP
                </b>
            </div>
            <div class="col-md-8 mt-2">
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <label>{{ __('Alamat:') }}</label>
                        <input id="alamat_ktp" type="text"
                            class="form-control @if ($errors->has('email')) is-invalid @endif" name="alamat_ktp"
                            value="{{ isset($ahliWaris) ? $ahliWaris->alamat_ktp->alamat_ktp : '' }}" placeholder="Contoh: Jalan Ayub"
                            required>
                        @if ($errors->has('alamat_ktp'))
                            <div class="invalid-feedback">
                                {{ $errors->first('alamat_ktp') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Kota/Kabupaten</label>
                        <input type="text" name="kota_ktp" required placeholder="Contoh: Kota Cirebon"
                            class="form-control" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_ktp->kota_ktp : '' }}">
                        @if ($errors->has('kota_ktp'))
                            <div class="invalid-feedback">
                                {{ $errors->first('kota_ktp') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6 mt-2">
                        <label>Kecamatan</label>
                        <input type="text" name="kecamatan_ktp" required placeholder="Contoh: Lemahwungkuk"
                            class="form-control" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_ktp->kecamatan_ktp : '' }}">
                        @if ($errors->has('kecamatan_ktp'))
                            <div class="invalid-feedback">
                                {{ $errors->first('kecamatan_ktp') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-2">
                <div class="row">
                    <div class="col-md-12 mt-2">
                        <label>Provinsi</label>
                        <input type="text" name="provinsi_ktp" required placeholder="Contoh: Jawa Barat"
                            class="form-control" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_ktp->provinsi_ktp : '' }}">
                        @if ($errors->has('provinsi_ktp'))
                            <div class="invalid-feedback">
                                {{ $errors->first('provinsi_ktp') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="d-flex" style="justify-content: space-between">
                    <b>
                        Alamat Tempat Tinggal
                    </b>
                    <div class="form-check mb-3">
                        <input type="hidden" name="same_ktp" value="0">
                        <input type="checkbox" id="same-ktp"
                        @if (isset($ahliWaris) && $ahliWaris->is_same)
                            checked
                        @endif
                        value="1" name="same_ktp" class="form-check-input same-ktp">
                        <label for="same-ktp">Sama dengan KTP</label>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="address_life" style="display: {{ isset($ahliWaris) ? ($ahliWaris->is_same) ? 'none' : '' : '' }}">
                <div class="row">
                    <div class="col-md-8 mt-2">
                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <label>{{ __('Alamat:') }}</label>
                                <input id="alamat_tinggal" type="text"
                                    class="form-control _tinggal @if ($errors->has('email')) is-invalid @endif"
                                    name="alamat_tinggal" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_tinggal->alamat_tinggal : '' }}"
                                    placeholder="Contoh: Jalan Ayub" required>
                                @if ($errors->has('alamat_tinggal'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('alamat_tinggal') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mt-2">
                                <label>Kota/Kabupaten</label>
                                <input type="text" name="kota_tinggal" required placeholder="Contoh: Kota Cirebon"
                                    class="form-control _tinggal" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_tinggal->kota_tinggal : '' }}">
                                @if ($errors->has('kota_tinggal'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('kota_tinggal') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6 mt-2">
                                <label>Kecamatan</label>
                                <input type="text" name="kecamatan_tinggal" required placeholder="Contoh: Lemahwungkuk"
                                    class="form-control _tinggal" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_tinggal->kecamatan_tinggal : '' }}">
                                @if ($errors->has('kecamatan_tinggal'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('kecamatan_tinggal') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <label>Provinsi</label>
                                <input type="text" name="provinsi_tinggal" required placeholder="Contoh: Jawa Barat"
                                    class="form-control _tinggal" value="{{ isset($ahliWaris) ? $ahliWaris->alamat_tinggal->provinsi_tinggal : '' }}">
                                @if ($errors->has('provinsi_tinggal'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('provinsi_tinggal') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <a href="{{ route('customer.ahli-waris') }}" class="btn btn-warning mr-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-success">Simpan Data</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
