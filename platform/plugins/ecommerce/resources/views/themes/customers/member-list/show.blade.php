@extends(EcommerceHelper::viewPath('customers.master'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <iframe style="height: 100vh" src="{{ route('customer.withdrawal.show.form',$withdrawal->id) }}" frameborder="0"></iframe>
        </div>
    </div>
@endsection
