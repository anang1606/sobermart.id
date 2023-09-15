@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
    <h5>
        Verifikasi Data Diri
    </h5>
    <form action="" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-content">
            <div class="mb-3">
                <label for="name">{{ __('Full Name') }}:</label>
                <input id="name" type="text" class="form-control"
                    name="name"
                    placeholder="{{ __('Enter Full Name') }}" required minlength="3" maxlength="120">
            </div>
            <div class="mb-3">
                <label for="nik">{{ __('NIK KTP:') }}</label>
                <input id="nik" type="number" min="15"
                    class="form-control" name="nik"
                    placeholder="{{ __('Enter nik KTP') }}" required>
            </div>
            <div class="mb-3">
                <label for="photo_ktp">{{ __('Photo KTP:') }}</label>
                <input id="photo_ktp" type="file"
                    class="form-control" name="photo_ktp" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="photo_ktp_selfi">{{ __('Photo Selfi dengan KTP:') }}</label>
                <input id="photo_ktp_selfi" type="file"
                    class="form-control" name="photo_ktp_selfi" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="photo_ktalamatp_selfi">{{ __('Alamat:') }}</label>
                <textarea name="alamat" id="" rows="7" required class="form-control"></textarea>
                <span class="text-muted">
                    Pastikan alamat di sini dengan benar, Karena di gunakan untuk pengiriman hadiah.
                </span>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <a href="{{ route('customer.gift-target') }}" class="btn btn-warning mr-2">
                    Batal
                </a>
                <button type="submit" class="btn btn-success">Simpan Data</button>
            </div>
        </div>
    </form>
@endsection
