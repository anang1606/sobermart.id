@extends(EcommerceHelper::viewPath('customers.master'))
@section('content')
<div class="customer-address py-3">
    <div class="d-flex justify-content-between py-2">
        <h4>{{ SeoHelper::getTitle() }}</h4>
        <a class="text-primary" href="{{ route('customer.ahli-waris.create') }}">{{ __('Add') }}</a>
    </div>
    <div class="row row-cols-md-3 row-cols-sm-2 row-cols-1">
        @forelse ($ahliWariss as $ahliWaris)
            <div class="col">
                <address class="border rounded p-2">
                    <h5>
                        {{ $ahliWaris->name }}
                    </h5>
                    <p>
                        <b>
                            {{ $ahliWaris->nik }}
                        </b>
                    </p>
                    <p>
                        <b>Alamat :</b>
                        <br>
                        {{ $ahliWaris->alamat_ktp->alamat_ktp }}, {{ $ahliWaris->alamat_ktp->kecamatan_ktp }}, {{ $ahliWaris->alamat_ktp->kota_ktp }}, {{ $ahliWaris->alamat_ktp->provinsi_ktp }}
                    </p>
                    <p>
                        <b>Tempat Tinggal :</b>
                        <br>
                        {{ $ahliWaris->alamat_tinggal->alamat_tinggal }}, {{ $ahliWaris->alamat_tinggal->kecamatan_tinggal }}, {{ $ahliWaris->alamat_tinggal->kota_tinggal }}, {{ $ahliWaris->alamat_tinggal->provinsi_tinggal }}
                    </p>
                    <div class="d-flex justify-content-between">
                        <div>
                            <a class="text-primary" href="{{ route('customer.ahli-waris.edit', $ahliWaris->id) }}">{{ __('Edit') }}</a>
                            <a class="text-danger btn-trigger-delete-address ms-2"
                               href="#" data-url="{{ route('customer.ahli-waris.destroy', $ahliWaris->id) }}">{{ __('Remove') }}</a>
                        </div>
                        @if ($ahliWaris->is_primary)
                            <div class="badge bg-primary">{{ __('Ahli Waris Utama') }}</div>
                        @endif
                    </div>
                </address>
            </div>
        @empty
            <div class="col w-100">
                <div class="alert alert-warning" role="alert">
                    <span class="fst-italic">
                        Kamu belum mengatur ahli waris. Silahkan atur terlebih dahulu.
                    </span>
                </div>
            </div>
        @endforelse
    </div>
</div>
<div class="modal fade" id="confirm-delete-modal" tabindex="-1" aria-labelledby="confirm-delete-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirm-delete-modal-label">Confirm delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus data ahli waris ini ??</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary border-0 py-2" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary py-2 mb-0 avatar-save btn-confirm-delete">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection
