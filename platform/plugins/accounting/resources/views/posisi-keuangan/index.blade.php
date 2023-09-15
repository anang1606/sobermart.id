@extends(BaseHelper::getAdminMasterLayoutTemplate())
<style>
    .page-content {
        padding: 0 !important;
    }
    .breadcrumb{
        margin: 20px !important;
    }
</style>
@section('content')
    <div style="width:100%;background-color: #606060;min-height: 100vh;position: relative;">
        <iframe src="{{ route('posisi-keuangan.data') }}"
            style="width: 100%; height: 100vh; padding: 0; margin: 0;border:none;"></iframe>
    </div>
@endsection
